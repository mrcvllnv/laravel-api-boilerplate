<?php

namespace App\Exceptions;

use Exception;
use PDOException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        list($statusCode, $detail, $errorCode) = $this->getStatusAndMessage($exception);

        $data = [
            'error' => [
                'code'    => $errorCode ?: $statusCode,
                'message' => $detail ?: $exception->getMessage(),
            ],
        ];

        return response()->json($data, $statusCode);
    }

    /**
     * @param \Exception $e
     *
     * @return array
     */
    protected function getStatusAndMessage(Exception $e): array
    {
        $statusCode = $e->getCode();
        $detail = null;
        $errorCode = null;

        switch ($e) {
            case $e instanceof AuthenticationException || $e instanceof JWTException:
                $statusCode = 401;
                break;
            case $e instanceof AuthorizationException:
                $statusCode = 403;
                break;
            case $e instanceof ModelNotFoundException:
                $statusCode = 404;
            break;
            case $e instanceof ValidationException:
                $statusCode = 422;
                $detail = $e->errors();
                break;
            case $e instanceof HttpException:
                $statusCode = $e->getStatusCode();
                $detail = $e->getMessage();
                break;
            case $e instanceof PDOException:
                $detail = $e->getMessage();
                $statusCode = 500;
                break;
        }

        if (method_exists($e, 'getErrorCode')) {
            $errorCode = $e->getErrorCode();
        }

        return [
            $statusCode ?: 500,
            $detail,
            $errorCode,
        ];
    }
}
