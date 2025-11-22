<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionException;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Services\DepositService;
use App\Services\ReversalService;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransferService $transferService,
        private DepositService $depositService,
        private ReversalService $reversalService
    ) {}

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $toUser = \App\Models\User::findOrFail($request->to_user_id);
            $transaction = $this->transferService->transfer(
                $request->user(),
                $toUser,
                $request->amount,
                $request->description
            );

            return response()->json([
                'message' => 'Transfer completed successfully',
                'transaction' => $transaction,
            ], 201);
        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (TransactionException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function deposit(DepositRequest $request): JsonResponse
    {
        try {
            $transaction = $this->depositService->deposit(
                $request->user(),
                $request->amount,
                $request->description
            );

            return response()->json([
                'message' => 'Deposit completed successfully',
                'transaction' => $transaction,
            ], 201);
        } catch (TransactionException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function reverse(Request $request, Transaction $transaction): JsonResponse
    {
        $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            if ($transaction->from_user_id !== $request->user()->id && 
                $transaction->to_user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'You can only reverse your own transactions',
                ], 403);
            }

            $reversalTransaction = $this->reversalService->reverse(
                $transaction,
                $request->description
            );

            return response()->json([
                'message' => 'Transaction reversed successfully',
                'reversal_transaction' => $reversalTransaction,
            ], 201);
        } catch (TransactionException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $transactions = Transaction::where('from_user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->with(['fromUser:id,name,email', 'toUser:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($transactions);
    }

    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        if ($transaction->from_user_id !== $user->id && $transaction->to_user_id !== $user->id) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        $transaction->load(['fromUser:id,name,email', 'toUser:id,name,email', 'reversedBy']);

        return response()->json($transaction);
    }
}
