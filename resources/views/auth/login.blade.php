@extends('layouts.app')

@section('title', 'Login - Guardian')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><i class="fas fa-shield-alt me-2"></i>Guardian</h4>
                    <p class="mb-0">Sistema de Gerenciamento de Projetos</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail ou Login</label>
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autofocus 
                                   placeholder="Digite seu e-mail ou login">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar de mim
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </button>
                        </div>
                    </form>

                    <hr>
                    <div class="text-center">
                        <p class="mb-0">Não tem uma conta? <a href="{{ route('register') }}">Registre-se aqui</a></p>
                    </div>

                    <!-- Usuários de demonstração -->
                    <div class="mt-4">
                        <h6 class="text-muted">Usuários de demonstração:</h6>
                        <small class="text-muted">
                            <strong>Admin:</strong> admin@guardian.local<br>
                            <strong>Gerente:</strong> joao@guardian.local<br>
                            <strong>Desenvolvedor:</strong> pedro@guardian.local<br>
                            <strong>Senha para todos:</strong> guardian123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
