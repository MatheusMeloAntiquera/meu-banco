<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\User\FindUserService;
use App\Services\User\CreateUserService;
use App\Services\User\DeleteUserService;
use App\Services\User\UpdateUserService;
use App\Dtos\User\StoreKeeper\StoreKeeperJsonDto;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;

class StoreKeeperController extends Controller
{
    /**
     * Rota para criar um novo lojista
     *
     * @param Request $request Dados da requisição
     * @param CreateUserService $createUserService
     * @return JsonResponse
     */
    public function store(Request $request, CreateUserService $createUserService): JsonResponse
    {
        $storekeeper = $createUserService->execute(new StoreKeeperCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cnpj: $request->cnpj,
        ));

        return response()
            ->json(['success' => true, "data" => new StoreKeeperJsonDto($storekeeper)], 201);
    }

    /**
     * Rota para mostrar os dados de um lojista.
     *
     * @param int $id id do lojista a ser procurado
     * @param FindUserService $findUserService
     * @return JsonResponse
     */
    public function show(int $id, FindUserService $findUserService): JsonResponse
    {
        $storekeeper = $findUserService->execute($id);
        return response()
            ->json(['success' => true, "data" => new StoreKeeperJsonDto($storekeeper)], 200);
    }

    /**
     * Rota para atualizar dados de um lojista
     *
     * @param Request $request Dados da requisição
     * @param integer $id id do lojista a ser atualizado
     * @param UpdateUserService $updateUserService
     * @return JsonResponse
     */
    public function update(Request $request, int $id, UpdateUserService $updateUserService): JsonResponse
    {
        $storekeeper = $updateUserService->execute($id, new StoreKeeperCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cnpj: $request->cnpj,
        ));

        return response()
            ->json(['success' => true, "data" => new StoreKeeperJsonDto($storekeeper)], 200);
    }

    /**
     * Rota para excluir um lojista
     *
     * @param integer $id id do lojista a ser excluido
     * @param DeleteUserService $deleteUserService
     * @return JsonResponse
     */
    public function destroy(int $id, DeleteUserService $deleteUserService): JsonResponse
    {
        $deleteUserService->execute($id);
        return response()
            ->json(['success' => true, "message" => "The user was deleted successfully"], 200);
    }
}
