<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SecurityContextService
{
    /**
     * Get current user's security context with minimal database queries.
     */
    public static function getSecurityContext(): ?array
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        $sessionKey = 'user_security_context_' . $user->id;
        
        // Try session first, then cache, then database
        $context = Session::get($sessionKey);
        
        if (!$context) {
            $context = Cache::get("security_context_{$user->id}");
        }
        
        if (!$context || self::isContextExpired($context)) {
            $context = self::buildFreshContext($user);
            Session::put($sessionKey, $context);
            Cache::put("security_context_{$user->id}", $context, now()->addMinutes(30));
        }

        return $context;
    }

    /**
     * Check if user has permission without database query.
     */
    public static function hasPermission(string $permission): bool
    {
        $context = self::getSecurityContext();
        
        if (!$context) {
            return false;
        }

        $permissions = $context['permissions'] ?? [];
        
        // Check exact permission
        if (in_array($permission, $permissions)) {
            return true;
        }

        // Check wildcard permissions
        foreach ($permissions as $userPermission) {
            if (str_ends_with($userPermission, '.*')) {
                $prefix = str_replace('.*', '', $userPermission);
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user can access project without database query.
     */
    public static function canAccessProject(int $projectId): bool
    {
        $context = self::getSecurityContext();
        
        if (!$context) {
            return false;
        }

        // Super admins and project managers can access all projects
        if (in_array($context['role'], ['super_admin', 'project_manager'])) {
            return true;
        }

        $accessibleProjects = $context['accessible_projects'] ?? [];
        return in_array($projectId, $accessibleProjects);
    }

    /**
     * Get user role without database query.
     */
    public static function getUserRole(): ?string
    {
        $context = self::getSecurityContext();
        return $context['role'] ?? null;
    }

    /**
     * Get user department without database query.
     */
    public static function getUserDepartment(): ?string
    {
        $context = self::getSecurityContext();
        return $context['department'] ?? null;
    }

    /**
     * Check if current session requires enhanced security.
     */
    public static function requiresEnhancedSecurity(): bool
    {
        $context = self::getSecurityContext();
        
        if (!$context) {
            return true;
        }

        return ($context['security_level'] ?? 'normal') !== 'normal';
    }

    /**
     * Get user's accessible project IDs for efficient querying.
     */
    public static function getAccessibleProjectIds(): array
    {
        $context = self::getSecurityContext();
        
        if (!$context) {
            return [];
        }

        return $context['accessible_projects'] ?? [];
    }

    /**
     * Update security context with new data.
     */
    public static function updateContext(array $updates): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $context = self::getSecurityContext();
        
        if ($context) {
            $context = array_merge($context, $updates);
            $context['updated_at'] = now()->timestamp;
            
            $sessionKey = 'user_security_context_' . $user->id;
            Session::put($sessionKey, $context);
            Cache::put("security_context_{$user->id}", $context, now()->addMinutes(30));
        }
    }

    /**
     * Clear security context (logout).
     */
    public static function clearContext(): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        Session::forget('user_security_context_' . $user->id);
        Cache::forget("security_context_{$user->id}");
        Cache::forget("user_projects_{$user->id}");
    }

    /**
     * Build fresh security context from database.
     */
    private static function buildFreshContext(User $user): array
    {
        return [
            'user_id' => $user->id,
            'user_hash' => md5($user->email . $user->password . $user->updated_at),
            'role' => $user->role,
            'permissions' => self::calculatePermissions($user->role),
            'department' => $user->department,
            'is_active' => $user->is_active,
            'session_ip' => request()->ip(),
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(30)->timestamp,
            'security_level' => self::calculateSecurityLevel($user),
            'accessible_projects' => self::getProjectIds($user),
        ];
    }

    /**
     * Check if context is expired.
     */
    private static function isContextExpired(array $context): bool
    {
        return ($context['expires_at'] ?? 0) < now()->timestamp;
    }

    /**
     * Calculate permissions for role.
     */
    private static function calculatePermissions(string $role): array
    {
        $rolePermissions = [
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

        return $rolePermissions[$role] ?? ['projects.view', 'tasks.view'];
    }

    /**
     * Calculate security level.
     */
    private static function calculateSecurityLevel(User $user): string
    {
        $loginAttempts = Cache::get("login_attempts_{$user->email}", 0);
        $lastLogin = $user->last_login_at;
        
        if ($loginAttempts > 3) {
            return 'high';
        }

        if ($lastLogin && $lastLogin->diffInHours() > 24) {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Get project IDs for user.
     */
    private static function getProjectIds(User $user): array
    {
        $cacheKey = "user_projects_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user) {
            if (in_array($user->role, ['super_admin', 'project_manager'])) {
                return \App\Models\Project::pluck('id')->toArray();
            }
            
            return \App\Models\Project::whereHas('tasks', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->pluck('id')->toArray();
        });
    }
}
