<?php

namespace App\Exceptions;

use Exception;

class NotAcceptableException extends Exception
{
    protected $message = 'Not Acceptable';

    protected $code = 406;
}
