<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Service responsável por retornar dados de um usuário
 */
class FindUserService
{
    protected UserRepository $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function execute(int $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if (empty($user)) {
            throw new NotFoundHttpException(message: "User not found", code: 404);
        }
        return $user;
    }
}
