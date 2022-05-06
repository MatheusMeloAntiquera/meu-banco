<?php

namespace App\Services\StoreKeeper;

use App\Models\StoreKeeper;
use App\Exceptions\InvalidDataException;
use Illuminate\Support\Facades\Validator;
use App\Repositories\StoreKeeperRepository;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;

/**
 * Service responsável por criar um lojista
 */
class CreateStoreKeeperService
{
    protected StoreKeeperRepository $storeKeeperRepository;
    public function __construct()
    {
        $this->storeKeeperRepository = new StoreKeeperRepository();
    }

    public function execute(StoreKeeperCreateDto $storeKeeperData): StoreKeeper
    {
        $this->validateStorKeeperData($storeKeeperData);
        return $this->storeKeeperRepository->create($storeKeeperData);
    }

    protected function validateStorKeeperData(StoreKeeperCreateDto $storeKeeperData)
    {
        $validator = Validator::make($storeKeeperData->toArray(), [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users|unique:store_keepers',
            'cnpj' => 'required|size:14|unique:store_keepers',
        ]);

        if ($validator->fails()) {
            throw new InvalidDataException("The storekeeper data is invalid", $validator->errors()->getMessages());
        }
    }
}
