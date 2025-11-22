<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $transactions = Transaction::where('from_user_id', $user->id)
            ->orWhere('to_user_id', $user->id)
            ->with(['fromUser:id,name,email', 'toUser:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('wallet.dashboard', [
            'user' => $user,
            'transactions' => $transactions,
        ]);
    }
}
