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

    /**
     * Trata as tranferências entre usuários
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @api {post} /transactions/ Transferencia Usuário > Usuário
     * @apiName TransfToUser
     * @apiGroup Transferências
     *
     * @apiDescription Realiza a transferência entre usuários
     *
     * @apiBody {int} sender_id ID do usuário que irá enviar o dinheiro
     * @apiBody {int} recipient_id ID do usuário que irá receber o dinheiro
     * @apiBody {float} value valor a ser transferido - o usuário deve ter saldo suficiente
     *
     * @apiSuccess (201) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (201) {int} transaction_id ID do registro da transação criada
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *       "success": true,
     *       "transaction_id": 1
     *     }
     */
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

    /**
     * Trata as tranferências de usuários para lojistas
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @api {post} /transactions/ Transferencia Usuário > Lojista
     * @apiName TransfToStore
     * @apiGroup Transferências
     *
     * @apiDescription Realiza a transferência de dinheiro de um usuário para um lojista
     *
     * @apiBody {int} sender_id ID do usuário que irá enviar o dinheiro
     * @apiBody {int} recipient_id ID do *lojista* que irá receber o dinheiro
     * @apiBody {float} value valor a ser transferido - o usuário deve ter saldo suficiente
     *
     * @apiSuccess (201) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (201) {int} transaction_id ID do registro da transação criada
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *       "success": true,
     *       "transaction_id": 1
     *     }
     */
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
