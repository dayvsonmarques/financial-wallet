<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function transfer(User $fromUser, User $toUser, float $amount, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new TransactionException('O valor deve ser maior que zero');
        }

        if ($fromUser->id === $toUser->id) {
            throw new TransactionException('Não é possível transferir para você mesmo');
        }

        if (!$fromUser->hasSufficientBalance($amount)) {
            throw new InsufficientBalanceException();
        }

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

            return $transaction->fresh(['fromUser', 'toUser']);
        });
    }
}

