<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'balance' => $user->balance,
            'formatted_balance' => number_format($user->balance, 2, '.', ''),
        ]);
    }
}
