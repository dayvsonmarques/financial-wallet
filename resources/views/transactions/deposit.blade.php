@extends('layouts.app')

@section('title', 'Deposit - Financial Wallet')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Deposit Money</h2>
    <p style="color: #666; margin-bottom: 20px;">Your current balance: <strong>R$ {{ number_format(auth()->user()->balance, 2, ',', '.') }}</strong></p>
    
    <form method="POST" action="/transactions/deposit">
        @csrf
        
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Deposit</button>
            <a href="/dashboard" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

