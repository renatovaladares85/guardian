<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian - Assistente de Configuração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .setup-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .setup-header { background: #2c3e50; color: white; border-radius: 15px 15px 0 0; }
        .option-card { transition: all 0.3s; cursor: pointer; border: 2px solid transparent; }
        .option-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .option-card.selected { border-color: #007bff; background: #f8f9fa; }
        .mode-icon { font-size: 3rem; margin-bottom: 1rem; }
        .test-mode { color: #17a2b8; }
        .production-mode { color: #28a745; }
        .custom-mode { color: #6f42c1; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="setup-card">
                    <div class="setup-header p-4 text-center">
                        <h1><i class="fas fa-shield-alt me-3"></i>Guardian</h1>
                        <p class="mb-0">Sistema de Gerenciamento de Projetos</p>
                        <small>Assistente de Configuração Inicial</small>
                    </div>

                    <div class="p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('setup.process') }}" method="POST" id="setupForm">
                            @csrf
                            
                            <div class="text-center mb-4">
                                <h3>Bem-vindo ao Guardian!</h3>
                                <p class="text-muted">Vamos configurar seu sistema. Escolha uma das opções abaixo:</p>
                            </div>

                            <div class="row mb-4">
                                <!-- Modo Teste -->
                                <div class="col-md-4 mb-3">
                                    <div class="option-card card h-100 text-center p-3" onclick="selectMode('test')">
                                        <input type="radio" name="setup_mode" value="test" id="mode_test" class="d-none">
                                        <div class="mode-icon test-mode">
                                            <i class="fas fa-flask"></i>
                                        </div>
                                        <h5>Modo Teste</h5>
                                        <p class="text-muted small">MVP com dados de exemplo para demonstração e testes</p>
                                        <div class="mt-auto">
                                            <span class="badge bg-info">Recomendado</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modo Produção -->
                                <div class="col-md-4 mb-3">
                                    <div class="option-card card h-100 text-center p-3" onclick="selectMode('production')">
                                        <input type="radio" name="setup_mode" value="production" id="mode_production" class="d-none">
                                        <div class="mode-icon production-mode">
                                            <i class="fas fa-rocket"></i>
                                        </div>
                                        <h5>Modo Produção</h5>
                                        <p class="text-muted small">Sistema limpo e otimizado para uso em ambiente real</p>
                                        <div class="mt-auto">
                                            <span class="badge bg-success">Produção</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modo Personalizado -->
                                <div class="col-md-4 mb-3">
                                    <div class="option-card card h-100 text-center p-3" onclick="selectMode('custom')">
                                        <input type="radio" name="setup_mode" value="custom" id="mode_custom" class="d-none">
                                        <div class="mode-icon custom-mode">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <h5>Personalizado</h5>
                                        <p class="text-muted small">Configuração passo a passo com opções avançadas</p>
                                        <div class="mt-auto">
                                            <span class="badge bg-purple">Avançado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações para Modo Produção -->
                            <div id="production-config" class="d-none">
                                <hr>
                                <h5><i class="fas fa-user-shield me-2"></i>Configuração do Administrador</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="admin_name" class="form-label">Nome do Administrador</label>
                                            <input type="text" class="form-control" id="admin_name" name="admin_name" value="{{ old('admin_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="admin_email" class="form-label">E-mail do Administrador</label>
                                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="{{ old('admin_email') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Senha do Administrador</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password">
                                    <div class="form-text">Mínimo 8 caracteres</div>
                                </div>
                            </div>

                            <!-- Configurações para Modo Personalizado -->
                            <div id="custom-config" class="d-none">
                                <hr>
                                <h5><i class="fas fa-cogs me-2"></i>Configurações Personalizadas</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_demo_data" name="include_demo_data" checked>
                                        <label class="form-check-label" for="include_demo_data">
                                            <strong>Incluir dados de demonstração</strong>
                                            <small class="d-block text-muted">Projetos e tarefas de exemplo para testes</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable_security" name="enable_security">
                                        <label class="form-check-label" for="enable_security">
                                            <strong>Recursos de segurança avançados</strong>
                                            <small class="d-block text-muted">2FA, logs de acesso, bloqueio por tentativas</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable_audit" name="enable_audit" checked>
                                        <label class="form-check-label" for="enable_audit">
                                            <strong>Auditoria completa</strong>
                                            <small class="d-block text-muted">Rastreamento de todas as ações dos usuários</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Configuração do administrador para modo custom sem demo data -->
                                <div id="custom-admin-config">
                                    <h6><i class="fas fa-user-shield me-2"></i>Administrador do Sistema</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_admin_name" class="form-label">Nome</label>
                                                <input type="text" class="form-control" id="custom_admin_name" name="admin_name" value="{{ old('admin_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_admin_email" class="form-label">E-mail</label>
                                                <input type="email" class="form-control" id="custom_admin_email" name="admin_email" value="{{ old('admin_email') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="custom_admin_password" class="form-label">Senha</label>
                                        <input type="password" class="form-control" id="custom_admin_password" name="admin_password">
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" id="setupButton" disabled>
                                    <i class="fas fa-rocket me-2"></i>Configurar Guardian
                                </button>
                            </div>
                        </form>

                        <!-- Informações sobre cada modo -->
                        <div class="mt-4">
                            <div class="alert alert-info" id="test-info" style="display: none;">
                                <h6><i class="fas fa-info-circle me-2"></i>Modo Teste incluirá:</h6>
                                <ul class="mb-0">
                                    <li>5 usuários de exemplo com diferentes funções</li>
                                    <li>2 projetos com tarefas e marcos já configurados</li>
                                    <li>Dados realistas para demonstração</li>
                                    <li>Acesso: <code>admin@guardian.local</code> senha: <code>guardian123</code></li>
                                </ul>
                            </div>

                            <div class="alert alert-success" id="production-info" style="display: none;">
                                <h6><i class="fas fa-info-circle me-2"></i>Modo Produção incluirá:</h6>
                                <ul class="mb-0">
                                    <li>Sistema limpo sem dados de exemplo</li>
                                    <li>Configurações otimizadas para performance</li>
                                    <li>Recursos de segurança ativados</li>
                                    <li>Cache otimizado para produção</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning" id="custom-info" style="display: none;">
                                <h6><i class="fas fa-info-circle me-2"></i>Modo Personalizado permite:</h6>
                                <ul class="mb-0">
                                    <li>Escolher quais recursos ativar</li>
                                    <li>Decidir sobre dados de demonstração</li>
                                    <li>Configurar segurança conforme necessário</li>
                                    <li>Personalizar configurações de auditoria</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectMode(mode) {
            // Remover seleção anterior
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Selecionar novo modo
            document.getElementById('mode_' + mode).checked = true;
            document.querySelector(`[onclick="selectMode('${mode}')"]`).classList.add('selected');
            
            // Habilitar botão
            document.getElementById('setupButton').disabled = false;
            
            // Mostrar/ocultar configurações
            document.getElementById('production-config').classList.add('d-none');
            document.getElementById('custom-config').classList.add('d-none');
            
            // Ocultar todas as informações
            document.querySelectorAll('[id$="-info"]').forEach(info => {
                info.style.display = 'none';
            });
            
            if (mode === 'production') {
                document.getElementById('production-config').classList.remove('d-none');
                document.getElementById('production-info').style.display = 'block';
            } else if (mode === 'custom') {
                document.getElementById('custom-config').classList.remove('d-none');
                document.getElementById('custom-info').style.display = 'block';
                toggleCustomAdminConfig();
            } else if (mode === 'test') {
                document.getElementById('test-info').style.display = 'block';
            }
        }
        
        function toggleCustomAdminConfig() {
            const includeDemoData = document.getElementById('include_demo_data').checked;
            const adminConfig = document.getElementById('custom-admin-config');
            
            if (includeDemoData) {
                adminConfig.style.display = 'none';
            } else {
                adminConfig.style.display = 'block';
            }
        }
        
        // Event listener para checkbox de dados de demonstração
        document.getElementById('include_demo_data')?.addEventListener('change', toggleCustomAdminConfig);
    </script>
</body>
</html>
