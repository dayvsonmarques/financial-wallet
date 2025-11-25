<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{
    public function deposit(User $user, float $amount, ?string $description = null): Transaction
    {
        Log::channel('transactions')->info('Deposit initiated', [
            'user_id' => $user->id,
            'amount' => $amount,
            'balance_before' => $user->balance,
        ]);

        if ($amount <= 0) {
            Log::channel('transactions')->warning('Deposit failed: invalid amount', [
                'user_id' => $user->id,
                'amount' => $amount,
            ]);
            throw new TransactionException('O valor deve ser maior que zero');
        }

        try {
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

                $transaction = $transaction->fresh(['toUser']);

                Log::channel('transactions')->info('Deposit completed', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'balance_after' => $user->fresh()->balance,
                ]);

                return $transaction;
            });
        } catch (\Exception $e) {
            Log::channel('transactions')->error('Deposit failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'amount' => $amount,
            ]);
            throw $e;
        }
    }
}

