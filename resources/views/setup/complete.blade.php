<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian - Configuração Concluída</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .success-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .success-header { background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 15px 15px 0 0; }
        .checkmark { font-size: 5rem; color: #28a745; animation: checkmark 0.8s ease-in-out; }
        @keyframes checkmark { 0% { transform: scale(0); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .info-box { background: #f8f9fa; border-left: 4px solid #007bff; padding: 1rem; margin: 1rem 0; }
        .access-btn { transition: all 0.3s; }
        .access-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="success-card">
                    <div class="success-header p-4 text-center">
                        <h1><i class="fas fa-shield-alt me-3"></i>Guardian</h1>
                        <p class="mb-0">Configuração Concluída!</p>
                    </div>

                    <div class="p-4 text-center">
                        <div class="checkmark mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <h2 class="text-success mb-3">Sistema Configurado com Sucesso!</h2>
                        
                        @if($setupMode === 'test')
                            <div class="info-box">
                                <h5><i class="fas fa-flask me-2"></i>Modo Teste Ativado</h5>
                                <p class="mb-2">Seu sistema Guardian foi configurado com dados de demonstração.</p>
                                
                                <h6 class="mt-3">👥 Usuários de Teste (senha: <code>guardian123</code>):</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr><td><code>admin@guardian.local</code></td><td><span class="badge bg-danger">Super Admin</span></td></tr>
                                            <tr><td><code>joao@guardian.local</code></td><td><span class="badge bg-primary">Gerente</span></td></tr>
                                            <tr><td><code>maria@guardian.local</code></td><td><span class="badge bg-info">Líder</span></td></tr>
                                            <tr><td><code>pedro@guardian.local</code></td><td><span class="badge bg-success">Desenvolvedor</span></td></tr>
                                            <tr><td><code>ana@guardian.local</code></td><td><span class="badge bg-success">Desenvolvedora</span></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Dica:</strong> O sistema já inclui 2 projetos com tarefas de exemplo para você explorar!
                                </div>
                            </div>

                        @elseif($setupMode === 'production')
                            <div class="info-box">
                                <h5><i class="fas fa-rocket me-2"></i>Modo Produção Ativado</h5>
                                <p class="mb-2">Seu sistema Guardian está otimizado e pronto para uso em produção.</p>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Use as credenciais do administrador que você criou para fazer o primeiro login.
                                </div>
                                
                                <div class="mt-3">
                                    <h6>✅ Recursos Ativados:</h6>
                                    <ul class="text-start">
                                        <li>Otimizações de performance</li>
                                        <li>Cache de configuração</li>
                                        <li>Recursos de segurança</li>
                                        <li>Auditoria completa</li>
                                    </ul>
                                </div>
                            </div>

                        @elseif($setupMode === 'custom')
                            <div class="info-box">
                                <h5><i class="fas fa-cogs me-2"></i>Configuração Personalizada</h5>
                                <p class="mb-2">Seu sistema Guardian foi configurado conforme suas preferências.</p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    As configurações escolhidas foram aplicadas com sucesso.
                                </div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <h5>🌐 Acesso ao Sistema</h5>
                            <div class="row mt-3">
                                <div class="col-md-6 mb-2">
                                    <a href="/" class="btn btn-primary btn-lg w-100 access-btn">
                                        <i class="fas fa-home me-2"></i>Acessar Guardian
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="http://localhost:8025" target="_blank" class="btn btn-outline-secondary btn-lg w-100 access-btn">
                                        <i class="fas fa-envelope me-2"></i>MailHog
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if($setupMode === 'test')
                            <div class="mt-4">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-book me-2"></i>Próximos Passos</h6>
                                    <ol class="text-start mb-0">
                                        <li>Faça login com qualquer usuário de teste</li>
                                        <li>Explore o dashboard e funcionalidades</li>
                                        <li>Teste criação de projetos e tarefas</li>
                                        <li>Experimente diferentes níveis de acesso</li>
                                        <li>Consulte o arquivo <code>TESTE-MVP.md</code> para roteiro completo</li>
                                    </ol>
                                </div>
                            </div>
                        @else
                            <div class="mt-4">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-rocket me-2"></i>Próximos Passos</h6>
                                    <ol class="text-start mb-0">
                                        <li>Faça login com as credenciais do administrador</li>
                                        <li>Configure usuários e equipes</li>
                                        <li>Crie seus primeiros projetos</li>
                                        <li>Defina processos e fluxos de trabalho</li>
                                        <li>Configure backup e monitoramento</li>
                                    </ol>
                                </div>
                            </div>
                        @endif

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Configuração realizada em {{ now()->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect após 10 segundos se for modo teste
        @if($setupMode === 'test')
            setTimeout(function() {
                if (confirm('Deseja ser redirecionado automaticamente para o sistema?')) {
                    window.location.href = '/';
                }
            }, 10000);
        @endif
    </script>
</body>
</html>
