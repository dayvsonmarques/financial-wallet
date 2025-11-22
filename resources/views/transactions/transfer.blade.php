@extends('layouts.app')

@section('title', 'Transfer - Financial Wallet')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Transfer Money</h2>
    <p style="color: #666; margin-bottom: 20px;">Your current balance: <strong>R$ {{ number_format(auth()->user()->balance, 2, ',', '.') }}</strong></p>
    
    <form method="POST" action="/transactions/transfer">
        @csrf
        
        <div class="form-group">
            <label for="to_user_id">Transfer to</label>
            <select id="to_user_id" name="to_user_id" required>
                <option value="">Select a user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn">Transfer</button>
            <a href="/dashboard" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

