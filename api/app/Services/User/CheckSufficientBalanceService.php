<?php

namespace App\Services\User;

use App\Models\User;
use App\Exceptions\TransactionException;

class CheckSufficientBalanceService
{
    const ERROR_MESSAGE = "The sender has insufficient balance to do this transaction";
    public static function execute(User $sender, float $value)
    {
        if ($sender->balance - $value < 0.0) {
            throw new TransactionException(self::ERROR_MESSAGE, 403);
        }
    }
}
