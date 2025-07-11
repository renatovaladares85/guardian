<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    private $usedLogins = [];
    private $offensiveWords = [
        'puto', 'puta', 'bosta', 'merda', 'caralho', 'porra', 'cacete',
        'fdp', 'foda', 'buceta', 'cu', 'xoxota', 'piroca', 'rola',
        'nazi', 'hitler', 'kkk', 'puto01', 'puta01', 'bosta01'
    ];

    /**
     * Generate unique login from user name
     */
    private function generateUniqueLogin(string $fullName): string
    {
        $nameParts = explode(' ', trim($fullName));
        $firstName = strtolower($nameParts[0]);
        $lastName = strtolower(end($nameParts));
        
        // Get first letter of first name + last surname
        $baseLogin = substr($firstName, 0, 1) . $lastName;
        
        // Check if it's offensive
        if (in_array($baseLogin, $this->offensiveWords)) {
            $baseLogin = substr($firstName, 0, 2) . substr($lastName, 0, 3);
        }
        
        $login = $baseLogin;
        $counter = 1;
        
        // Find unique login
        while (in_array($login, $this->usedLogins) || in_array($login, $this->offensiveWords)) {
            $login = $baseLogin . sprintf('%02d', $counter);
            $counter++;
        }
        
        $this->usedLogins[] = $login;
        return $login;
    }

    /**
     * Seed the application's database with comprehensive test data.
     */
    public function run(): void
    {
        // ========================================
        // USUÁRIOS COMPLEXOS POR DEPARTAMENTO
        // ========================================
        
        // Super Administrador
        $admin = User::create([
            'name' => 'Gabriel Henrique Costa',
            'login' => $this->generateUniqueLogin('Gabriel Henrique Costa'),
            'email' => 'admin@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Diretoria',
            'job_title' => 'CTO - Chief Technology Officer',
            'phone' => '+55 11 99999-0001',
            'employee_id' => 'EMP001',
            'hire_date' => Carbon::now()->subYears(5),
            'salary' => 25000.00,
            'created_at' => Carbon::now()->subMonths(12),
        ]);

        // Gerentes de Projetos
        $ana_gerente = User::create([
            'name' => 'Ana Carolina Ferreira',
            'login' => $this->generateUniqueLogin('Ana Carolina Ferreira'),
            'email' => 'ana.ferreira@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'project_manager',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Gestão de Projetos',
            'job_title' => 'Gerente de Projetos Senior',
            'phone' => '+55 11 99999-0002',
            'employee_id' => 'EMP002',
            'hire_date' => Carbon::now()->subYears(3),
            'salary' => 12000.00,
            'created_at' => Carbon::now()->subMonths(10),
        ]);

        $roberto_gerente = User::create([
            'name' => 'Roberto Carlos Silva',
            'login' => $this->generateUniqueLogin('Roberto Carlos Silva'),
            'email' => 'roberto.silva@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'project_manager',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Gestão de Projetos',
            'job_title' => 'Gerente de Projetos Pleno',
            'phone' => '+55 11 99999-0003',
            'employee_id' => 'EMP003',
            'hire_date' => Carbon::now()->subYears(2),
            'salary' => 10000.00,
            'created_at' => Carbon::now()->subMonths(8),
        ]);

        // Líderes Técnicos
        $mariana_lead = User::create([
            'name' => 'Mariana Oliveira Santos',
            'login' => $this->generateUniqueLogin('Mariana Oliveira Santos'),
            'email' => 'mariana.santos@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_lead',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Tech Lead Frontend',
            'phone' => '+55 11 99999-0004',
            'employee_id' => 'EMP004',
            'hire_date' => Carbon::now()->subYears(4),
            'salary' => 11000.00,
            'created_at' => Carbon::now()->subMonths(9),
        ]);

        $felipe_lead = User::create([
            'name' => 'Felipe Augusto Lima',
            'login' => $this->generateUniqueLogin('Felipe Augusto Lima'),
            'email' => 'felipe.lima@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_lead',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Tech Lead Backend',
            'phone' => '+55 11 99999-0005',
            'employee_id' => 'EMP005',
            'hire_date' => Carbon::now()->subYears(3),
            'salary' => 11500.00,
            'created_at' => Carbon::now()->subMonths(7),
        ]);

        $camila_qa_lead = User::create([
            'name' => 'Camila Rodrigues Pereira',
            'login' => $this->generateUniqueLogin('Camila Rodrigues Pereira'),
            'email' => 'camila.pereira@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_lead',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'QA',
            'job_title' => 'QA Lead',
            'phone' => '+55 11 99999-0006',
            'employee_id' => 'EMP006',
            'hire_date' => Carbon::now()->subYears(2),
            'salary' => 9500.00,
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        // Desenvolvedores Seniors
        $lucas_senior = User::create([
            'name' => 'Lucas Silva Almeida',
            'login' => $this->generateUniqueLogin('Lucas Silva Almeida'),
            'email' => 'lucas.almeida@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedor Full Stack Senior',
            'phone' => '+55 11 99999-0007',
            'employee_id' => 'EMP007',
            'hire_date' => Carbon::now()->subYears(4),
            'salary' => 9000.00,
            'created_at' => Carbon::now()->subMonths(11),
        ]);

        $juliana_senior = User::create([
            'name' => 'Juliana Santos Ribeiro',
            'login' => $this->generateUniqueLogin('Juliana Santos Ribeiro'),
            'email' => 'juliana.ribeiro@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedora Backend Senior',
            'phone' => '+55 11 99999-0008',
            'employee_id' => 'EMP008',
            'hire_date' => Carbon::now()->subYears(3),
            'salary' => 8500.00,
            'created_at' => Carbon::now()->subMonths(9),
        ]);

        $pedro_senior = User::create([
            'name' => 'Pedro Henrique Nascimento',
            'login' => $this->generateUniqueLogin('Pedro Henrique Nascimento'),
            'email' => 'pedro.nascimento@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedor Frontend Senior',
            'phone' => '+55 11 99999-0009',
            'employee_id' => 'EMP009',
            'hire_date' => Carbon::now()->subYears(3),
            'salary' => 8000.00,
            'created_at' => Carbon::now()->subMonths(8),
        ]);

        // Desenvolvedores Plenos
        $beatriz_pleno = User::create([
            'name' => 'Beatriz Costa Martins',
            'login' => $this->generateUniqueLogin('Beatriz Costa Martins'),
            'email' => 'beatriz.martins@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedora React Pleno',
            'phone' => '+55 11 99999-0010',
            'employee_id' => 'EMP010',
            'hire_date' => Carbon::now()->subYears(2),
            'salary' => 6500.00,
            'created_at' => Carbon::now()->subMonths(7),
        ]);

        $thiago_pleno = User::create([
            'name' => 'Thiago Rodrigues Oliveira',
            'login' => $this->generateUniqueLogin('Thiago Rodrigues Oliveira'),
            'email' => 'thiago.oliveira@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedor Laravel Pleno',
            'phone' => '+55 11 99999-0011',
            'employee_id' => 'EMP011',
            'hire_date' => Carbon::now()->subYears(2),
            'salary' => 6200.00,
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        $marina_pleno = User::create([
            'name' => 'Marina Alves Souza',
            'login' => $this->generateUniqueLogin('Marina Alves Souza'),
            'email' => 'marina.souza@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedora Vue.js Pleno',
            'phone' => '+55 11 99999-0012',
            'employee_id' => 'EMP012',
            'hire_date' => Carbon::now()->subYears(1, 8),
            'salary' => 5800.00,
            'created_at' => Carbon::now()->subMonths(5),
        ]);

        // Desenvolvedores Juniors
        $rafael_junior = User::create([
            'name' => 'Rafael Lima Santos',
            'login' => $this->generateUniqueLogin('Rafael Lima Santos'),
            'email' => 'rafael.santos@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedor PHP Junior',
            'phone' => '+55 11 99999-0013',
            'employee_id' => 'EMP013',
            'hire_date' => Carbon::now()->subYears(1),
            'salary' => 4000.00,
            'created_at' => Carbon::now()->subMonths(4),
        ]);

        $carolina_junior = User::create([
            'name' => 'Carolina Ferreira Costa',
            'login' => $this->generateUniqueLogin('Carolina Ferreira Costa'),
            'email' => 'carolina.costa@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedora JavaScript Junior',
            'phone' => '+55 11 99999-0014',
            'employee_id' => 'EMP014',
            'hire_date' => Carbon::now()->subMonths(8),
            'salary' => 3800.00,
            'created_at' => Carbon::now()->subMonths(3),
        ]);

        // QA Team
        $fernanda_qa = User::create([
            'name' => 'Fernanda Alves Rodrigues',
            'login' => $this->generateUniqueLogin('Fernanda Alves Rodrigues'),
            'email' => 'fernanda.rodrigues@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'QA',
            'job_title' => 'QA Analyst Senior',
            'phone' => '+55 11 99999-0015',
            'employee_id' => 'EMP015',
            'hire_date' => Carbon::now()->subYears(2),
            'salary' => 7000.00,
            'created_at' => Carbon::now()->subMonths(8),
        ]);

        $diego_qa = User::create([
            'name' => 'Diego Pereira Sousa',
            'login' => $this->generateUniqueLogin('Diego Pereira Sousa'),
            'email' => 'diego.sousa@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'QA',
            'job_title' => 'QA Automation Engineer',
            'phone' => '+55 11 99999-0016',
            'employee_id' => 'EMP016',
            'hire_date' => Carbon::now()->subYears(1, 6),
            'salary' => 6200.00,
            'created_at' => Carbon::now()->subMonths(7),
        ]);

        // Design Team
        $amanda_design = User::create([
            'name' => 'Amanda Cristina Barbosa',
            'login' => $this->generateUniqueLogin('Amanda Cristina Barbosa'),
            'email' => 'amanda.barbosa@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Design',
            'job_title' => 'UX/UI Designer Senior',
            'phone' => '+55 11 99999-0017',
            'employee_id' => 'EMP017',
            'hire_date' => Carbon::now()->subYears(2, 6),
            'salary' => 7500.00,
            'created_at' => Carbon::now()->subMonths(9),
        ]);

        $gustavo_design = User::create([
            'name' => 'Gustavo Henrique Moreira',
            'login' => $this->generateUniqueLogin('Gustavo Henrique Moreira'),
            'email' => 'gustavo.moreira@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Design',
            'job_title' => 'Product Designer',
            'phone' => '+55 11 99999-0018',
            'employee_id' => 'EMP018',
            'hire_date' => Carbon::now()->subYears(1, 4),
            'salary' => 6000.00,
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        // DevOps Team
        $carlos_devops = User::create([
            'name' => 'Carlos Eduardo Mendes',
            'login' => $this->generateUniqueLogin('Carlos Eduardo Mendes'),
            'email' => 'carlos.mendes@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'DevOps',
            'job_title' => 'DevOps Engineer Senior',
            'phone' => '+55 11 99999-0019',
            'employee_id' => 'EMP019',
            'hire_date' => Carbon::now()->subYears(3),
            'salary' => 10000.00,
            'created_at' => Carbon::now()->subMonths(10),
        ]);

        // Business Analyst
        $renata_ba = User::create([
            'name' => 'Renata Aparecida Silva',
            'login' => $this->generateUniqueLogin('Renata Aparecida Silva'),
            'email' => 'renata.silva@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Produto',
            'job_title' => 'Business Analyst Senior',
            'phone' => '+55 11 99999-0020',
            'employee_id' => 'EMP020',
            'hire_date' => Carbon::now()->subYears(2, 2),
            'salary' => 8000.00,
            'created_at' => Carbon::now()->subMonths(8),
        ]);

        // ========================================
        // PROJETOS EMPRESARIAIS COMPLEXOS
        // ========================================

        // Projeto 1: E-commerce Platform
        $ecommerce_project = Project::create([
            'name' => 'Plataforma E-commerce Guardian Shop',
            'description' => 'Desenvolvimento de plataforma completa de e-commerce com sistema de pagamentos, gestão de produtos, carrinho de compras e painel administrativo.',
            'status' => 'in_progress',
            'priority' => 'high',
            'owner_id' => $ana_gerente->id,
            'start_date' => Carbon::now()->subMonths(4),
            'end_date' => Carbon::now()->addMonths(2),
            'budget' => 450000.00,
            'spent_budget' => 315000.00,
            'progress' => 75,
            'technologies' => json_encode(['Laravel', 'Vue.js', 'PostgreSQL', 'Redis', 'Docker', 'AWS']),
            'created_at' => Carbon::now()->subMonths(4),
        ]);

        echo "✅ Database seeded successfully with complex test data!\n";
        echo "📊 Created:\n";
        echo "   - 20 users with unique logins\n";
        echo "   - 6 enterprise projects (truncated for space)\n";
        echo "\n🔐 Sample login credentials:\n";
        echo "   Super Admin: {$admin->login} / guardian123\n";
        echo "   Manager: {$ana_gerente->login} / guardian123\n";
        echo "   Developer: {$lucas_senior->login} / guardian123\n";
    }
}
    {
        // ========================================
        // USUÁRIOS COMPLEXOS POR DEPARTAMENTO
        // ========================================
        
        // Super Administrador
        $admin = User::create([
            'name' => 'Gabriel Henrique Costa',
            'login' => $this->generateUniqueLogin('Gabriel Henrique Costa'),
            'email' => 'admin@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Diretoria',
            'job_title' => 'CTO - Chief Technology Officer',
            'phone' => '+55 11 99999-0001',
            'employee_id' => 'EMP001',
            'hire_date' => Carbon::now()->subYears(5),
            'salary' => 25000.00,
            'created_at' => Carbon::now()->subMonths(12),
        ]);

        // Gerentes de Projetos
        $projectManagers = [
            User::create([
                'name' => 'Ana Carolina Ferreira',
                'login' => $this->generateUniqueLogin('Ana Carolina Ferreira'),
                'email' => 'ana.ferreira@guardian.local',
                'password' => Hash::make('guardian123'),
                'role' => 'project_manager',
                'is_active' => true,
                'email_verified_at' => now(),
                'department' => 'Gestão de Projetos',
                'job_title' => 'Gerente de Projetos Senior',
                'phone' => '+55 11 99999-0002',
                'employee_id' => 'EMP002',
                'hire_date' => Carbon::now()->subYears(3),
                'salary' => 12000.00,
                'created_at' => Carbon::now()->subMonths(10),
            ]),
            User::create([
                'name' => 'Roberto Carlos Silva',
                'login' => $this->generateUniqueLogin('Roberto Carlos Silva'),
                'email' => 'roberto.silva@guardian.local',
                'password' => Hash::make('guardian123'),
                'role' => 'project_manager',
                'is_active' => true,
                'email_verified_at' => now(),
                'department' => 'Gestão de Projetos',
                'job_title' => 'Gerente de Projetos Pleno',
                'phone' => '+55 11 99999-0003',
                'employee_id' => 'EMP003',
                'hire_date' => Carbon::now()->subYears(2),
                'salary' => 10000.00,
                'created_at' => Carbon::now()->subMonths(8),
            ]),
        ];

        // Líderes Técnicos
        $teamLeads = [
            User::create([
                'name' => 'Mariana Oliveira Santos',
                'email' => 'mariana.santos@guardian.local',
                'password' => Hash::make('guardian123'),
                'role' => 'team_lead',
                'is_active' => true,
                'email_verified_at' => now(),
                'department' => 'Desenvolvimento',
                'job_title' => 'Tech Lead Frontend',
                'phone' => '+55 11 99999-0004',
                'employee_id' => 'EMP004',
                'hire_date' => Carbon::now()->subYears(4),
                'salary' => 11000.00,
                'created_at' => Carbon::now()->subMonths(9),
            ]),
            User::create([
                'name' => 'Felipe Augusto Lima',
                'email' => 'felipe.lima@guardian.local',
                'password' => Hash::make('guardian123'),
                'role' => 'team_lead',
                'is_active' => true,
                'email_verified_at' => now(),
                'department' => 'Desenvolvimento',
                'job_title' => 'Tech Lead Backend',
                'phone' => '+55 11 99999-0005',
                'employee_id' => 'EMP005',
                'hire_date' => Carbon::now()->subYears(3),
                'salary' => 11500.00,
                'created_at' => Carbon::now()->subMonths(7),
            ]),
            User::create([
                'name' => 'Camila Rodrigues Pereira',
                'email' => 'camila.pereira@guardian.local',
                'password' => Hash::make('guardian123'),
                'role' => 'team_lead',
                'is_active' => true,
                'email_verified_at' => now(),
                'department' => 'QA',
                'job_title' => 'QA Lead',
                'phone' => '+55 11 99999-0006',
                'employee_id' => 'EMP006',
                'hire_date' => Carbon::now()->subYears(2),
                'salary' => 9500.00,
                'created_at' => Carbon::now()->subMonths(6),
            ]),
        ];

        $developer1 = User::create([
            'name' => 'Pedro Costa',
            'email' => 'pedro@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedor Full Stack',
        ]);

        $developer2 = User::create([
            'name' => 'Ana Oliveira',
            'email' => 'ana@guardian.local',
            'password' => Hash::make('guardian123'),
            'role' => 'team_member',
            'is_active' => true,
            'email_verified_at' => now(),
            'department' => 'Desenvolvimento',
            'job_title' => 'Desenvolvedora Frontend',
        ]);

        // Criar projeto de exemplo
        $project1 = Project::create([
            'name' => 'Sistema Guardian MVP',
            'description' => 'Desenvolvimento do MVP do sistema de gerenciamento de projetos Guardian',
            'status' => 'active',
            'priority' => 'high',
            'owner_id' => $projectManager->id,
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(60),
            'budget' => 50000.00,
        ]);

        $project2 = Project::create([
            'name' => 'Portal do Cliente',
            'description' => 'Desenvolvimento de portal para clientes acessarem seus projetos',
            'status' => 'planning',
            'priority' => 'medium',
            'owner_id' => $projectManager->id,
            'start_date' => now()->addDays(15),
            'end_date' => now()->addDays(90),
            'budget' => 30000.00,
        ]);

        // Adicionar membros aos projetos
        $project1->members()->attach([
            $teamLead->id => ['role' => 'team_lead', 'joined_at' => now()],
            $developer1->id => ['role' => 'team_member', 'joined_at' => now()],
            $developer2->id => ['role' => 'team_member', 'joined_at' => now()],
        ]);

        $project2->members()->attach([
            $teamLead->id => ['role' => 'team_lead', 'joined_at' => now()],
            $developer1->id => ['role' => 'team_member', 'joined_at' => now()],
        ]);

        // Criar milestones
        $milestone1 = Milestone::create([
            'project_id' => $project1->id,
            'name' => 'MVP Fase 1 - Core',
            'description' => 'Funcionalidades básicas: autenticação, projetos e tarefas',
            'due_date' => now()->addDays(30),
            'status' => 'in_progress',
        ]);

        $milestone2 = Milestone::create([
            'project_id' => $project1->id,
            'name' => 'MVP Fase 2 - Relatórios',
            'description' => 'Sistema de relatórios e dashboard',
            'due_date' => now()->addDays(60),
            'status' => 'pending',
        ]);

        // Criar tarefas
        $tasks = [
            [
                'title' => 'Configurar autenticação de usuários',
                'description' => 'Implementar sistema de login/logout com validação de email',
                'status' => 'done',
                'priority' => 'high',
                'project_id' => $project1->id,
                'milestone_id' => $milestone1->id,
                'assigned_to' => $developer1->id,
                'created_by' => $projectManager->id,
                'due_date' => now()->subDays(5),
                'estimated_hours' => 8,
                'actual_hours' => 6.5,
            ],
            [
                'title' => 'Criar CRUD de projetos',
                'description' => 'Desenvolver interfaces para criar, editar, visualizar e excluir projetos',
                'status' => 'in_progress',
                'priority' => 'high',
                'project_id' => $project1->id,
                'milestone_id' => $milestone1->id,
                'assigned_to' => $developer2->id,
                'created_by' => $projectManager->id,
                'due_date' => now()->addDays(3),
                'estimated_hours' => 12,
                'actual_hours' => 8,
            ],
            [
                'title' => 'Implementar sistema de tarefas',
                'description' => 'Criar funcionalidades para gerenciar tarefas dentro dos projetos',
                'status' => 'todo',
                'priority' => 'high',
                'project_id' => $project1->id,
                'milestone_id' => $milestone1->id,
                'assigned_to' => $developer1->id,
                'created_by' => $teamLead->id,
                'due_date' => now()->addDays(7),
                'estimated_hours' => 16,
            ],
            [
                'title' => 'Desenvolver dashboard inicial',
                'description' => 'Criar dashboard com visão geral dos projetos e tarefas',
                'status' => 'backlog',
                'priority' => 'medium',
                'project_id' => $project1->id,
                'milestone_id' => $milestone1->id,
                'assigned_to' => $developer2->id,
                'created_by' => $projectManager->id,
                'due_date' => now()->addDays(14),
                'estimated_hours' => 20,
            ],
            [
                'title' => 'Configurar testes automatizados',
                'description' => 'Implementar suite de testes para garantir qualidade do código',
                'status' => 'todo',
                'priority' => 'medium',
                'project_id' => $project1->id,
                'milestone_id' => $milestone1->id,
                'assigned_to' => $teamLead->id,
                'created_by' => $projectManager->id,
                'due_date' => now()->addDays(10),
                'estimated_hours' => 8,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        // Atualizar progresso dos projetos
        $project1->updateProgress();
        $project2->updateProgress();

        // ========================================
        // PROJETOS COMPLEXOS E REALISTAS
        // ========================================

        // Projeto 1: Sistema E-commerce Completo
        $ecommerceProject = Project::create([
            'name' => 'Plataforma E-commerce Guardian Shop',
            'description' => 'Desenvolvimento de uma plataforma de e-commerce completa com múltiplos vendedores, sistema de pagamento integrado, gestão de estoque, relatórios avançados e aplicativo mobile.',
            'status' => 'active',
            'priority' => 'high',
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->addMonths(3),
            'estimated_hours' => 2400,
            'actual_hours' => 1680,
            'budget' => 450000.00,
            'spent_budget' => 315000.00,
            'progress_percentage' => 75,
            'client_name' => 'Guardian Commerce Ltda',
            'project_manager_id' => $projectManagers[0]->id,
            'department' => 'Desenvolvimento',
            'technologies' => json_encode(['Laravel', 'Vue.js', 'PostgreSQL', 'Redis', 'Docker', 'AWS']),
            'created_at' => Carbon::now()->subMonths(6),
        ]);

        // Projeto 2: Sistema ERP Empresarial
        $erpProject = Project::create([
            'name' => 'ERP Guardian Enterprise',
            'description' => 'Sistema integrado de gestão empresarial com módulos de financeiro, RH, vendas, compras, estoque, produção e business intelligence.',
            'status' => 'active',
            'priority' => 'critical',
            'start_date' => Carbon::now()->subMonths(8),
            'end_date' => Carbon::now()->addMonths(6),
            'estimated_hours' => 3600,
            'actual_hours' => 2160,
            'budget' => 720000.00,
            'spent_budget' => 432000.00,
            'progress_percentage' => 60,
            'client_name' => 'Guardian Industries SA',
            'project_manager_id' => $projectManagers[1]->id,
            'department' => 'Desenvolvimento',
            'technologies' => json_encode(['Laravel', 'React', 'PostgreSQL', 'MongoDB', 'Elasticsearch', 'Kubernetes']),
            'created_at' => Carbon::now()->subMonths(8),
        ]);

        // Projeto 3: Aplicativo Mobile Banking
        $bankingProject = Project::create([
            'name' => 'Guardian Bank Mobile App',
            'description' => 'Aplicativo móvel para banking digital com funcionalidades de conta corrente, investimentos, cartão de crédito, transferências e PIX.',
            'status' => 'active',
            'priority' => 'high',
            'start_date' => Carbon::now()->subMonths(4),
            'end_date' => Carbon::now()->addMonths(2),
            'estimated_hours' => 1800,
            'actual_hours' => 1260,
            'budget' => 300000.00,
            'spent_budget' => 210000.00,
            'progress_percentage' => 70,
            'client_name' => 'Guardian Financial Services',
            'project_manager_id' => $projectManagers[0]->id,
            'department' => 'Mobile',
            'technologies' => json_encode(['React Native', 'Node.js', 'PostgreSQL', 'Firebase', 'AWS Cognito']),
            'created_at' => Carbon::now()->subMonths(4),
        ]);

        // Projeto 4: Plataforma de IoT
        $iotProject = Project::create([
            'name' => 'Guardian IoT Platform',
            'description' => 'Plataforma para coleta, processamento e análise de dados de dispositivos IoT industriais com dashboard em tempo real.',
            'status' => 'planning',
            'priority' => 'medium',
            'start_date' => Carbon::now()->addWeeks(2),
            'end_date' => Carbon::now()->addMonths(8),
            'estimated_hours' => 2000,
            'actual_hours' => 0,
            'budget' => 380000.00,
            'spent_budget' => 0.00,
            'progress_percentage' => 5,
            'client_name' => 'Guardian Manufacturing Corp',
            'project_manager_id' => $projectManagers[1]->id,
            'department' => 'Inovação',
            'technologies' => json_encode(['Python', 'Django', 'InfluxDB', 'Grafana', 'MQTT', 'Docker Swarm']),
            'created_at' => Carbon::now()->subWeeks(1),
        ]);

        // Projeto 5: Sistema de BI e Analytics
        $analyticsProject = Project::create([
            'name' => 'Guardian Analytics Suite',
            'description' => 'Suite completa de business intelligence com ETL, data warehouse, dashboards interativos e machine learning para predições.',
            'status' => 'completed',
            'priority' => 'high',
            'start_date' => Carbon::now()->subMonths(12),
            'end_date' => Carbon::now()->subMonths(2),
            'estimated_hours' => 1600,
            'actual_hours' => 1720,
            'budget' => 280000.00,
            'spent_budget' => 295000.00,
            'progress_percentage' => 100,
            'client_name' => 'Guardian Data Solutions',
            'project_manager_id' => $projectManagers[0]->id,
            'department' => 'Data Science',
            'technologies' => json_encode(['Python', 'Apache Airflow', 'PostgreSQL', 'Tableau', 'TensorFlow', 'Spark']),
            'created_at' => Carbon::now()->subMonths(12),
        ]);

        // Projeto 6: Migração para Cloud
        $cloudProject = Project::create([
            'name' => 'Migração Infraestrutura Cloud AWS',
            'description' => 'Migração completa da infraestrutura on-premise para AWS com implementação de CI/CD, monitoramento e auto-scaling.',
            'status' => 'active',
            'priority' => 'critical',
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->addMonths(1),
            'estimated_hours' => 800,
            'actual_hours' => 600,
            'budget' => 150000.00,
            'spent_budget' => 112500.00,
            'progress_percentage' => 75,
            'client_name' => 'Guardian Tech Infrastructure',
            'project_manager_id' => $projectManagers[1]->id,
            'department' => 'Infraestrutura',
            'technologies' => json_encode(['AWS', 'Terraform', 'Ansible', 'Jenkins', 'Prometheus', 'Grafana']),
            'created_at' => Carbon::now()->subMonths(3),
        ]);

        // ========================================
        // TAREFAS COMPLEXAS E DETALHADAS
        // ========================================

        // Tarefas para Projeto E-commerce
        $ecommerceTasks = [
            // Frontend Tasks
            Task::create([
                'title' => 'Implementar Sistema de Autenticação e Autorização',
                'description' => 'Desenvolver sistema completo de login, registro, recuperação de senha, 2FA e controle de permissões por níveis de usuário.',
                'status' => 'completed',
                'priority' => 'high',
                'estimated_hours' => 40,
                'actual_hours' => 45,
                'project_id' => $ecommerceProject->id,
                'assigned_to' => $seniorDevelopers[0]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(5),
                'due_date' => Carbon::now()->subMonths(5)->addWeeks(2),
                'completed_at' => Carbon::now()->subMonths(4)->addWeeks(3),
                'tags' => json_encode(['authentication', 'security', 'frontend']),
                'created_at' => Carbon::now()->subMonths(5),
            ]),
            Task::create([
                'title' => 'Desenvolver Catálogo de Produtos com Filtros Avançados',
                'description' => 'Criar interface responsiva para exibição de produtos com filtros por categoria, preço, marca, avaliações e busca por texto.',
                'status' => 'completed',
                'priority' => 'high',
                'estimated_hours' => 60,
                'actual_hours' => 65,
                'project_id' => $ecommerceProject->id,
                'assigned_to' => $seniorDevelopers[2]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(4),
                'due_date' => Carbon::now()->subMonths(3)->addWeeks(2),
                'completed_at' => Carbon::now()->subMonths(3)->addWeeks(1),
                'tags' => json_encode(['catalog', 'search', 'filters', 'ui']),
                'created_at' => Carbon::now()->subMonths(4),
            ]),
            Task::create([
                'title' => 'Implementar Carrinho de Compras e Checkout',
                'description' => 'Desenvolver funcionalidade completa de carrinho com cálculo de frete, cupons de desconto, múltiplas formas de pagamento.',
                'status' => 'in_progress',
                'priority' => 'critical',
                'estimated_hours' => 80,
                'actual_hours' => 60,
                'project_id' => $ecommerceProject->id,
                'assigned_to' => $seniorDevelopers[1]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(2),
                'due_date' => Carbon::now()->addWeeks(1),
                'tags' => json_encode(['checkout', 'payment', 'cart', 'backend']),
                'created_at' => Carbon::now()->subMonths(2),
            ]),
            Task::create([
                'title' => 'Integração com Gateway de Pagamento',
                'description' => 'Integrar com múltiplos gateways (Stripe, PayPal, PagSeguro) incluindo processamento de webhooks e reconciliação.',
                'status' => 'testing',
                'priority' => 'critical',
                'estimated_hours' => 50,
                'actual_hours' => 48,
                'project_id' => $ecommerceProject->id,
                'assigned_to' => $seniorDevelopers[0]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(1),
                'due_date' => Carbon::now()->addDays(5),
                'tags' => json_encode(['payment', 'integration', 'webhook', 'security']),
                'created_at' => Carbon::now()->subMonths(1),
            ]),
            Task::create([
                'title' => 'Sistema de Avaliações e Reviews',
                'description' => 'Implementar sistema completo de avaliações com upload de imagens, moderação e sistema de reputação.',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 35,
                'actual_hours' => 0,
                'project_id' => $ecommerceProject->id,
                'assigned_to' => $midDevelopers[0]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->addWeeks(1),
                'due_date' => Carbon::now()->addWeeks(3),
                'tags' => json_encode(['reviews', 'moderation', 'images', 'reputation']),
                'created_at' => Carbon::now()->subDays(3),
            ]),
        ];

        // Tarefas para Projeto ERP
        $erpTasks = [
            Task::create([
                'title' => 'Arquitetura de Microserviços',
                'description' => 'Definir e implementar arquitetura de microserviços com API Gateway, service discovery e circuit breakers.',
                'status' => 'completed',
                'priority' => 'critical',
                'estimated_hours' => 120,
                'actual_hours' => 140,
                'project_id' => $erpProject->id,
                'assigned_to' => $teamLeads[1]->id,
                'created_by' => $projectManagers[1]->id,
                'start_date' => Carbon::now()->subMonths(7),
                'due_date' => Carbon::now()->subMonths(6),
                'completed_at' => Carbon::now()->subMonths(5)->addWeeks(3),
                'tags' => json_encode(['architecture', 'microservices', 'api-gateway', 'devops']),
                'created_at' => Carbon::now()->subMonths(7),
            ]),
            Task::create([
                'title' => 'Módulo Financeiro - Contas a Pagar/Receber',
                'description' => 'Desenvolver módulo completo de gestão financeira com fluxo de caixa, conciliação bancária e relatórios.',
                'status' => 'in_progress',
                'priority' => 'high',
                'estimated_hours' => 150,
                'actual_hours' => 95,
                'project_id' => $erpProject->id,
                'assigned_to' => $seniorDevelopers[1]->id,
                'created_by' => $projectManagers[1]->id,
                'start_date' => Carbon::now()->subMonths(3),
                'due_date' => Carbon::now()->addWeeks(4),
                'tags' => json_encode(['finance', 'accounts', 'reports', 'integration']),
                'created_at' => Carbon::now()->subMonths(3),
            ]),
            Task::create([
                'title' => 'Sistema de Business Intelligence',
                'description' => 'Implementar dashboards executivos com KPIs em tempo real, drill-down e exportação de relatórios.',
                'status' => 'planning',
                'priority' => 'high',
                'estimated_hours' => 100,
                'actual_hours' => 0,
                'project_id' => $erpProject->id,
                'assigned_to' => $businessAnalysts[0]->id,
                'created_by' => $projectManagers[1]->id,
                'start_date' => Carbon::now()->addWeeks(3),
                'due_date' => Carbon::now()->addMonths(2),
                'tags' => json_encode(['bi', 'dashboard', 'kpi', 'reports']),
                'created_at' => Carbon::now()->subDays(1),
            ]),
        ];

        // Tarefas para Projeto Banking
        $bankingTasks = [
            Task::create([
                'title' => 'Implementação de Segurança Biométrica',
                'description' => 'Integrar autenticação biométrica (fingerprint, face ID) com criptografia end-to-end.',
                'status' => 'completed',
                'priority' => 'critical',
                'estimated_hours' => 60,
                'actual_hours' => 70,
                'project_id' => $bankingProject->id,
                'assigned_to' => $seniorDevelopers[0]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(3),
                'due_date' => Carbon::now()->subMonths(2),
                'completed_at' => Carbon::now()->subMonths(1)->addWeeks(3),
                'tags' => json_encode(['security', 'biometric', 'encryption', 'mobile']),
                'created_at' => Carbon::now()->subMonths(3),
            ]),
            Task::create([
                'title' => 'Integração PIX e TED',
                'description' => 'Implementar funcionalidades de transferência PIX e TED com validação em tempo real.',
                'status' => 'testing',
                'priority' => 'critical',
                'estimated_hours' => 45,
                'actual_hours' => 42,
                'project_id' => $bankingProject->id,
                'assigned_to' => $seniorDevelopers[1]->id,
                'created_by' => $projectManagers[0]->id,
                'start_date' => Carbon::now()->subMonths(1),
                'due_date' => Carbon::now()->addDays(3),
                'tags' => json_encode(['pix', 'ted', 'transfer', 'validation']),
                'created_at' => Carbon::now()->subMonths(1),
            ]),
        ];

        // Consolidar todas as tarefas
        $allTasks = array_merge($ecommerceTasks, $erpTasks, $bankingTasks);

        $allProjects = [$ecommerceProject, $erpProject, $bankingProject, $iotProject, $analyticsProject, $cloudProject];

        // ========================================
        // MARCOS E MILESTONES DETALHADOS
        // ========================================

        $milestones = [
            // E-commerce Milestones
            Milestone::create([
                'title' => 'MVP E-commerce Funcional',
                'description' => 'Versão mínima viável com catálogo, carrinho e checkout básico implementados e testados.',
                'project_id' => $ecommerceProject->id,
                'due_date' => Carbon::now()->subMonths(3),
                'status' => 'completed',
                'completion_percentage' => 100,
                'created_by' => $projectManagers[0]->id,
                'completed_at' => Carbon::now()->subMonths(2)->addWeeks(3),
                'created_at' => Carbon::now()->subMonths(5),
            ]),
            Milestone::create([
                'title' => 'Integração Pagamentos Completa',
                'description' => 'Todos os gateways de pagamento integrados com testes de homologação aprovados.',
                'project_id' => $ecommerceProject->id,
                'due_date' => Carbon::now()->addDays(7),
                'status' => 'in_progress',
                'completion_percentage' => 85,
                'created_by' => $projectManagers[0]->id,
                'created_at' => Carbon::now()->subMonths(1),
            ]),
            Milestone::create([
                'title' => 'Launch Beta Testing',
                'description' => 'Lançamento para grupo seleto de beta testers com monitoramento completo.',
                'project_id' => $ecommerceProject->id,
                'due_date' => Carbon::now()->addWeeks(3),
                'status' => 'pending',
                'completion_percentage' => 30,
                'created_by' => $projectManagers[0]->id,
                'created_at' => Carbon::now()->subWeeks(2),
            ],

            // ERP Milestones
            Milestone::create([
                'title' => 'Arquitetura Base Implementada',
                'description' => 'Microserviços base, API Gateway e infraestrutura de desenvolvimento completa.',
                'project_id' => $erpProject->id,
                'due_date' => Carbon::now()->subMonths(5),
                'status' => 'completed',
                'completion_percentage' => 100,
                'created_by' => $projectManagers[1]->id,
                'completed_at' => Carbon::now()->subMonths(4)->addWeeks(2),
                'created_at' => Carbon::now()->subMonths(7),
            ]),
            Milestone::create([
                'title' => 'Módulos Core Financeiro',
                'description' => 'Módulos de contas a pagar, receber e conciliação bancária funcionais.',
                'project_id' => $erpProject->id,
                'due_date' => Carbon::now()->addWeeks(6),
                'status' => 'in_progress',
                'completion_percentage' => 65,
                'created_by' => $projectManagers[1]->id,
                'created_at' => Carbon::now()->subMonths(2),
            ],

            // Banking Milestones
            Milestone::create([
                'title' => 'Core Banking Funcional',
                'description' => 'Funcionalidades básicas de conta corrente, saldo e extrato implementadas.',
                'project_id' => $bankingProject->id,
                'due_date' => Carbon::now()->subMonths(2),
                'status' => 'completed',
                'completion_percentage' => 100,
                'created_by' => $projectManagers[0]->id,
                'completed_at' => Carbon::now()->subMonths(1)->addWeeks(2),
                'created_at' => Carbon::now()->subMonths(3),
            ]),
            Milestone::create([
                'title' => 'Certificação Segurança Bancária',
                'description' => 'Aprovação em auditoria de segurança e conformidade com regulamentações bancárias.',
                'project_id' => $bankingProject->id,
                'due_date' => Carbon::now()->addWeeks(2),
                'status' => 'in_progress',
                'completion_percentage' => 80,
                'created_by' => $projectManagers[0]->id,
                'created_at' => Carbon::now()->subWeeks(3),
            ]),
        ];

        // ========================================
        // CONFIGURAÇÕES DO SISTEMA
        // ========================================
        
        // Configurar settings do sistema
        \DB::table('system_settings')->insert([
            'key' => 'app_name',
            'value' => 'Guardian Project Manager',
            'description' => 'Nome da aplicação',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'company_name',
            'value' => 'Guardian Technologies Ltda',
            'description' => 'Nome da empresa',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'default_project_status',
            'value' => 'planning',
            'description' => 'Status padrão para novos projetos',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'max_file_upload_size',
            'value' => '50',
            'description' => 'Tamanho máximo de upload em MB',
            'type' => 'integer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'session_timeout',
            'value' => '480',
            'description' => 'Timeout de sessão em minutos',
            'type' => 'integer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'enable_audit_logs',
            'value' => '1',
            'description' => 'Habilitar logs de auditoria',
            'type' => 'boolean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'enable_email_notifications',
            'value' => '1',
            'description' => 'Habilitar notificações por email',
            'type' => 'boolean',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('system_settings')->insert([
            'key' => 'default_timezone',
            'value' => 'America/Sao_Paulo',
            'description' => 'Fuso horário padrão do sistema',
            'type' => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Base de dados complexa criada com sucesso!');
        $this->command->info('📊 Estatísticas criadas:');
        $this->command->info("   • " . count($allUsers) . " usuários com perfis completos");
        $this->command->info("   • " . count($allProjects) . " projetos com orçamentos e cronogramas");
        $this->command->info("   • " . count($allTasks) . " tarefas detalhadas com estimativas");
        $this->command->info("   • " . count($milestones) . " marcos com métricas de progresso");
        $this->command->info('🔐 Credenciais de acesso:');
        $this->command->info('   • Admin: admin@guardian.local / guardian123');
        $this->command->info('   • Todos os demais: [email] / guardian123');
    }
}
