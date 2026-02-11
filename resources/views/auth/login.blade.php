@extends('layouts.app')

@section('title', 'Login – Restaurante')

@section('content')
<div class="login-page">
    <div class="login-bg">
        <div class="login-bg-gradient"></div>
        <div class="login-bg-grid"></div>
        <div class="login-bg-glow login-glow-1"></div>
        <div class="login-bg-glow login-glow-2"></div>
    </div>
    <div class="login-container">
        <div class="login-card">
            <div class="login-card-inner">
                <div class="login-brand">
                    <div class="login-brand-icon">
                        <i class="bi bi-cup-hot-fill"></i>
                    </div>
                    <h1 class="login-title">Restaurante</h1>
                    <p class="login-subtitle">Entre com seu e-mail e senha para continuar</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf
                    <div class="login-field">
                        <label for="email" class="login-label">E-mail</label>
                        <div class="login-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email"
                                   class="login-input @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="seu@email.com"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <span class="login-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="login-field">
                        <label for="password" class="login-label">Senha</label>
                        <div class="login-input-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password"
                                   class="login-input @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   required>
                        </div>
                        @error('password')
                            <span class="login-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="login-remember">
                        <input type="checkbox" class="login-checkbox" id="remember" name="remember">
                        <label class="login-checkbox-label" for="remember">Lembrar-me</label>
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Entrar</span>
                    </button>
                </form>

                <div class="login-credenciais">
                    <strong>Contas de teste</strong>
                    <p>Admin: admin@test.com · Gerente: manager@test.com · Usuário: user@test.com</p>
                    <small>Senha: 123456</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
