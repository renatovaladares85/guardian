<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SetupWizardController;

// Rotas de configuração inicial (sem middleware de autenticação)
Route::prefix('setup')->name('setup.')->group(function () {
    Route::get('/', [SetupWizardController::class, 'index'])->name('wizard');
    Route::post('/process', [SetupWizardController::class, 'process'])->name('process');
    Route::get('/complete', [SetupWizardController::class, 'complete'])->name('complete');
});

// Página inicial - redireciona baseado no estado do sistema
Route::get('/', function () {
    // Verificar se sistema foi configurado
    if (!\Illuminate\Support\Facades\File::exists(storage_path('app/guardian_setup_complete.flag'))) {
        try {
            if (\Illuminate\Support\Facades\DB::table('users')->count() === 0) {
                return redirect()->route('setup.wizard');
            }
        } catch (\Exception $e) {
            return redirect()->route('setup.wizard');
        }
    }
    
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// Rotas que requerem autenticação e sistema configurado
Route::middleware(['auth', 'verified', 'check.system.setup'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Projetos
    Route::resource('projects', ProjectController::class);
    
    // Tarefas
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    // Perfil do usuário
    Route::get('/profile', function() {
        return view('profile.edit');
    })->name('profile.edit');
});

// Health check endpoint para monitoramento dos containers
Route::get('/health-check', function () {
    try {
        // Verificar conexão com banco de dados
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        
        // Verificar conexão com Redis
        \Illuminate\Support\Facades\Redis::ping();
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'database' => 'connected',
            'redis' => 'connected',
            'version' => config('app.version', '1.0.0')
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->toISOString(),
            'error' => $e->getMessage()
        ], 503);
    }
});

require __DIR__.'/auth.php';
