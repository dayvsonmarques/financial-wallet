@extends('layouts.app')

@section('title', 'Transações - Carteira Financeira')

@section('content')
<div class="card">
    <h2 class="mb-20">Histórico de Transações</h2>

    @if($transactions->count() > 0)
        <div>
            @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div class="flex-between">
                        <div class="flex-1">
                            <div class="flex-align gap-10 mb-5">
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
                                <span class="fw-500">
                                    @if($transaction->type === 'transfer')
                                        @if($transaction->from_user_id === auth()->id())
                                            Para: {{ $transaction->toUser->name ?? 'N/A' }}
                                        @else
                                            De: {{ $transaction->fromUser->name ?? 'N/A' }}
                                        @endif
                                    @else
                                        {{ $transaction->description ?? 'Depósito' }}
                                    @endif
                                </span>
                            </div>
                            @if($transaction->description && $transaction->type !== 'deposit')
                                <div class="text-muted small mt-5">{{ $transaction->description }}</div>
                            @endif
                            <div class="meta">
                                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                                @if($transaction->status === 'reversed')
                                    <span class="text-danger">• Estornado</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="transaction-amount {{ $transaction->from_user_id === auth()->id() ? 'negative' : 'positive' }}">
                                {{ $transaction->from_user_id === auth()->id() ? '-' : '+' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </div>
                            @if($transaction->canBeReversed() && $transaction->from_user_id === auth()->id())
                                <form method="POST" action="/transactions/{{ $transaction->id }}/reverse" class="mt-10 inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Tem certeza que deseja estornar esta transação?')">Estornar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-20">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Nenhuma transação encontrada.</p>
            <a href="/transactions/deposit" class="btn btn-success mt-20">Faça seu primeiro depósito</a>
        </div>
    @endif
</div>
@endsection

