<?php

namespace App\Repositories;

use App\Models\StoreKeeper;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;

class StoreKeeperRepository
{
    /**
     * Cria um novo usuário
     *
     * @param StoreKeeperCreateDto $data
     * @return StoreKeeper
     */
    public function create(StoreKeeperCreateDto $data): StoreKeeper
    {
        $storekeeper = new StoreKeeper($data->toArray());
        $storekeeper->save();
        return $storekeeper;
    }

    /**
     * Encontra um usuário pelo id
     *
     * @param UserCreateDto $data
     * @return StoreKeeper|null
     */
    public function findById(int $id): ?StoreKeeper
    {
        return StoreKeeper::find($id);
    }


    /**
     * Atualiza um novo registro
     *
     * @param StoreKeeperCreateDto $data
     * @return StoreKeeper
     */
    public function update(int $id, StoreKeeperCreateDto $data): StoreKeeper
    {
        $storekeeper = StoreKeeper::find($id);
        $storekeeper->fill(
            array_filter($data->toArray())
        );
        $storekeeper->save();
        return $storekeeper;
    }

    /**
     * Deleta um lojista pelo id
     *
     * @param $id $data
     *
     */
    public function deleteById(int $id): void
    {
        StoreKeeper::destroy($id);
    }

    public function updateBalance(int $id, float $newValue)
    {
        StoreKeeper::where('id', $id)
            ->update(['balance' => $newValue]);
    }
}
