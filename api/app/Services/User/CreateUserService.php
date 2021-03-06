<?php

namespace App\Services\User;

use App\Models\User;
use InvalidArgumentException;
use App\Dtos\User\UserCreateDto;
use App\Repositories\UserRepository;
use App\Exceptions\InvalidDataException;
use Illuminate\Support\Facades\Validator;

/**
 * Service responsible for creating a new user
 */
class CreateUserService
{
    const DATA_USER_INVALID = 'The user data is invalid';
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(UserCreateDto $userData): User
    {
        $this->validateUserData($userData);
        return $this->userRepository->create($userData);
    }

    protected function validateUserData(UserCreateDto $userData)
    {
        $validator = Validator::make($userData->toArray(), [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users|unique:store_keepers',
            'cpf' => 'required|size:11|unique:users',
        ]);

        if ($validator->fails()) {
            throw new InvalidDataException(self::DATA_USER_INVALID, $validator->errors()->getMessages());
        }
    }
}
