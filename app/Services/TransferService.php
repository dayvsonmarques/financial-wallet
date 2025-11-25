<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    public function transfer(User $fromUser, User $toUser, float $amount, ?string $description = null): Transaction
    {
        Log::channel('transactions')->info('Transfer initiated', [
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'amount' => $amount,
            'from_balance_before' => $fromUser->balance,
            'to_balance_before' => $toUser->balance,
        ]);

        if ($amount <= 0) {
            Log::channel('transactions')->warning('Transfer failed: invalid amount', [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'amount' => $amount,
            ]);
            throw new TransactionException('O valor deve ser maior que zero');
        }

        if ($fromUser->id === $toUser->id) {
            Log::channel('transactions')->warning('Transfer failed: self transfer', [
                'user_id' => $fromUser->id,
                'amount' => $amount,
            ]);
            throw new TransactionException('Não é possível transferir para você mesmo');
        }

        if (!$fromUser->hasSufficientBalance($amount)) {
            Log::channel('transactions')->warning('Transfer failed: insufficient balance', [
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'amount' => $amount,
                'current_balance' => $fromUser->balance,
            ]);
            throw new InsufficientBalanceException();
        }

        try {
            return DB::transaction(function () use ($fromUser, $toUser, $amount, $description) {
                $fromUser->lockForUpdate();
                $toUser->lockForUpdate();

                if (!$fromUser->hasSufficientBalance($amount)) {
                    throw new InsufficientBalanceException();
                }

                $transaction = Transaction::create([
                    'from_user_id' => $fromUser->id,
                    'to_user_id' => $toUser->id,
                    'type' => 'transfer',
                    'amount' => $amount,
                    'status' => 'completed',
                    'description' => $description,
                ]);

                $fromUser->decrement('balance', $amount);
                $toUser->increment('balance', $amount);

                $transaction = $transaction->fresh(['fromUser', 'toUser']);

                Log::channel('transactions')->info('Transfer completed', [
                    'transaction_id' => $transaction->id,
                    'from_user_id' => $fromUser->id,
                    'to_user_id' => $toUser->id,
                    'amount' => $amount,
                    'from_balance_after' => $fromUser->fresh()->balance,
                    'to_balance_after' => $toUser->fresh()->balance,
                ]);

                return $transaction;
            });
        } catch (\Exception $e) {
            Log::channel('transactions')->error('Transfer failed', [
                'error' => $e->getMessage(),
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'amount' => $amount,
            ]);
            throw $e;
        }
    }
}

