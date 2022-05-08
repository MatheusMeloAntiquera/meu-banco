<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Exceptions\NotFoundException;

/**
 * Service responsável por retornar dados de um usuário
 */
class FindUserService
{
    const USER_NOT_FOUND_MESSAGE = "User not found";
    protected UserRepository $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function execute(int $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if (empty($user)) {
            throw new NotFoundException(message: self::USER_NOT_FOUND_MESSAGE, code: 404);
        }
        return $user;
    }
}
