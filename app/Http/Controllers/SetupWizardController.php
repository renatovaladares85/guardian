<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SetupWizardController extends Controller
{
    public function index()
    {
        // Se já estiver configurado, redirecionar
        if ($this->isSystemConfigured()) {
            return redirect('/')->with('info', 'Sistema já foi configurado.');
        }

        return view('setup.wizard');
    }

    public function process(Request $request)
    {
        $request->validate([
            'setup_mode' => 'required|in:test,production,custom',
            'admin_name' => 'required_if:setup_mode,production,custom|string|max:255',
            'admin_email' => 'required_if:setup_mode,production,custom|email|max:255',
            'admin_password' => 'required_if:setup_mode,production,custom|min:8',
            'include_demo_data' => 'boolean',
            'enable_security' => 'boolean',
            'enable_audit' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            switch ($request->setup_mode) {
                case 'test':
                    $this->setupTestMode();
                    break;
                    
                case 'production':
                    $this->setupProductionMode($request);
                    break;
                    
                case 'custom':
                    $this->setupCustomMode($request);
                    break;
            }

            $this->markSetupComplete($request->setup_mode);
            
            DB::commit();

            return redirect()->route('setup.complete')->with([
                'setup_mode' => $request->setup_mode,
                'success' => 'Sistema configurado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'setup_error' => 'Erro durante a configuração: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function complete()
    {
        $setupMode = session('setup_mode', 'unknown');
        return view('setup.complete', compact('setupMode'));
    }

    private function setupTestMode()
    {
        // Executar migrations
        Artisan::call('migrate:fresh', ['--force' => true]);
        
        // Executar seeders
        Artisan::call('db:seed', ['--force' => true]);

        // Configurar modo teste
        $this->createConfigFile([
            'mode' => 'test',
            'features' => [
                'demo_data' => true,
                'security_relaxed' => true,
                'audit_minimal' => true,
            ]
        ]);
    }

    private function setupProductionMode(Request $request)
    {
        // Executar migrations
        Artisan::call('migrate', ['--force' => true]);

        // Criar usuário administrador
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Configurar modo produção
        $this->createConfigFile([
            'mode' => 'production',
            'features' => [
                'demo_data' => false,
                'security_enhanced' => true,
                'audit_complete' => true,
            ]
        ]);

        // Otimizações
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
    }

    private function setupCustomMode(Request $request)
    {
        if ($request->boolean('include_demo_data')) {
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } else {
            Artisan::call('migrate', ['--force' => true]);
            
            // Criar usuário administrador
            User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Configurações personalizadas
        $this->createConfigFile([
            'mode' => 'custom',
            'features' => [
                'demo_data' => $request->boolean('include_demo_data'),
                'security_enhanced' => $request->boolean('enable_security'),
                'audit_complete' => $request->boolean('enable_audit'),
            ]
        ]);
    }

    private function createConfigFile(array $config)
    {
        $config['setup_date'] = now()->toISOString();
        $config['version'] = '1.0-MVP';

        File::put(
            storage_path('app/guardian_config.json'),
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }

    private function markSetupComplete(string $mode)
    {
        File::put(
            storage_path('app/guardian_setup_complete.flag'),
            json_encode([
                'mode' => $mode,
                'completed_at' => now()->toISOString(),
                'version' => '1.0-MVP'
            ])
        );
    }

    private function isSystemConfigured(): bool
    {
        try {
            return File::exists(storage_path('app/guardian_setup_complete.flag')) ||
                   DB::table('users')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
