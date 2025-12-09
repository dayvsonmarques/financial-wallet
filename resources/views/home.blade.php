@extends('layouts.app')

@section('title', 'Início - Carteira Financeira')

@section('content')
<div class="card text-center">
    <h1 class="hero-title">💰 Carteira Financeira</h1>
    <p class="hero-subtitle">
        Gerencie seu dinheiro com facilidade. Transfira, deposite e acompanhe suas transações.
    </p>

    @auth
        <div class="mt-30">
            <a href="/dashboard" class="btn">Ir para o Painel</a>
        </div>
    @else
        <div class="features">
            <a href="/login" class="btn">Entrar</a>
            <a href="/register" class="btn btn-success">Cadastrar</a>
        </div>
    @endauth
</div>
@endsection

