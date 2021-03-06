<?php

namespace App\Exceptions;

use App\Exceptions\InvalidCredentialsException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        InvalidCredentialsException::class,
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
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        list($statusCode, $detail, $errorCode) = $this->getStatusAndMessage($exception);

        $data = [
            'error' => [
                'code'    => $errorCode ?: $statusCode,
                'message' => $detail ? $detail : ($exception->getMessage() ?: trans('http.' . $statusCode . '.message')),
            ],
        ];

        return response()->json($data, $statusCode);
    }

    /**
     * @param \Throwable $e
     *
     * @return array
     */
    protected function getStatusAndMessage(Throwable $e): array
    {
        $statusCode = $e->getCode();
        $detail = null;
        $errorCode = null;

        switch ($e) {
            case $e instanceof BadRequestHttpException:
                $statusCode = 400;
                break;
            case $e instanceof AuthenticationException || $e instanceof JWTException:
                $statusCode = 401;
                $detail = trans('http.401.message');
                break;
            case $e instanceof AuthorizationException:
                $statusCode = 403;
                break;
            case $e instanceof ModelNotFoundException:
                $statusCode = 404;
                break;
            case $e instanceof NotFoundHttpException:
                $statusCode = 404;
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $statusCode = 405;
                $detail = trans('http.405.message');
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
