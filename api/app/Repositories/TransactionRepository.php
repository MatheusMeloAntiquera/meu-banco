<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UsersTransaction;
use Illuminate\Support\Facades\DB;
use App\Dtos\Transaction\TransactionCreateDto;

class TransactionRepository
{
    public function create(TransactionCreateDto $data): UsersTransaction
    {
        return DB::transaction(function () use ($data) {
            User::where('id', $data->senderId)
                ->update(['balance' => $data->senderBalance - $data->value]);

            User::where('id', $data->recipientId)
                ->update(['balance' => $data->recipientBalance + $data->value]);

            return UsersTransaction::create($data->toArray());
        }, 5);
    }
}
