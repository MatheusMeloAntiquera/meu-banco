<?php

namespace App\Dtos\User\StoreKeeper;

use App\Dtos\BaseResponseDto;

class StoreKeeperResponseDto extends BaseResponseDto
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'cnpj' => $this->cnpj,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
