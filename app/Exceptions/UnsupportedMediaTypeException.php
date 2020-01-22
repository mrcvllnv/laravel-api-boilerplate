<?php

namespace App\Exceptions;

use Exception;

class UnsupportedMediaTypeException extends Exception
{
    protected $message = 'Unsupported Media Type';

    protected $code = 415;
}
