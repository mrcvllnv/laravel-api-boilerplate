<?php

namespace App\Exceptions;

use Exception;

class InvalidResetTokenException extends Exception
{
    protected $message = 'Invalid reset token.';

    protected $code = 400;
}
