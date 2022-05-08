<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Dtos\Transaction\NotifyRecipientDto;

class NotifyServiceRepository
{
    /**
     * Consome um serviço externo para notificar o usuário recebedor
     *
     * @param NotifyRecipientDto $data Dados enviado para o serviço externo
     * @return void
     */
    public function notificationRecipientOfTransaction(NotifyRecipientDto $data): void
    {
        $response = Http::post('http://o4d9z.mocklab.io/notify', $data->toArray());

        if ($response->status() !== 201) {
            throw new NotifyServiceRepositoryException(
                "It was not possible notify the recipient user. Error on request for external service or it is not available",
                $response->status()
            );
        }
    }
}

class NotifyServiceRepositoryException extends Exception
{
}
