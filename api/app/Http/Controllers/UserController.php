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
     * Rota para criar um novo usuário
     *
     * @param Request $request Dados da requisição
     * @param CreateUserService $createUserService
     * @return JsonResponse
     *
     * @api {post} /user/ Criar um usuário
     * @apiName StoreUser
     * @apiGroup Usuários
     *
     * @apiBody {string} first_name nome do usuário
     * @apiBody {string} last_name sobrenome do usuário
     * @apiBody {string} email email do usuário
     * @apiBody {string} password senha do usuário - mínimo 8 caracteres
     * @apiBody {string} cpf cpf do usuário - sem pontuação e letras
     *
     * @apiSuccess (201) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (201) {Object} data Dados do usuário criado
     * @apiSuccess (201) {int} data.id ID do usuário criado
     * @apiSuccess (201) {string} data.first_name nome do usuário
     * @apiSuccess (201) {string} data.last_name sobrenome do usuário
     * @apiSuccess (201) {string} data.email email do usuário
     * @apiSuccess (201) {string} data.cpf cpf do usuário
     * @apiSuccess (201) {bool} data.active define se o usuário está ativo
     * @apiSuccess (201) {Datetime} data.created_at data de criação do usuário
     * @apiSuccess (201) {Datetime} data.updated_at data da ultima atualização do usuário
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 201 Created
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Jon",
     *          "last_name": "Snow",
     *          "email": "jon.snow@stark.com",
     *          "cpf": "40050060078",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *       }
     *     }
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
     *
     * @api {get} /user/:id Buscar um usuário por ID
     * @apiParam {Number} id ID do usuário a ser encontrado
     * @apiName ShowUser
     * @apiGroup Usuários
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {Object} data Dados do usuário encontrado
     * @apiSuccess (200) {int} data.id ID do usuário encontrado
     * @apiSuccess (200) {string} data.first_name nome do usuário
     * @apiSuccess (200) {string} data.last_name sobrenome do usuário
     * @apiSuccess (200) {string} data.email email do usuário
     * @apiSuccess (200) {string} data.cpf cpf do usuário
     * @apiSuccess (200) {bool} data.active define se o usuário está ativo
     * @apiSuccess (200) {Datetime} data.created_at data de criação do usuário
     * @apiSuccess (200) {Datetime} data.updated_at data da ultima atualização do usuário
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Jon",
     *          "last_name": "Snow",
     *          "email": "jon.snow@stark.com",
     *          "cpf": "40050060078",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *        }
     *     }
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
     *
     * @api {put} /user/:id Atualiza um usuário por ID
     * @apiParam {Number} id ID do usuário a ser atualizado
     * @apiName UpdateUser
     * @apiGroup Usuários
     *
     * @apiBody {string} first_name nome do usuário
     * @apiBody {string} last_name sobrenome do usuário
     * @apiBody {string} email email do usuário
     * @apiBody {string} password senha do usuário - mínimo 8 caracteres
     * @apiBody {string} cpf cpf do usuário - sem pontuação e letras
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {Object} data Dados do usuário criado
     * @apiSuccess (200) {int} data.id ID do usuário encontrado
     * @apiSuccess (200) {string} data.first_name nome do usuário
     * @apiSuccess (200) {string} data.last_name sobrenome do usuário
     * @apiSuccess (200) {string} data.email email do usuário
     * @apiSuccess (200) {string} data.cpf cpf do usuário
     * @apiSuccess (200) {bool} data.active define se o usuário está ativo
     * @apiSuccess (200) {Datetime} data.created_at data de criação do usuário
     * @apiSuccess (200) {Datetime} data.updated_at data da ultima atualização do usuário
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "data": {
     *          "id": 1
     *          "first_name": "Jon",
     *          "last_name": "Targaryen", //Novo sobrenome
     *          "email": "jon.snow@stark.com",
     *          "cpf": "40050060078",
     *          "active": true,
     *          "created_at": "2022-05-08T20:32:44.000000Z"
     *          "updated_at": "2022-05-08T20:32:44.000000Z"
     *       }
     *     }
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
     *
     * @api {delete} /user/:id Deleta um usuário por ID
     * @apiParam {Number} id ID do usuário a ser excluído
     * @apiName DeleteUser
     * @apiGroup Usuários
     *
     * @apiSuccess (200) {bool} success define se a requisição obteve sucesso
     * @apiSuccess (200) {message} message
     *
     * @apiSuccessExample {json} Exemplo de Response de Sucesso:
     *     HTTP/1.1 200 OK
     *     {
     *       "success": true,
     *       "message": "The user was deleted successfully"
     *     }
     */
    public function destroy(int $id, DeleteUserService $deleteUserService): JsonResponse
    {
        $deleteUserService->execute($id);
        return response()
            ->json(['success' => true, "message" => "The user was deleted successfully"], 200);
    }
}
