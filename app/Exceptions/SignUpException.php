<?php

namespace App\Exceptions;

use Exception;

class SignUpException extends Exception
{
    protected $message = 'There\'s an error during registration. Please try again later.';

    protected $code = 500;
}
