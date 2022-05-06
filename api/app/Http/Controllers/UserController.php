<?php

namespace App\Http\Controllers;

use App\Dtos\User\UserResponseDto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Dtos\User\UserCreateDto;
use App\Services\User\FindUserService;
use App\Services\User\CreateUserService;
use App\Services\User\DeleteUserService;
use App\Services\User\UpdateUserService;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Rota para criar um novo usuário
     *
     * @param Request $request Dados da requisição
     * @param CreateUserService $createUserService
     * @return JsonResponse
     */
    public function store(Request $request, CreateUserService $createUserService): JsonResponse
    {
        $user = $createUserService->execute(new UserCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cpf: $request->cpf,
        ));

        return response()
            ->json(['success' => true, "data" => new UserResponseDto($user)], 201);
    }

    /**
     * Rota para mostrar os dados de um usuário.
     *
     * @param int $id id do usuário a ser procurado
     * @param FindUserService $findUserService
     * @return JsonResponse
     */
    public function show(int $id, FindUserService $findUserService): JsonResponse
    {
        $user = $findUserService->execute($id);
        return response()
            ->json(['success' => true, "data" => new UserResponseDto($user)], 200);
    }

    /**
     * Rota para atualizar dados de um usuário
     *
     * @param Request $request Dados da requisição
     * @param integer $id id do usuário a ser atualizado
     * @param UpdateUserService $updateUserService
     * @return JsonResponse
     */
    public function update(Request $request, int $id, UpdateUserService $updateUserService): JsonResponse
    {
        $user = $updateUserService->execute($id, new UserCreateDto(
            firstName: $request->first_name,
            lastName: $request->last_name,
            email: $request->email,
            password: $request->password,
            cpf: $request->cpf,
        ));

        return response()
            ->json(['success' => true, "data" => new UserResponseDto($user)], 200);
    }

    /**
     * Rota para excluir um usuário
     *
     * @param integer $id id do usuário a ser excluido
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
