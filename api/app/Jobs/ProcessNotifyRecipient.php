<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\StoreKeeper;
use Illuminate\Bus\Queueable;
use App\Models\UsersTransaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UsersTransactionStorekeeper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Dtos\Transaction\NotifyRecipientDto;
use App\Repositories\NotifyServiceRepository;

/**
 * Job responsável por notificar notificar o usuário recebedor via serviço externo.
 */
class ProcessNotifyRecipient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private User|StoreKeeper $recipient;
    private User $sender;
    private UsersTransaction|UsersTransactionStorekeeper $transaction;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        User $sender,
        User|StoreKeeper $recipient,
        UsersTransaction|UsersTransactionStorekeeper $transaction
    ) {
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->transaction = $transaction;
        $this->onQueue('notify');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotifyServiceRepository $notifyServiceRepository)
    {
        $notifyServiceRepository->notificationRecipientOfTransaction(new NotifyRecipientDto(
            sender: $this->sender,
            recipient: $this->recipient,
            transaction: $this->transaction,
        ));
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [10, 30, 60];
    }
}
