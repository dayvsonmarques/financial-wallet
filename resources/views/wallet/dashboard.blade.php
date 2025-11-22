@extends('layouts.app')

@section('title', 'Dashboard - Financial Wallet')

@section('content')
<div class="card">
    <h2 style="margin-bottom: 20px;">Dashboard</h2>
    
    <div class="balance">
        Balance: R$ {{ number_format($user->balance, 2, ',', '.') }}
    </div>
    
    <div class="grid" style="margin-top: 30px;">
        <a href="/transactions/transfer" class="card" style="text-decoration: none; text-align: center;">
            <h3>💸 Transfer</h3>
            <p style="color: #666; margin-top: 10px;">Send money to another user</p>
        </a>
        
        <a href="/transactions/deposit" class="card" style="text-decoration: none; text-align: center;">
            <h3>💰 Deposit</h3>
            <p style="color: #666; margin-top: 10px;">Add money to your account</p>
        </a>
        
        <a href="/transactions" class="card" style="text-decoration: none; text-align: center;">
            <h3>📋 Transactions</h3>
            <p style="color: #666; margin-top: 10px;">View transaction history</p>
        </a>
    </div>
    
    @if($transactions->count() > 0)
        <div class="card mt-20">
            <h3 style="margin-bottom: 15px;">Recent Transactions</h3>
            @foreach($transactions as $transaction)
                <div class="transaction-item">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="transaction-type {{ $transaction->type }}">{{ ucfirst($transaction->type) }}</span>
                            <span style="margin-left: 10px; color: #666;">
                                @if($transaction->type === 'transfer')
                                    @if($transaction->from_user_id === $user->id)
                                        To: {{ $transaction->toUser->name ?? 'N/A' }}
                                    @else
                                        From: {{ $transaction->fromUser->name ?? 'N/A' }}
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
                            <span style="color: #dc3545;">(Reversed)</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

