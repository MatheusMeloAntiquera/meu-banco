<?php

namespace App\Services\User;

use App\Repositories\UserRepository;

/**
 * Service responsável por deletar um usuário
 */
class DeleteUserService
{
    protected UserRepository $userRepository;
    protected FindUserService $findUserService;
    public function __construct(UserRepository $userRepository, FindUserService $findUserService)
    {
        $this->userRepository = $userRepository;
        $this->findUserService = $findUserService;
    }

    public function execute(int $userId): void
    {
        $this->findUserService->execute($userId);
        $this->userRepository->deleteById($userId);
    }
}
