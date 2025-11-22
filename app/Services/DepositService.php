<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function deposit(User $user, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new TransactionException('O valor deve ser maior que zero');
        }

        return DB::transaction(function () use ($user, $amount, $description) {
            $user->lockForUpdate();

            $transaction = Transaction::create([
                'from_user_id' => null,
                'to_user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'completed',
                'description' => $description ?? 'Depósito',
            ]);

            // Increment balance (works even if balance is negative)
            $user->increment('balance', $amount);

            return $transaction->fresh(['toUser']);
        });
    }
}

