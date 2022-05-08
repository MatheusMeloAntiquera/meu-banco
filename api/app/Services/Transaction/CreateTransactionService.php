<?php

namespace App\Services\Transaction;

use Throwable;
use App\Models\User;
use App\Models\StoreKeeper;
use App\Models\UsersTransaction;
use App\Jobs\ProcessNotifyRecipient;
use App\Repositories\BaseRepository;
use App\Repositories\UserRepository;
use App\Services\User\FindUserService;
use App\Exceptions\TransactionException;
use App\Models\UsersTransactionStorekeeper;
use App\Repositories\TransactionRepository;
use App\Dtos\Transaction\TransactionCreateDto;
use App\Repositories\AuthorizationServiceRepository;
use App\Repositories\StoreKeeperRepository;
use App\Services\User\CheckSufficientBalanceService;

/**
 * Service responsável por realizar uma transferencia de um usuário para outro
 */
class CreateTransactionService
{
    const NOT_EXTERNAL_AUTHERIZED_MESSAGE = 'This transaction was not authorized by external authorization service';

    protected AuthorizationServiceRepository $authorizationServiceRepository;
    protected BaseRepository $baseRepository;
    protected TransactionRepository $transactionRepository;
    protected UserRepository $userRepository;
    protected FindUserService $findUserService;

    protected User $sender;
    protected User|StoreKeeper $recipient;
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
    public function execute(
        User $sender,
        User|StoreKeeper $recipient,
        float $value,
        UsersTransaction|UsersTransactionStorekeeper $transactionModel,
    ): UsersTransaction|UsersTransactionStorekeeper {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->valueTransaction = $value;

        CheckSufficientBalanceService::execute($this->sender, $this->valueTransaction);

        try {
            $this->baseRepository->beginTransaction();

            $transactionModel->fill(
                (new TransactionCreateDto(
                    senderId: $this->sender->id,
                    recipientId: $this->sender->id,
                    value: $this->valueTransaction,
                    senderBalance: $this->sender->balance,
                    recipientBalance: $this->recipient->balance,
                ))->toArray()
            );

            //Cria o registro da transferencia
            $transaction = $this->transactionRepository->create($transactionModel);

            $this->updateBalances();

            $this->checkAuthorizationOnExternalService();

            $this->baseRepository->commit();
        } catch (Throwable $e) {
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
        if ($this->authorizationServiceRepository->isAuthorized() === false) {
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
        $recipientRepository = $this->recipient instanceof Storekeeper ?
            new StoreKeeperRepository()
            : $this->userRepository;
        $this->userRepository->updateBalance(
            $this->sender->id,
            $this->sender->balance - $this->valueTransaction
        );

        $recipientRepository->updateBalance(
            $this->recipient->id,
            $this->recipient->balance + $this->valueTransaction
        );
    }
}
