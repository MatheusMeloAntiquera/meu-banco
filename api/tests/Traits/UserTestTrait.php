<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\StoreKeeper;
use App\Dtos\User\UserCreateDto;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;
use Faker\Factory as Faker;
trait UserTestTrait
{

    protected function returnAUserInsertable($faker)
    {
        return new UserCreateDto(
            firstName: $faker->firstName(),
            lastName: $faker->lastName(),
            email: $faker->freeEmail(),
            password: $faker->password(),
            cpf: $faker->cpf(false),
        );
    }

    protected function returnAStoreKeeperInsertable($faker)
    {
        return new StoreKeeperCreateDto(
            firstName: $faker->firstName(),
            lastName: $faker->lastName(),
            email: $faker->freeEmail(),
            password: $faker->password(),
            cnpj: $faker->cnpj(false),
        );
    }

    protected function createAUserSuccessfully(): User
    {
        $fakerBr = Faker::create('pt_BR');
        return User::factory()->create(
            $this->returnAUserInsertable($fakerBr)->toArray()
        );
    }

    protected function createAStoreKeeperSuccessfully(): StoreKeeper
    {
        $fakerBr = Faker::create('pt_BR');
        return StoreKeeper::factory()->create(
            $this->returnAStoreKeeperInsertable($fakerBr)->toArray()
        );
    }

    /**
     * Retorna uma rota gerada pelo do `Route::apiResource`
     *
     * @param string $action Ação/método do controller
     * @param integer|null $id $id do usuário
     * @param string $name nome da rota do apiResource
     * @param string $nameParam nome do parâmetro do id na rota
     * @return string
     */
    protected function getRouteApiResource(
        string $action, ?int $id = null, $name = 'users', $nameParam = 'user'): string
    {
        $params = !empty($id) ? [$nameParam => $id] : null;
        return route("{$name}.{$action}", $params, false);
    }

    /**
     * Retorna uma rota gerada pelo do `Route::apiResource` para lojistas
     *
     * @param string $action Ação/método do controller
     * @param integer|null $id $id do usuário lojista
     * @return string
     */
    protected function getRouteStoreKeeperApiResource(string $action, ?int $id = null): string
    {
        return $this->getRouteApiResource($action, $id, 'storekeepers', 'storekeeper');
    }
}
