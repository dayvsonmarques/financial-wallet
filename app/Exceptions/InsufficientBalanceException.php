<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(string $message = 'Insufficient balance for this transaction')
    {
        parent::__construct($message, 422);
    }
}
