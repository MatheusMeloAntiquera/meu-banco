<?php

namespace App\Services\User;

use App\Models\User;
use App\Dtos\User\UserCreateDto;
use App\Repositories\UserRepository;

/**
 * Service responsável por atualizar os dados de um usuário
 */
class UpdateUserService
{
    protected UserRepository $userRepository;
    protected FindUserService $findUserService;
    public function __construct(UserRepository $userRepository, FindUserService $findUserService)
    {
        $this->userRepository = $userRepository;
        $this->findUserService = $findUserService;
    }

    public function execute(int $userId, UserCreateDto $userData): User
    {
        $this->findUserService->execute($userId);
        return $this->userRepository->update($userId, $userData);;
    }
}
