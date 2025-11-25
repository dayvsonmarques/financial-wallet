@extends('layouts.app')

@section('title', 'Entrar - Carteira Financeira')

@section('content')
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Entrar</h2>

    <form method="POST" action="/login">
        @csrf

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="remember" name="remember" style="margin: 0;">
            <label for="remember" style="margin: 0; cursor: pointer;">Lembrar-me</label>
        </div>

        <button type="submit" class="btn" style="width: 100%;">Entrar</button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="/register">Não tem uma conta? Cadastre-se</a>
    </div>
</div>
@endsection

