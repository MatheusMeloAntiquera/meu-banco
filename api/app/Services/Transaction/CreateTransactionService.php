<?php

namespace App\Services\Transaction;

use App\Models\User;
use App\Models\UsersTransaction;
use App\Repositories\UserRepository;
use App\Services\User\FindUserService;
use App\Exceptions\TransactionException;
use App\Repositories\TransactionRepository;
use App\Dtos\Transaction\TransactionCreateDto;
use App\Services\User\CheckSufficientBalanceService;

/**
 * Service responsável por realizar uma transferencia de um usuário para outro
 */
class CreateTransactionService
{
    protected TransactionRepository $transactionRepository;
    protected FindUserService $findUserService;
    public function __construct(
        TransactionRepository $transactionRepository,
        FindUserService $findUserService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->findUserService = $findUserService;
    }

    public function execute(TransactionCreateDto $data): UsersTransaction
    {
        $sender = $this->findUserService->execute($data->senderId);
        $recipient = $this->findUserService->execute($data->recipientId);
        CheckSufficientBalanceService::execute($sender, $data->value);

        $data->setSenderBalance($sender->balance);
        $data->setRecipientBalance($recipient->balance);

        return $this->transactionRepository->create($data);
    }
}
