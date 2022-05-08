<?php

namespace App\Dtos\Transaction;

use App\Dtos\BaseDto;

class TransactionCreateDto extends BaseDto
{
    protected int $senderId;
    protected int $recipientId;
    protected float $value;
    protected float $senderBalance;
    protected float $recipientBalance;

    public function __construct(
        int $senderId,
        int $recipientId,
        float $value,
        float $senderBalance = 0.0,
        float $recipientBalance = 0.0
    ) {
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
        $this->value = $value;
        $this->senderBalance = $senderBalance;
        $this->recipientBalance = $recipientBalance;
    }

    /**
     * @override `App\Dtos\BaseDto::toArray`
     * @return array
     */
    public function toArray(): array
    {
        return [
            'sender_id' => $this->senderId,
            'recipient_id' => $this->recipientId,
            'value_transaction' => $this->value,
            'sender_balance' => $this->senderBalance,
            'recipient_balance' => $this->recipientBalance,
        ];
    }

    public function setSenderBalance(float $balance)
    {
        $this->senderBalance = $balance;
        return $this;
    }

    public function setRecipientBalance(float $balance)
    {
        $this->recipientBalance = $balance;
        return $this;
    }
}
