<?php

namespace Tests\Traits;

use App\Models\User;
use App\Dtos\User\UserCreateDto;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;

trait UserTestTrait
{

    protected function returnAnUserInsertable($faker)
    {
        return new UserCreateDto(
            firstName: $faker->firstName(),
            lastName: $faker->lastName(),
            email: $faker->email(),
            password: $faker->password(),
            cpf: $faker->cpf(false),
        );
    }

    protected function returnAStoreKeeperInsertable($faker)
    {
        return new StoreKeeperCreateDto(
            firstName: $faker->firstName(),
            lastName: $faker->lastName(),
            email: $faker->email(),
            password: $faker->password(),
            cnpj: $faker->cnpj(false),
        );
    }

    protected function createAnUserSuccessfully(): User
    {
        return User::factory()->create(
            $this->returnAnUserInsertable($this->fakerBr)->toArray()
        );
    }

    protected function createAStoreKeeperSuccessfully(): User
    {
        return User::factory()->create(
            $this->returnAStoreKeeperInsertable($this->fakerBr)->toArray()
        );
    }
}
