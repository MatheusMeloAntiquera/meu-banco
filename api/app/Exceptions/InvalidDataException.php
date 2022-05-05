<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidDataException extends Exception
{
    protected array $messages;
    public function __construct(string $message, ?array $messages = null, ?int $code = null, ?Throwable $previous = null)
    {
        $this->messages = $messages;
        parent::__construct($message, $code, $previous);
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
