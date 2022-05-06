<?php

namespace App\Dtos\User\StoreKeeper;

use App\Dtos\User\UserCreateDto;

class StoreKeeperCreateDto extends UserCreateDto
{

    private ?string $cnpj;

    public function __construct(
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password,
        ?string $cnpj,
        float $balance = 0.00,
        bool $active = true
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->cnpj = $cnpj;
        $this->balance = $balance;
        $this->active = $active;
    }

    /**
     * @inheritDoc
     * @override `App\Dtos\BaseDto::toArray`
     * @return array
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'password' => $this->password,
            'cnpj' => $this->cnpj,
            'balance' => $this->balance,
            'active' => $this->active,
        ];
    }
}
