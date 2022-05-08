<?php

namespace App\Dtos\Transaction;

use DateTime;
use App\Models\User;
use App\Dtos\BaseDto;
use App\Models\UsersTransaction;

class NotifyRecipientDto extends BaseDto
{
    protected int $sender_id;
    protected string $sender_name;
    protected string $sender_email;
    protected int $recipient_id;
    protected string $recipient_name;
    protected string $recipient_email;
    protected float $value_transaction;
    protected DateTime $transaction_date;

    public function __construct(
       User $sender,
       User $recipient,
       UsersTransaction $transaction,
    ) {
        $this->sender_id = $sender->id;
        $this->sender_name = $sender->name;
        $this->sender_email = $sender->email;
        $this->recipient_id = $recipient->id;
        $this->recipient_name = $recipient->name;
        $this->recipient_email = $recipient->email;
        $this->value_transaction = $transaction->value_transaction;
        $this->transaction_date = $transaction->created_at;
    }
}
