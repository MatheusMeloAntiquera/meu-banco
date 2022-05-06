<?php

namespace App\Dtos\User;

use App\Dtos\BaseDto;

class UserCreateDto extends BaseDto
{
    //Campos em comum entre os dois tipos de usuário
    protected ?string $firstName;
    protected ?string $lastName;
    protected ?string $email;
    protected ?string $password;
    protected ?float $balance;
    protected ?bool $active;

    //Campos especificos para esse tipo de usuário
    protected ?string $cpf;

    public function __construct(
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password,
        ?string $cpf,
        float $balance = 0.00,
        bool $active = true
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->cpf = $cpf;
        $this->balance = $balance;
        $this->active = $active;
    }

    /**
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
            'cpf' => $this->cpf,
            'balance' => $this->balance,
            'active' => $this->active,
        ];
    }
}
