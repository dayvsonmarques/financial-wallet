@extends('layouts.app')

@section('title', 'Painel - Carteira Financeira')

@section('content')
<div class="card">
    <h2 style="margin-bottom: 20px;">Painel</h2>
    
    <div class="balance">
        Saldo: R$ {{ number_format($user->balance, 2, ',', '.') }}
    </div>
    
    <div class="grid" style="margin-top: 30px;">
        <a href="/transactions/transfer" class="card" style="text-decoration: none; text-align: center;">
            <h3>💸 Transferir</h3>
            <p style="color: #666; margin-top: 10px;">Enviar dinheiro para outro usuário</p>
        </a>
        
        <a href="/transactions/deposit" class="card" style="text-decoration: none; text-align: center;">
            <h3>💰 Depositar</h3>
            <p style="color: #666; margin-top: 10px;">Adicionar dinheiro à sua conta</p>
        </a>
        
        <a href="/transactions" class="card" style="text-decoration: none; text-align: center;">
            <h3>📋 Transações</h3>
            <p style="color: #666; margin-top: 10px;">Ver histórico de transações</p>
        </a>
    </div>
    
    @if($transactions->count() > 0)
        <div class="card mt-20">
            <h3 style="margin-bottom: 15px;">Transações Recentes</h3>
            @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="transaction-type {{ $transaction->type }}">
                                @if($transaction->type === 'transfer')
                                    Transferência
                                @elseif($transaction->type === 'deposit')
                                    Depósito
                                @elseif($transaction->type === 'reversal')
                                    Estorno
                                @else
                                    {{ ucfirst($transaction->type) }}
                                @endif
                            </span>
                            <span style="margin-left: 10px; color: #666;">
                                @if($transaction->type === 'transfer')
                                    @if($transaction->from_user_id === $user->id)
                                        Para: {{ $transaction->toUser->name ?? 'N/A' }}
                                    @else
                                        De: {{ $transaction->fromUser->name ?? 'N/A' }}
                                    @endif
                                @else
                                    {{ $transaction->description ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="transaction-amount {{ $transaction->from_user_id === $user->id ? 'negative' : 'positive' }}">
                                {{ $transaction->from_user_id === $user->id ? '-' : '+' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div style="margin-top: 5px; font-size: 12px; color: #999;">
                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                        @if($transaction->status === 'reversed')
                            <span style="color: #dc3545;">(Estornado)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

