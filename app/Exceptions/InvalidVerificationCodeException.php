<?php

namespace App\Exceptions;

use Exception;

class InvalidVerificationCodeException extends Exception
{
    protected $message = 'The provided verification code is invalid.';

    protected $code = 400;
}
