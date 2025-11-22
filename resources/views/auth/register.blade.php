@extends('layouts.app')

@section('title', 'Cadastrar - Carteira Financeira')

@section('content')
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Cadastrar</h2>
    
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
        
        <button type="submit" class="btn btn-success" style="width: 100%;">Cadastrar</button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="/login">Já tem uma conta? Entrar</a>
    </div>
</div>
@endsection

