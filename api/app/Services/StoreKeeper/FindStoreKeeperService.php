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
    const STOREKEEPER_NOT_FOUND_MESSAGE = "Storekeeper not found";
    protected StoreKeeperRepository $storeKeeperRepository;
    public function __construct(StoreKeeperRepository $storeKeeperRepository)
    {
        $this->storeKeeperRepository = $storeKeeperRepository;
    }

    public function execute(int $storeKeeperId): StoreKeeper
    {
        $storekeeper = $this->storeKeeperRepository->findById($storeKeeperId);

        if (empty($storekeeper)) {
            throw new NotFoundHttpException(
                message: FindStoreKeeperService::STOREKEEPER_NOT_FOUND_MESSAGE,
                code: 404
            );
        }
        return $storekeeper;
    }
}
