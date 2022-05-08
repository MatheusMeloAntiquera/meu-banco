<?php

namespace App\Dtos;

use ReflectionClass;
use App\Dtos\DtoInterface;

abstract class BaseDto implements DtoInterface
{
    /**
     * Transforma os dados do DTO para array
     *
     * Por padrão é retornado todos os atributos definidos no DTO, caso queira
     * não retornar todos as propriedades faça um `override` dessa função
     *
     * @return array
     */
    public function toArray(): array
    {
        $baseDtoReflectionClass = new ReflectionClass($this);
        $array = [];
        foreach ($baseDtoReflectionClass->getProperties() as $properly) {
            $array[$properly->getName()] = $this->{$properly->getName()};
        }
        return $array;
    }

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
