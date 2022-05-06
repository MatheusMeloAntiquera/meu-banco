<?php

namespace App\Services\StoreKeeper;

use App\Models\StoreKeeper;
use App\Repositories\StoreKeeperRepository;
use App\Dtos\User\StoreKeeper\StoreKeeperCreateDto;

/**
 * Service responsável por atualizar os dados de um lojista
 */
class UpdateStoreKeeperService
{
    protected StoreKeeperRepository $storeKeeperRepository;
    protected FindStoreKeeperService $findStoreKeeperService;
    public function __construct(StoreKeeperRepository $storeKeeperRepository, FindStoreKeeperService $findStoreKeeperService)
    {
        $this->storeKeeperRepository = $storeKeeperRepository;
        $this->findStoreKeeperService = $findStoreKeeperService;
    }

    public function execute(int $storeKeeperId, StoreKeeperCreateDto $storeKeeperData): StoreKeeper
    {
        $this->findStoreKeeperService->execute($storeKeeperId);
        return $this->storeKeeperRepository->update($storeKeeperId, $storeKeeperData);
    }
}
