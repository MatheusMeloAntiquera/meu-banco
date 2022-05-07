<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Dtos\Transaction\TransactionCreateDto;
use App\Services\Transaction\CreateTransactionService;

class UsersTransactionController extends Controller
{
    public function store(Request $request, CreateTransactionService $createTransactionService)
    {
        $transaction = $createTransactionService->execute(new TransactionCreateDto(
            senderId: $request->sender_id,
            recipientId: $request->recipient_id,
            value: $request->value,
        ));

        return response()
            ->json(['success' => true, "transaction_id" => $transaction->id], 201);
    }
}
