<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Services\DepositService;
use App\Services\ReversalService;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionWebController extends Controller
{
    public function __construct(
        private TransferService $transferService,
        private DepositService $depositService,
        private ReversalService $reversalService
    ) {}

    public function showTransfer(Request $request): View
    {
        $users = \App\Models\User::where('id', '!=', $request->user()->id)
            ->orderBy('name')
            ->get();

        return view('transactions.transfer', ['users' => $users]);
    }

    public function transfer(TransferRequest $request): RedirectResponse
    {
        try {
            $toUser = \App\Models\User::findOrFail($request->to_user_id);
            $this->transferService->transfer(
                $request->user(),
                $toUser,
                $request->amount,
                $request->description
            );

            return redirect('/dashboard')->with('success', 'Transferência realizada com sucesso!');
        } catch (InsufficientBalanceException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        } catch (TransactionException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function showDeposit(): View
    {
        return view('transactions.deposit');
    }

    public function deposit(DepositRequest $request): RedirectResponse
    {
        try {
            $this->depositService->deposit(
                $request->user(),
                $request->amount,
                $request->description
            );

            return redirect('/dashboard')->with('success', 'Depósito realizado com sucesso!');
        } catch (TransactionException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        
        $transactions = Transaction::where('from_user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->with(['fromUser:id,name,email', 'toUser:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('transactions.index', ['transactions' => $transactions]);
    }

    public function reverse(Request $request, Transaction $transaction): RedirectResponse
    {
        try {
            if ($transaction->from_user_id !== $request->user()->id && 
                $transaction->to_user_id !== $request->user()->id) {
                return back()->withErrors(['error' => 'Você só pode estornar suas próprias transações']);
            }

            $this->reversalService->reverse(
                $transaction,
                $request->description ?? 'Transação estornada'
            );

            return redirect('/transactions')->with('success', 'Transação estornada com sucesso!');
        } catch (TransactionException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
