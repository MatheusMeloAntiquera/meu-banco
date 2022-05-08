<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsersTransaction;
use App\Services\User\FindUserService;
use App\Models\UsersTransactionStorekeeper;
use App\Dtos\Transaction\TransactionCreateDto;
use App\Services\StoreKeeper\FindStoreKeeperService;
use App\Services\Transaction\CreateTransactionService;

class TransactionController extends Controller
{
    private CreateTransactionService $createTransactionService;
    private FindUserService $findUserService;
    private FindStoreKeeperService $findStoreKeeperService;

    public function __construct(
        CreateTransactionService $createTransactionService,
        FindUserService $findUserService,
        FindStoreKeeperService $findStoreKeeperService
    ) {
        $this->createTransactionService = $createTransactionService;
        $this->findUserService = $findUserService;
        $this->findStoreKeeperService = $findStoreKeeperService;
    }
    public function transfToUser(Request $request,)
    {
        $transaction = $this->createTransactionService->execute(
            $this->findUserService->execute($request->sender_id),
            $this->findUserService->execute($request->recipient_id),
            $request->value,
            new UsersTransaction()
        );

        return response()
            ->json(['success' => true, "transaction_id" => $transaction->id], 201);
    }

    public function transfToStore(Request $request)
    {
        $transaction = $this->createTransactionService->execute(
            $this->findUserService->execute($request->sender_id),
            $this->findStoreKeeperService->execute($request->recipient_id),
            $request->value,
            new UsersTransactionStorekeeper()
        );

        return response()
            ->json(["transaction_id" => $transaction->id], 201);
    }
}
