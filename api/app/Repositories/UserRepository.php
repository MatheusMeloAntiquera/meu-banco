<?php

namespace App\Repositories;

use App\Models\User;
use App\Dtos\User\UserCreateDto;

class UserRepository
{
    /**
     * Cria um novo usuário
     *
     * @param UserCreateDto $data
     * @return User
     */
    public function create(UserCreateDto $data): User
    {
        $user = new User($data->toArray());
        $user->save();
        return $user;
    }

    /**
     * Encontra um usuário pelo id
     *
     * @param UserCreateDto $data
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }


    /**
     * Create a new user
     *
     * @param UserCreateDto $data
     * @return User
     */
    public function update(int $id, UserCreateDto $data): User
    {
        $user = User::find($id);
        $user->fill(
            array_filter($data->toArray())
        );
        $user->save();
        return $user;
    }

    /**
     * Deleta um usuário pelo id
     *
     * @param UserCreateDto $data
     * @return void
     */
    public function deleteById(int $id): void
    {
        User::destroy($id);
    }


    public function updateBalance(int $id, float $newValue)
    {
        User::where('id', $id)
            ->update(['balance' => $newValue]);
    }
}
