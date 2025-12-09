@extends('layouts.app')

@section('title', 'Painel - Carteira Financeira')

@section('content')
<div class="card">
    <h2 class="mb-20">Painel</h2>

    <div class="balance">
        Saldo: R$ {{ number_format($user->balance, 2, ',', '.') }}
    </div>

    <div class="grid mt-30">
        <a href="/transactions/transfer" class="card text-center no-underline">
            <h3>💸 Transferir</h3>
            <p class="text-muted mt-10">Enviar dinheiro para outro usuário</p>
        </a>

        <a href="/transactions/deposit" class="card text-center no-underline">
            <h3>💰 Depositar</h3>
            <p class="text-muted mt-10">Adicionar dinheiro à sua conta</p>
        </a>

        <a href="/transactions" class="card text-center no-underline">
            <h3>📋 Transações</h3>
            <p class="text-muted mt-10">Ver histórico de transações</p>
        </a>
    </div>

    @if($transactions->count() > 0)
        <div class="card mt-20">
            <h3 class="mb-15">Transações Recentes</h3>
            @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div class="flex-between">
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
                            <span class="ml-10 text-muted">
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
                    <div class="meta">
                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                        @if($transaction->status === 'reversed')
                            <span class="text-danger">(Estornado)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

