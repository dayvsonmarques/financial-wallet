<?php

namespace App\Exceptions;

use Exception;

class TransactionException extends Exception
{
    public function __construct(string $message = 'Transaction failed', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
