<?php

namespace App\Services\StoreKeeper;

use App\Repositories\StoreKeeperRepository;
use App\Services\StoreKeeper\FindStoreKeeperService;

/**
 * Service responsável por deletar um lojista
 */
class DeleteStoreKeeperService
{
    protected StoreKeeperRepository $storeKeeperRepository;
    protected FindStoreKeeperService $findStoreKeeperService;
    public function __construct(StoreKeeperRepository $storeKeeperRepository, FindStoreKeeperService $findStoreKeeperService)
    {
        $this->storeKeeperRepository = $storeKeeperRepository;
        $this->findStoreKeeperService = $findStoreKeeperService;
    }

    public function execute(int $storeKeeperId): void
    {
        $this->findStoreKeeperService->execute($storeKeeperId);
        $this->storeKeeperRepository->deleteById($storeKeeperId);
    }
}
