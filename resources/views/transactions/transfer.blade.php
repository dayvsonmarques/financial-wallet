@extends('layouts.app')

@section('title', 'Transferir - Carteira Financeira')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;">Transferir Dinheiro</h2>
    <p style="color: #666; margin-bottom: 20px;">Seu saldo atual: <strong>R$ {{ number_format(auth()->user()->balance, 2, ',', '.') }}</strong></p>

    <form method="POST" action="/transactions/transfer">
        @csrf

        <div class="form-group">
            <label for="to_user_id">Transferir para</label>
            <select id="to_user_id" name="to_user_id" required>
                <option value="">Selecione um usuário</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('to_user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Valor</label>
            <input type="text" id="amount" name="amount" value="{{ old('amount') }}" placeholder="R$ 0,00" required>
        </div>

        <div class="form-group">
            <label for="description">Descrição (opcional)</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn">Transferir</button>
            <a href="/dashboard" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

