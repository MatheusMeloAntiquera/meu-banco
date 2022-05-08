<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Models\UsersTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\TransactionException;
use App\Dtos\Transaction\TransactionCreateDto;

class TransactionRepository
{
    public function create(
        TransactionCreateDto $data,
    ): UsersTransaction {
        return UsersTransaction::create($data->toArray());
    }
}
