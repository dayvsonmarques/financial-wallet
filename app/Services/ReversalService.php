<?php

namespace App\Services;

use App\Exceptions\TransactionException;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReversalService
{
    public function reverse(Transaction $transaction, ?string $description = null): Transaction
    {
        Log::channel('transactions')->info('Reversal initiated', [
            'transaction_id' => $transaction->id,
            'original_type' => $transaction->type,
            'amount' => $transaction->amount,
        ]);

        if (!$transaction->canBeReversed()) {
            Log::channel('transactions')->warning('Reversal failed: transaction cannot be reversed', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]);
            throw new TransactionException('Esta transação não pode ser estornada');
        }

        try {
            return DB::transaction(function () use ($transaction, $description) {
                $transaction->lockForUpdate();

                if (!$transaction->canBeReversed()) {
                    throw new TransactionException('Esta transação não pode ser estornada');
                }

                $reversalTransaction = Transaction::create([
                    'from_user_id' => $transaction->to_user_id,
                    'to_user_id' => $transaction->from_user_id,
                    'type' => 'reversal',
                    'amount' => $transaction->amount,
                    'status' => 'completed',
                    'description' => $description ?? 'Estorno da transação #' . $transaction->id,
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

                $reversalTransaction = $reversalTransaction->fresh(['fromUser', 'toUser']);

                Log::channel('transactions')->info('Reversal completed', [
                    'reversal_transaction_id' => $reversalTransaction->id,
                    'original_transaction_id' => $transaction->id,
                    'amount' => $transaction->amount,
                ]);

                return $reversalTransaction;
            });
        } catch (\Exception $e) {
            Log::channel('transactions')->error('Reversal failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
            ]);
            throw $e;
        }
    }
}

