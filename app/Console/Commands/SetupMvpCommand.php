<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupMvpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guardian:setup-mvp {--force : Force the setup even if already configured}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Guardian MVP with sample data for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Configurando Guardian MVP...');

        // Verificar se já foi configurado
        if (!$this->option('force')) {
            try {
                if (DB::table('users')->count() > 0) {
                    if (!$this->confirm('O sistema já tem dados. Deseja continuar? (isso irá resetar tudo)')) {
                        $this->info('Cancelado pelo usuário.');
                        return 0;
                    }
                }
            } catch (\Exception $e) {
                // Banco não existe ainda, continua
            }
        }

        // Executar migrations
        $this->info('📋 Executando migrations...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->line(Artisan::output());

        // Executar seeders
        $this->info('🌱 Populando com dados de exemplo...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->line(Artisan::output());

        // Limpar caches
        $this->info('🧹 Limpando caches...');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        $this->info('✅ Guardian MVP configurado com sucesso!');
        $this->newLine();
        $this->info('👥 Usuários de teste criados:');
        $this->table(
            ['Email', 'Senha', 'Função'],
            [
                ['admin@guardian.local', 'guardian123', 'Super Admin'],
                ['joao@guardian.local', 'guardian123', 'Gerente de Projetos'],
                ['maria@guardian.local', 'guardian123', 'Líder de Equipe'],
                ['pedro@guardian.local', 'guardian123', 'Desenvolvedor'],
                ['ana@guardian.local', 'guardian123', 'Desenvolvedora'],
            ]
        );
        
        $this->newLine();
        $this->info('🌐 Acesse: http://localhost:8000');
        $this->info('📧 MailHog: http://localhost:8025');

        return 0;
    }
}
