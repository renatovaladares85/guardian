<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian - Sistema de Gerenciamento de Projetos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
        }
        .welcome-card { 
            background: rgba(255,255,255,0.95); 
            backdrop-filter: blur(10px); 
            border-radius: 15px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        }
        .hero-icon { 
            font-size: 4rem; 
            color: #667eea; 
            margin-bottom: 1rem; 
        }
        .feature-icon { 
            font-size: 2rem; 
            color: #667eea; 
            margin-bottom: 0.5rem; 
        }
        .btn-get-started {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .btn-get-started:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="welcome-card p-5">
                    <div class="text-center mb-5">
                        <div class="hero-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h1 class="display-4 fw-bold mb-3">Guardian</h1>
                        <p class="lead text-muted">Sistema de Gerenciamento de Projetos</p>
                        <p class="text-muted">Organize, gerencie e acompanhe seus projetos com eficiência</p>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-4 text-center mb-4">
                            <div class="feature-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h5>Gestão de Projetos</h5>
                            <p class="text-muted small">Organize projetos, defina marcos e acompanhe o progresso em tempo real.</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <div class="feature-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h5>Controle de Tarefas</h5>
                            <p class="text-muted small">Distribua tarefas, defina prioridades e monitore o andamento da equipe.</p>
                        </div>
                        <div class="col-md-4 text-center mb-4">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Gestão de Equipes</h5>
                            <p class="text-muted small">Gerencie usuários, defina funções e controle o acesso às informações.</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i>Recursos Inclusos:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Dashboard intuitivo</li>
                                <li><i class="fas fa-check text-success me-2"></i>Relatórios em tempo real</li>
                                <li><i class="fas fa-check text-success me-2"></i>Sistema de notificações</li>
                                <li><i class="fas fa-check text-success me-2"></i>Controle de acesso</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-shield-alt text-primary me-2"></i>Segurança:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Autenticação segura</li>
                                <li><i class="fas fa-check text-success me-2"></i>Auditoria de ações</li>
                                <li><i class="fas fa-check text-success me-2"></i>Backup automático</li>
                                <li><i class="fas fa-check text-success me-2"></i>Criptografia de dados</li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('setup.wizard') }}" class="btn btn-primary btn-get-started me-3">
                            <i class="fas fa-rocket me-2"></i>Começar Agora
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Versão 1.0 MVP - Pronto para produção
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
