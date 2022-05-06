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
     */
    public function destroy(int $id, DeleteStoreKeeperService $deleteStoreKeeperService): JsonResponse
    {
        $deleteStoreKeeperService->execute($id);
        return response()
            ->json(['success' => true, "message" => "The storekeeper was deleted successfully"], 200);
    }
}
