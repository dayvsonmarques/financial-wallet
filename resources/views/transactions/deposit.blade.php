@extends('layouts.app')

@section('title', 'Depositar - Carteira Financeira')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Depositar Dinheiro</h2>
    <p style="color: #666; margin-bottom: 20px;">Seu saldo atual: <strong>R$ {{ number_format(auth()->user()->balance, 2, ',', '.') }}</strong></p>
    
    <form method="POST" action="/transactions/deposit">
        @csrf
        
        <div class="form-group">
            <label for="amount">Valor</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição (opcional)</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Depositar</button>
            <a href="/dashboard" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

