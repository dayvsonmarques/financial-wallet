<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReversalService
{
    public function reverse(Transaction $transaction, ?string $description = null): Transaction
    {
        if (!$transaction->canBeReversed()) {
            throw new TransactionException('Transaction cannot be reversed');
        }

        return DB::transaction(function () use ($transaction, $description) {
            $transaction->lockForUpdate();

            if (!$transaction->canBeReversed()) {
                throw new TransactionException('Transaction cannot be reversed');
            }

            $reversalTransaction = Transaction::create([
                'from_user_id' => $transaction->to_user_id,
                'to_user_id' => $transaction->from_user_id,
                'type' => 'reversal',
                'amount' => $transaction->amount,
                'status' => 'completed',
                'description' => $description ?? 'Reversal of transaction #' . $transaction->id,
                'reversed_by_transaction_id' => $transaction->id,
            ]);

            // Reverse the original transaction
            $transaction->update(['status' => 'reversed']);

            // Reverse balances
            if ($transaction->from_user_id) {
                $transaction->fromUser->lockForUpdate();
                $transaction->fromUser->increment('balance', $transaction->amount);
            }

            if ($transaction->to_user_id) {
                $transaction->toUser->lockForUpdate();
                $transaction->toUser->decrement('balance', $transaction->amount);
            }

            return $reversalTransaction->fresh(['fromUser', 'toUser']);
        });
    }
}

