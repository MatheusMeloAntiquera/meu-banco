<?php

namespace App\Repositories;

use App\Models\UsersTransaction;
use App\Models\UsersTransactionStorekeeper;

class TransactionRepository
{
    public function create(
        UsersTransaction|UsersTransactionStorekeeper $transaction
    ): UsersTransaction|UsersTransactionStorekeeper {
        $transaction->save();
        return $transaction;
    }
}
