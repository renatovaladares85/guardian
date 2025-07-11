<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CheckSystemSetup
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se é uma rota de configuração ou API
        if ($this->isSetupRoute($request) || $this->isApiRoute($request)) {
            return $next($request);
        }

        // Verificar se o sistema foi configurado
        if (!$this->isSystemConfigured()) {
            return redirect()->route('setup.wizard');
        }

        return $next($request);
    }

    private function isSetupRoute(Request $request): bool
    {
        $setupRoutes = [
            'setup.wizard',
            'setup.process',
            'setup.complete'
        ];

        return in_array($request->route()?->getName(), $setupRoutes);
    }

    private function isApiRoute(Request $request): bool
    {
        return $request->is('api/*');
    }

    private function isSystemConfigured(): bool
    {
        try {
            // Verificar se existe flag de configuração
            if (File::exists(storage_path('app/guardian_setup_complete.flag'))) {
                return true;
            }

            // Verificar se existe pelo menos um usuário
            return DB::table('users')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
