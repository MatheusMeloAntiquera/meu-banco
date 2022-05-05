<?php

namespace App\Services\User;

use App\Models\User;
use App\Dtos\User\UserCreateDto;
use App\Repositories\UserRepository;

/**
 * Service responsible for creating a new user
 */
class CreateUserService
{
    public UserRepository $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function execute(UserCreateDto $userData): User
    {
        return $this->userRepository->create($userData);
    }
}
