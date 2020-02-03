<?php

namespace App\Exceptions;

use Exception;

class ExpiredVerificationCodeException extends Exception
{
    protected $message = 'The provided verification code is expired.';

    protected $code = 400;
}
