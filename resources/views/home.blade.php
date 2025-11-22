@extends('layouts.app')

@section('title', 'Início - Carteira Financeira')

@section('content')
<div class="card text-center">
    <h1 style="font-size: 36px; margin-bottom: 20px;">💰 Carteira Financeira</h1>
    <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
        Gerencie seu dinheiro com facilidade. Transfira, deposite e acompanhe suas transações.
    </p>
    
    @auth
        <div style="margin-top: 30px;">
            <a href="/dashboard" class="btn">Ir para o Painel</a>
        </div>
    @else
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
            <a href="/login" class="btn">Entrar</a>
            <a href="/register" class="btn btn-success">Cadastrar</a>
        </div>
    @endauth
</div>
@endsection

