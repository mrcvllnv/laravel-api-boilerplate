<?php

namespace App\Exceptions;

use Exception;

class NotJWTSubjectException extends Exception
{
    protected $message = 'Could not generate access token.';

    protected $code = 500;
}
