<?php

namespace App\Services\Transaction;

use Throwable;
use App\Models\User;
use App\Models\UsersTransaction;
use App\Jobs\ProcessNotifyRecipient;
use App\Repositories\BaseRepository;
use App\Repositories\UserRepository;
use App\Services\User\FindUserService;
use App\Exceptions\TransactionException;
use App\Repositories\TransactionRepository;
use App\Dtos\Transaction\TransactionCreateDto;
use App\Repositories\AuthorizationServiceRepository;
use App\Services\User\CheckSufficientBalanceService;

/**
 * Service responsável por realizar uma transferencia de um usuário para outro
 */
class CreateTransactionService
{
    const NOT_EXTERNAL_AUTHERIZED_MESSAGE = 'It was not possible to complete the transaction. Try again later';

    protected AuthorizationServiceRepository $authorizationServiceRepository;
    protected BaseRepository $baseRepository;
    protected TransactionRepository $transactionRepository;
    protected UserRepository $userRepository;
    protected FindUserService $findUserService;

    protected User $sender;
    protected User $recipient;
    protected float $valueTransaction;

    public function __construct(
        AuthorizationServiceRepository $authorizationServiceRepository,
        BaseRepository $baseRepository,
        TransactionRepository $transactionRepository,
        UserRepository $userRepository,
        FindUserService $findUserService,
    ) {
        $this->authorizationServiceRepository = $authorizationServiceRepository;
        $this->baseRepository = $baseRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->findUserService = $findUserService;
    }

    /**
     * Realiza a transferência entre dois usuários
     *
     * @param TransactionCreateDto $data Dados para realizar a transferência
     * @return UsersTransaction Registro da Transferência realizada.
     */
    public function execute(TransactionCreateDto $data): UsersTransaction
    {
        $this->sender = $this->findUserService->execute($data->senderId);
        $this->recipient = $this->findUserService->execute($data->recipientId);
        $this->valueTransaction = $data->value;

        CheckSufficientBalanceService::execute($this->sender, $this->valueTransaction);

        $data->setSenderBalance($this->sender->balance);
        $data->setRecipientBalance($this->recipient->balance);

        try {
            $this->baseRepository->beginTransaction();

            //Cria o registro da transferencia
            $transaction = $this->transactionRepository->create($data);

            //Atualiza os saldos do envolvidos na transferencia
            $this->updateBalances($data->value);

            //Checa se foi autorizado pelo serviço externo
            $this->checkAuthorizationOnExternalService();

            $this->baseRepository->commit();

        } catch (Throwable $e) {
            // dd($e);
            //Todo: criar log para exception
            $this->baseRepository->rollBack();
            throw new TransactionException("It was not possible to complete the transaction. Try again later", 403);
        }

        //Notifica o usuário recebedor
        ProcessNotifyRecipient::dispatch(
            $this->sender,
            $this->recipient,
            $transaction,
        );

        return $transaction;
    }
    /**
     * Checa se a transferencia foi autorizada pelo serviço externo
     *
     * @return void
     * @throws TransactionException Lança uma exceção caso a transferência não seja autorizada ou o serviço está funcionando
     */
    private function checkAuthorizationOnExternalService()
    {
        if (!$this->authorizationServiceRepository->isAuthorized()) {
            throw new TransactionException(
                CreateTransactionService::NOT_EXTERNAL_AUTHERIZED_MESSAGE,
                403
            );
        }
    }

    /**
     * Atualiza os saldos do envolvidos na transferência
     *
     * @return void
     */
    private function updateBalances()
    {
        $this->userRepository->updateBalance(
            $this->sender->id,
            $this->sender->balance - $this->valueTransaction
        );

        $this->userRepository->updateBalance(
            $this->recipient->id,
            $this->recipient->balance + $this->valueTransaction
        );
    }
}
