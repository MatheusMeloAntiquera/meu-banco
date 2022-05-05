<?php

namespace App\Repositories;

use App\Models\User;
use App\Dtos\User\UserCreateDto;

class UserRepository
{
    /**
     * Create a new user
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
}
