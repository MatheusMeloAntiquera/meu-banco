<?php

namespace Tests\Traits;

use App\Dtos\User\UserCreateDto;

trait UserTestTrait
{

    protected function returnUserInsertable($faker)
    {
        return new UserCreateDto(
            firstName: $faker->firstName(),
            lastName: $faker->lastName(),
            email: $faker->email(),
            password: $faker->password(),
            cpf: $faker->cpf(false),
        );
    }
}
