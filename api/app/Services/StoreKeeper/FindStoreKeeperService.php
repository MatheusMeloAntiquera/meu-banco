<?php

namespace App\Services\StoreKeeper;

use App\Models\StoreKeeper;
use App\Repositories\StoreKeeperRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Service responsável por retornar dados de um lojista
 */
class FindStoreKeeperService
{
    protected StoreKeeperRepository $storeKeeperRepository;
    public function __construct(StoreKeeperRepository $storeKeeperRepository)
    {
        $this->storeKeeperRepository = $storeKeeperRepository;
    }

    public function execute(int $storeKeeperId): StoreKeeper
    {
        $storekeeper = $this->storeKeeperRepository->findById($storeKeeperId);

        if (empty($storekeeper)) {
            throw new NotFoundHttpException(message: "Storekeeper not found", code: 404);
        }
        return $storekeeper;
    }
}
