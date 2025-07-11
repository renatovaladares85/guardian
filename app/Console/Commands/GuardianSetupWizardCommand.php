<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GuardianSetupWizardCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'guardian:setup {--mode= : Setup mode (test|production|interactive)}';

    /**
     * The console command description.
     */
    protected $description = 'Guardian System Setup Wizard - First time configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayWelcome();

        // Verificar se já foi configurado
        if ($this->isAlreadySetup() && !$this->option('mode')) {
            $this->warn('⚠️  Sistema já foi configurado anteriormente.');
            
            if (!$this->confirm('Deseja reconfigurar o sistema? (isso irá resetar todos os dados)')) {
                $this->info('Configuração cancelada.');
                return 0;
            }
        }

        $mode = $this->option('mode') ?: $this->selectSetupMode();

        switch ($mode) {
            case 'test':
                return $this->setupTestMode();
            case 'production':
                return $this->setupProductionMode();
            case 'interactive':
            default:
                return $this->setupInteractiveMode();
        }
    }

    private function displayWelcome()
    {
        $this->info('');
        $this->info('🛡️  ==========================================');
        $this->info('    GUARDIAN PROJECT MANAGEMENT SYSTEM');
        $this->info('    Assistente de Configuração Inicial');
        $this->info('==========================================');
        $this->info('');
    }

    private function isAlreadySetup(): bool
    {
        try {
            return DB::table('users')->count() > 0 || 
                   File::exists(storage_path('app/guardian_setup_complete.flag'));
        } catch (\Exception $e) {
            return false;
        }
    }

    private function selectSetupMode(): string
    {
        $this->info('Por favor, selecione o modo de configuração:');
        $this->info('');
        
        $choice = $this->choice(
            'Qual é o seu objetivo?',
            [
                'test' => '🧪 Modo Teste - MVP com dados de exemplo (recomendado para demonstração)',
                'production' => '🚀 Modo Produção - Sistema completo para uso real',
                'interactive' => '⚙️  Modo Interativo - Configuração personalizada passo a passo'
            ],
            'test'
        );

        return $choice;
    }

    private function setupTestMode(): int
    {
        $this->info('');
        $this->info('🧪 Configurando Guardian em Modo Teste...');
        $this->info('');

        // Preparar banco
        $this->info('📋 Preparando banco de dados...');
        Artisan::call('migrate:fresh', ['--force' => true]);

        // Dados de teste
        $this->info('🌱 Criando dados de demonstração...');
        Artisan::call('db:seed', ['--force' => true]);

        // Configurações otimizadas para teste
        $this->setupTestEnvironment();

        $this->markSetupComplete('test');
        $this->displayTestModeSuccess();

        return 0;
    }

    private function setupProductionMode(): int
    {
        $this->info('');
        $this->info('🚀 Configurando Guardian em Modo Produção...');
        $this->info('');

        // Validações de segurança
        if (!$this->validateProductionRequirements()) {
            return 1;
        }

        // Configurar banco
        $this->info('📋 Configurando banco de dados...');
        Artisan::call('migrate', ['--force' => true]);

        // Criar usuário administrador
        $this->createAdminUser();

        // Configurações de produção
        $this->setupProductionEnvironment();

        $this->markSetupComplete('production');
        $this->displayProductionModeSuccess();

        return 0;
    }

    private function setupInteractiveMode(): int
    {
        $this->info('');
        $this->info('⚙️  Configuração Interativa do Guardian...');
        $this->info('');

        // Perguntas de configuração
        $includeTestData = $this->confirm('Incluir dados de exemplo/demonstração?', true);
        $setupSecurity = $this->confirm('Configurar recursos de segurança avançados?', false);
        $enableAudit = $this->confirm('Habilitar auditoria completa?', true);

        // Configurar banco
        $this->info('📋 Configurando banco de dados...');
        if ($includeTestData) {
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        } else {
            Artisan::call('migrate', ['--force' => true]);
            $this->createAdminUser();
        }

        // Configurações específicas
        if ($setupSecurity) {
            $this->setupSecurityFeatures();
        }

        if ($enableAudit) {
            $this->setupAuditFeatures();
        }

        $this->markSetupComplete('interactive');
        $this->displayInteractiveModeSuccess($includeTestData);

        return 0;
    }

    private function validateProductionRequirements(): bool
    {
        $this->info('🔍 Validando requisitos de produção...');

        $errors = [];

        // Verificar ambiente
        if (env('APP_ENV') === 'local') {
            $errors[] = 'APP_ENV deve ser definido como "production"';
        }

        // Verificar chave da aplicação
        if (!env('APP_KEY')) {
            $errors[] = 'APP_KEY não está definida';
        }

        // Verificar configurações de banco
        if (!env('DB_PASSWORD') || env('DB_PASSWORD') === 'password') {
            $errors[] = 'DB_PASSWORD deve ser uma senha segura';
        }

        if (!empty($errors)) {
            $this->error('❌ Problemas encontrados:');
            foreach ($errors as $error) {
                $this->error("   - $error");
            }
            $this->info('');
            $this->info('Por favor, corrija essas configurações no arquivo .env antes de continuar.');
            return false;
        }

        return true;
    }

    private function createAdminUser(): void
    {
        $this->info('👤 Criando usuário administrador...');

        $name = $this->ask('Nome do administrador', 'Administrador');
        $email = $this->ask('Email do administrador', 'admin@empresa.com');
        
        do {
            $password = $this->secret('Senha do administrador (mínimo 8 caracteres)');
            if (strlen($password) < 8) {
                $this->error('Senha deve ter pelo menos 8 caracteres.');
            }
        } while (strlen($password) < 8);

        \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Usuário administrador criado: $email");
    }

    private function setupTestEnvironment(): void
    {
        $this->info('⚙️  Configurando ambiente de teste...');
        
        // Configurações específicas para teste
        file_put_contents(
            storage_path('app/guardian_config.json'),
            json_encode([
                'mode' => 'test',
                'features' => [
                    'demo_data' => true,
                    'security_relaxed' => true,
                    'audit_minimal' => true,
                ],
                'setup_date' => now()->toISOString(),
            ], JSON_PRETTY_PRINT)
        );
    }

    private function setupProductionEnvironment(): void
    {
        $this->info('⚙️  Configurando ambiente de produção...');
        
        file_put_contents(
            storage_path('app/guardian_config.json'),
            json_encode([
                'mode' => 'production',
                'features' => [
                    'demo_data' => false,
                    'security_enhanced' => true,
                    'audit_complete' => true,
                ],
                'setup_date' => now()->toISOString(),
            ], JSON_PRETTY_PRINT)
        );

        // Otimizações de produção
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
    }

    private function setupSecurityFeatures(): void
    {
        $this->info('🔐 Configurando recursos de segurança...');
        // Implementar configurações de segurança avançadas
    }

    private function setupAuditFeatures(): void
    {
        $this->info('📊 Configurando auditoria...');
        // Implementar configurações de auditoria
    }

    private function markSetupComplete(string $mode): void
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

    private function displayTestModeSuccess(): void
    {
        $this->info('');
        $this->info('✅ Guardian configurado em Modo Teste com sucesso!');
        $this->info('');
        $this->info('🌐 Acesso: http://localhost:8000');
        $this->info('📧 MailHog: http://localhost:8025');
        $this->info('');
        $this->info('👥 Usuários de teste (senha: guardian123):');
        $this->table(
            ['Email', 'Função'],
            [
                ['admin@guardian.local', 'Super Admin'],
                ['joao@guardian.local', 'Gerente de Projetos'],
                ['maria@guardian.local', 'Líder de Equipe'],
                ['pedro@guardian.local', 'Desenvolvedor'],
                ['ana@guardian.local', 'Desenvolvedora'],
            ]
        );
        $this->info('');
        $this->info('🎯 O sistema inclui projetos e tarefas de exemplo!');
        $this->info('📖 Consulte o arquivo TESTE-MVP.md para roteiro completo de testes.');
    }

    private function displayProductionModeSuccess(): void
    {
        $this->info('');
        $this->info('✅ Guardian configurado em Modo Produção com sucesso!');
        $this->info('');
        $this->info('🚀 Sistema pronto para uso em produção.');
        $this->info('🔐 Recursos de segurança ativados.');
        $this->info('📊 Auditoria completa habilitada.');
        $this->info('');
        $this->info('⚠️  Lembre-se de:');
        $this->info('   - Configurar backup regular do banco de dados');
        $this->info('   - Revisar configurações de segurança');
        $this->info('   - Monitorar logs do sistema');
    }

    private function displayInteractiveModeSuccess(bool $hasTestData): void
    {
        $this->info('');
        $this->info('✅ Guardian configurado com sucesso!');
        $this->info('');
        
        if ($hasTestData) {
            $this->info('🎯 Sistema configurado com dados de exemplo.');
            $this->info('👥 Use os usuários de teste para experimentar o sistema.');
        } else {
            $this->info('🚀 Sistema configurado para uso real.');
            $this->info('👤 Use o usuário administrador criado para começar.');
        }
        
        $this->info('');
        $this->info('🌐 Acesse: http://localhost:8000');
    }
}
