<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class SessionBasedSecurity
{
    /**
     * Handle an incoming request with session-based security optimization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for login and register routes
        if ($this->shouldSkipAuthentication($request)) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $sessionKey = 'user_security_context_' . $user->id;
        
        // Try to get security context from session first
        $securityContext = Session::get($sessionKey);
        
        if (!$securityContext || $this->shouldRefreshSecurityContext($securityContext)) {
            // Refresh security context from database and cache
            $securityContext = $this->buildSecurityContext($user);
            Session::put($sessionKey, $securityContext);
            
            // Also cache for faster subsequent requests
            Cache::put("security_context_{$user->id}", $securityContext, now()->addMinutes(30));
        }

        // Validate security context
        if (!$this->validateSecurityContext($securityContext, $request)) {
            $this->invalidateUserSessions($user);
            return redirect()->route('login')->with('error', 'Sessão inválida por motivos de segurança.');
        }

        // Update last activity
        $this->updateLastActivity($user, $securityContext);

        // Share security context with the request
        $request->merge(['security_context' => $securityContext]);

        return $next($request);
    }

    /**
     * Check if authentication should be skipped for this route.
     */
    private function shouldSkipAuthentication(Request $request): bool
    {
        $skipRoutes = [
            'login',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'api/*',
        ];

        foreach ($skipRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build comprehensive security context for the user.
     */
    private function buildSecurityContext(User $user): array
    {
        return [
            'user_id' => $user->id,
            'user_hash' => md5($user->email . $user->password . $user->updated_at),
            'role' => $user->role,
            'permissions' => $this->getUserPermissions($user),
            'department' => $user->department,
            'is_active' => $user->is_active,
            'last_login' => $user->last_login_at,
            'session_ip' => request()->ip(),
            'session_agent' => request()->userAgent(),
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(config('session.lifetime', 120))->timestamp,
            'security_level' => $this->calculateSecurityLevel($user),
            'accessible_projects' => $this->getUserProjectIds($user),
            'login_attempts' => Cache::get("login_attempts_{$user->email}", 0),
        ];
    }

    /**
     * Get user permissions based on role.
     */
    private function getUserPermissions(User $user): array
    {
        $permissions = [
            'super_admin' => [
                'users.*', 'projects.*', 'tasks.*', 'settings.*', 
                'audit.*', 'reports.*', 'system.*'
            ],
            'project_manager' => [
                'projects.create', 'projects.edit', 'projects.view', 'projects.delete',
                'tasks.*', 'users.view', 'reports.view', 'reports.create'
            ],
            'team_lead' => [
                'projects.view', 'tasks.*', 'users.view', 'reports.view'
            ],
            'senior_developer' => [
                'projects.view', 'tasks.create', 'tasks.edit', 'tasks.view',
                'tasks.assign', 'reports.view'
            ],
            'developer' => [
                'projects.view', 'tasks.create', 'tasks.edit', 'tasks.view'
            ],
            'junior_developer' => [
                'projects.view', 'tasks.view', 'tasks.edit:own'
            ],
            'qa_engineer' => [
                'projects.view', 'tasks.view', 'tasks.test', 'reports.create'
            ],
            'designer' => [
                'projects.view', 'tasks.view', 'tasks.edit:design'
            ],
            'business_analyst' => [
                'projects.view', 'tasks.view', 'requirements.*', 'reports.*'
            ],
            'devops_engineer' => [
                'projects.view', 'tasks.view', 'deployment.*', 'infrastructure.*'
            ],
        ];

        return $permissions[$user->role] ?? ['projects.view', 'tasks.view'];
    }

    /**
     * Get project IDs accessible by the user.
     */
    private function getUserProjectIds(User $user): array
    {
        // Cache project access for performance
        $cacheKey = "user_projects_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user) {
            if (in_array($user->role, ['super_admin', 'project_manager'])) {
                return \App\Models\Project::pluck('id')->toArray();
            }
            
            // Get projects where user is assigned to tasks or is project member
            return \App\Models\Project::whereHas('tasks', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->orWhereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->pluck('id')->toArray();
        });
    }

    /**
     * Calculate security level based on user context.
     */
    private function calculateSecurityLevel(User $user): string
    {
        $loginAttempts = Cache::get("login_attempts_{$user->email}", 0);
        $lastLogin = $user->last_login_at;
        $isNewDevice = !Cache::has("trusted_device_{$user->id}_" . md5(request()->userAgent()));

        if ($loginAttempts > 3 || $isNewDevice) {
            return 'high';
        }

        if ($lastLogin && $lastLogin->diffInHours() > 24) {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Check if security context should be refreshed.
     */
    private function shouldRefreshSecurityContext(array $securityContext): bool
    {
        // Refresh if expired
        if ($securityContext['expires_at'] < now()->timestamp) {
            return true;
        }

        // Refresh if IP changed
        if ($securityContext['session_ip'] !== request()->ip()) {
            return true;
        }

        // Refresh if user agent changed significantly
        $currentAgent = request()->userAgent();
        if (md5($securityContext['session_agent']) !== md5($currentAgent)) {
            return true;
        }

        // Refresh every 30 minutes for security
        if ($securityContext['created_at'] < now()->subMinutes(30)->timestamp) {
            return true;
        }

        return false;
    }

    /**
     * Validate security context integrity.
     */
    private function validateSecurityContext(array $securityContext, Request $request): bool
    {
        $user = Auth::user();

        // Validate user hash for data integrity
        $currentHash = md5($user->email . $user->password . $user->updated_at);
        if ($securityContext['user_hash'] !== $currentHash) {
            return false;
        }

        // Validate user is still active
        if (!$securityContext['is_active'] || !$user->is_active) {
            return false;
        }

        // Validate role hasn't changed
        if ($securityContext['role'] !== $user->role) {
            return false;
        }

        // Check for suspicious activity
        if ($this->detectSuspiciousActivity($securityContext, $request)) {
            return false;
        }

        return true;
    }

    /**
     * Detect suspicious activity patterns.
     */
    private function detectSuspiciousActivity(array $securityContext, Request $request): bool
    {
        $userId = $securityContext['user_id'];
        
        // Check for too many requests in short time
        $requestCount = Cache::get("request_count_{$userId}", 0);
        if ($requestCount > 100) { // 100 requests per minute
            Cache::put("suspicious_activity_{$userId}", true, now()->addMinutes(30));
            return true;
        }

        // Increment request counter
        Cache::put("request_count_{$userId}", $requestCount + 1, now()->addMinute());

        // Check if user is flagged for suspicious activity
        if (Cache::has("suspicious_activity_{$userId}")) {
            return true;
        }

        // Check for concurrent sessions from different IPs
        $activeSessions = Cache::get("active_sessions_{$userId}", []);
        if (count($activeSessions) > 3) {
            return true;
        }

        return false;
    }

    /**
     * Update last activity timestamp.
     */
    private function updateLastActivity(User $user, array &$securityContext): void
    {
        $sessionKey = 'user_security_context_' . $user->id;
        $securityContext['last_activity'] = now()->timestamp;
        
        Session::put($sessionKey, $securityContext);
        
        // Update active sessions tracking
        $activeSessions = Cache::get("active_sessions_{$user->id}", []);
        $sessionId = md5(request()->ip() . request()->userAgent());
        $activeSessions[$sessionId] = now()->timestamp;
        
        // Clean old sessions
        $activeSessions = array_filter($activeSessions, function ($timestamp) {
            return $timestamp > now()->subHours(2)->timestamp;
        });
        
        Cache::put("active_sessions_{$user->id}", $activeSessions, now()->addHours(4));
    }

    /**
     * Invalidate all user sessions for security.
     */
    private function invalidateUserSessions(User $user): void
    {
        // Clear session data
        Session::forget('user_security_context_' . $user->id);
        
        // Clear cache data
        Cache::forget("security_context_{$user->id}");
        Cache::forget("user_projects_{$user->id}");
        Cache::forget("active_sessions_{$user->id}");
        
        // Mark for security review
        Cache::put("security_violation_{$user->id}", [
            'timestamp' => now()->timestamp,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reason' => 'Invalid security context'
        ], now()->addHours(24));
        
        Auth::logout();
    }
}
