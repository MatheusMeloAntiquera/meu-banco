<?php

namespace App\Dtos\User;

use App\Dtos\BaseResponseDto;

class UserResponseDto extends BaseResponseDto
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
