<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Dtos\User\StoreKeeper\StoreKeeperResponseDto;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;
use App\Services\StoreKeeper\FindStoreKeeperService;
use App\Services\StoreKeeper\CreateStoreKeeperService;
use App\Services\StoreKeeper\DeleteStoreKeeperService;
use App\Services\StoreKeeper\UpdateStoreKeeperService;

class StoreKeeperController extends Controller
{
    /**
     * Rota para criar um novo lojista
     *
     * @param Request $request Dados da requisição
     * @param CreateStoreKeeperService $createStoreKeeperService
     * @return JsonResponse
     *
     * @api {post} /storekeepers/ Criar um lojista
     * @apiName StoreStorekeeper
     * @apiGroup Lojistas
     *
     * @apiBody {string} first_name nome do lojista
     * @apiBody {string} last_name sobrenome do lojista
     * @apiBody {string} email email do lojista
     * @apiBody {string} password senha do lojista - mínimo 8 caracteres
     * @apiBody {string} cnpj cnpj do lojista - sem pontuação e letras
     *
     * @apiSuccess (201) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (201) {Object} data Dados do lojista criado
     * @apiSuccess (201) {int} data.id ID do lojista criado
     * @apiSuccess (201) {string} data.first_name nome do lojista
     * @apiSuccess (201) {string} data.last_name sobrenome do lojista
     * @apiSuccess (201) {string} data.email email do lojista
     * @apiSuccess (201) {string} data.cnpj cnpj do lojista
     * @apiSuccess (201) {bool} data.active define se o lojista está ativo
     * @apiSuccess (201) {Datetime} data.created_at data de criação do lojista
     * @apiSuccess (201) {Datetime} data.updated_at data da ultima atualização do lojista
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Central Perk",
     *          "last_name": "NY Company",
     *          "email": "central_perk@friends.com",
     *          "cnpj": "07471272000119",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *       }
     *     }
     */
    public function store(Request $request, CreateStoreKeeperService $createStoreKeeperService): JsonResponse
    {
        $storekeeper = $createStoreKeeperService->execute(new StoreKeeperCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cnpj: $request->cnpj,
        ));

        return response()
            ->json(['success' => true, "data" => new StoreKeeperResponseDto($storekeeper)], 201);
    }

    /**
     * Rota para mostrar os dados de um lojista.
     *
     * @param int $id id do lojista a ser procurado
     * @param FindStoreKeeperService $findStoreKeeperService
     * @return JsonResponse
     *
     * @api {get} /storekeepers/:id Buscar um lojista por ID
     * @apiParam {Number} id ID do lojista a ser encontrado
     * @apiName ShowStorekeeper
     * @apiGroup Lojistas
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {Object} data Dados do lojista encontrado
     * @apiSuccess (200) {int} data.id ID do lojista encontrado
     * @apiSuccess (200) {string} data.first_name nome do lojista
     * @apiSuccess (200) {string} data.last_name sobrenome do lojista
     * @apiSuccess (200) {string} data.email email do lojista
     * @apiSuccess (200) {string} data.cnpj cnpj do lojista
     * @apiSuccess (200) {bool} data.active define se o lojista está ativo
     * @apiSuccess (200) {Datetime} data.created_at data de criação do lojista
     * @apiSuccess (200) {Datetime} data.updated_at data da ultima atualização do lojista
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Central Perk",
     *          "last_name": "NY Company",
     *          "email": "central_perk@friends.com",
     *          "cnpj": "07471272000119",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *        }
     *     }
     */
    public function show(int $id, FindStoreKeeperService $findStoreKeeperService): JsonResponse
    {
        $storekeeper = $findStoreKeeperService->execute($id);
        return response()
            ->json(['success' => true, "data" => new StoreKeeperResponseDto($storekeeper)], 200);
    }

    /**
     * Rota para atualizar dados de um lojista
     *
     * @param Request $request Dados da requisição
     * @param integer $id id do lojista a ser atualizado
     * @param UpdateStoreKeeperService $updateStoreKeeperService
     * @return JsonResponse
     *
     * * @api {put} /storekeepers/:id Atualiza um lojista por ID
     * @apiParam {Number} id ID do lojista a ser atualizado
     * @apiName UpdateStorekeeper
     * @apiGroup Lojistas
     *
     * @apiBody {string} first_name nome do lojista
     * @apiBody {string} last_name sobrenome do lojista
     * @apiBody {string} email email do lojista
     * @apiBody {string} password senha do lojista - mínimo 8 caracteres
     * @apiBody {string} cnpj cnpj do lojista - sem pontuação e letras
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {Object} data Dados do lojista criado
     * @apiSuccess (200) {int} data.id ID do lojista encontrado
     * @apiSuccess (200) {string} data.first_name nome do lojista
     * @apiSuccess (200) {string} data.last_name sobrenome do lojista
     * @apiSuccess (200) {string} data.email email do lojista
     * @apiSuccess (200) {string} data.cnpj cnpj do lojista
     * @apiSuccess (200) {bool} data.active define se o lojista está ativo
     * @apiSuccess (200) {Datetime} data.created_at data de criação do lojista
     * @apiSuccess (200) {Datetime} data.updated_at data da ultima atualização do lojista
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Central Perk",
     *          "last_name": "NY Company",
     *          "email": "central_perk_ny@friends.com", //Novo e-mail
     *          "cnpj": "07471272000119",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *       }
     *     }
     */
    public function update(Request $request, int $id, UpdateStoreKeeperService $updateStoreKeeperService): JsonResponse
    {
        $storekeeper = $updateStoreKeeperService->execute($id, new StoreKeeperCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cnpj: $request->cnpj,
        ));

        return response()
            ->json(['success' => true, "data" => new StoreKeeperResponseDto($storekeeper)], 200);
    }

    /**
     * Rota para excluir um lojista
     *
     * @param integer $id id do lojista a ser excluido
     * @param DeleteStoreKeeperService $deleteStoreKeeperService
     * @return JsonResponse
     *
     * @api {delete} /storekeepers/:id Deleta um lojista por ID
     * @apiParam {Number} id ID do lojista a ser excluído
     * @apiName DeleteUser
     * @apiGroup Lojistas
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {message} message
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "message": "The storekeeper was deleted successfully"
     *     }
     */
    public function destroy(int $id, DeleteStoreKeeperService $deleteStoreKeeperService): JsonResponse
    {
        $deleteStoreKeeperService->execute($id);
        return response()
            ->json(['success' => true, "message" => "The storekeeper was deleted successfully"], 200);
    }
}
