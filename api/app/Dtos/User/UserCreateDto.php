<?php

namespace App\Dtos\User;

class UserCreateDto
{
    protected string $firstName;
    protected string $lastName;
    protected string $email;
    protected string $password;
    protected string $cpf;
    protected float $balance;
    protected bool $active;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $cpf,
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
    public function toArray()
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

    function __get($name)
    {
        return $this->{$name};
    }
}
