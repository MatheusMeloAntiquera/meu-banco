<?php

namespace App\Exceptions;

class NotFoundException extends WithHttpsCodeException
{
    protected $code = 404;
}
