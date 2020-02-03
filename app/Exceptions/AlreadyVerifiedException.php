<?php

namespace App\Exceptions;

use Exception;

class AlreadyVerifiedException extends Exception
{
    protected $message = 'The provided email is already verified.';

    protected $code = 400;
}
