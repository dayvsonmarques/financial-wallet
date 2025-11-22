<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(string $message = 'Saldo insuficiente para esta transação')
    {
        parent::__construct($message, 422);
    }
}
