@extends('layouts.app')

@section('title', 'Entrar - Carteira Financeira')

@section('content')
<div class="card card-auth">
    <h2 class="mb-20">Entrar</h2>

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

        <div class="form-group flex-align gap-8">
            <input type="checkbox" id="remember" name="remember" class="m-0">
            <label for="remember" class="m-0 pointer">Lembrar-me</label>
        </div>

        <button type="submit" class="btn btn-block">Entrar</button>
    </form>

    <div class="mt-20 text-center">
        <a href="/register">Não tem uma conta? Cadastre-se</a>
    </div>
</div>
@endsection

