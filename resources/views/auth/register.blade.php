@extends('layouts.app')

@section('title', 'Cadastrar - Carteira Financeira')

@section('content')
<div class="card card-auth">
    <h2 class="mb-20">Cadastrar</h2>

    <form method="POST" action="/register">
        @csrf

        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Senha</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-success btn-block">Cadastrar</button>
    </form>

    <div class="mt-20 text-center">
        <a href="/login">Já tem uma conta? Entrar</a>
    </div>
</div>
@endsection

