@extends('layouts.app')

@section('title', 'Transações - Carteira Financeira')

@section('content')
<div class="card">
    <h2 style="margin-bottom: 20px;">Histórico de Transações</h2>
    
    @if($transactions->count() > 0)
        <div>
            @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
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
                                <span style="font-weight: 500;">
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
                                <div style="color: #666; font-size: 14px; margin-top: 5px;">{{ $transaction->description }}</div>
                            @endif
                            <div style="font-size: 12px; color: #999; margin-top: 5px;">
                                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                                @if($transaction->status === 'reversed')
                                    <span style="color: #dc3545;">• Estornado</span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="transaction-amount {{ $transaction->from_user_id === auth()->id() ? 'negative' : 'positive' }}">
                                {{ $transaction->from_user_id === auth()->id() ? '-' : '+' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </div>
                            @if($transaction->canBeReversed() && $transaction->from_user_id === auth()->id())
                                <form method="POST" action="/transactions/{{ $transaction->id }}/reverse" style="margin-top: 10px; display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Tem certeza que deseja estornar esta transação?')">Estornar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="margin-top: 20px;">
            {{ $transactions->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Nenhuma transação encontrada.</p>
            <a href="/transactions/deposit" class="btn btn-success mt-20">Faça seu primeiro depósito</a>
        </div>
    @endif
</div>
@endsection

