<?php

namespace App\Dtos;

abstract class BaseDto
{
    /**
     * Transforma os dados do DTO para array
     *
     * @return array
     */
    public abstract function toArray(): array;

    /**
     * Getter para acessar atributos privados ou protegidos do DTO
     *
     * @param string $name
     * @return mixed valor do atributo inacessível
     */
    public function __get($name)
    {
        return $this->{$name};
    }
}
