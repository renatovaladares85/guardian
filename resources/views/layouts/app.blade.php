<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guardian - Sistema de Gerenciamento de Projetos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { 
            min-height: 100vh; 
            background: #2c3e50; 
        }
        .sidebar .nav-link { 
            color: #ecf0f1; 
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            background: #34495e; 
            color: #fff;
        }
        .main-content { 
            background: #f8f9fa; 
            min-height: 100vh; 
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .priority-high { border-left: 4px solid #dc3545; }
        .priority-medium { border-left: 4px solid #ffc107; }
        .priority-low { border-left: 4px solid #28a745; }
        .priority-critical { border-left: 4px solid #6f42c1; }
        .priority-urgent { border-left: 4px solid #fd7e14; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-shield-alt me-2"></i>Guardian
                        </h4>
                        <small class="text-light">v1.0 MVP</small>
                    </div>
                    
                    @auth
                    <div class="text-center mb-3">
                        <div class="text-light">
                            <small>Olá, {{ Auth::user()->name }}</small><br>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </div>
                    </div>
                    @endauth

                    <nav class="nav flex-column">
                        @auth
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                            <i class="fas fa-project-diagram me-2"></i>Projetos
                        </a>
                        <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                            <i class="fas fa-tasks me-2"></i>Tarefas
                        </a>
                        <hr class="text-light">
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i>Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </form>
                        @else
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </a>
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-2"></i>Registrar
                        </a>
                        @endauth
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <div class="main-content p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
