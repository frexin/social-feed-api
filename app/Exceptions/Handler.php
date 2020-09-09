<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $error = null;
        $code  = null;
        $data  = null;

        switch (get_class($e)) {
            case ModelNotFoundException::class:
                $error = class_basename($e->getModel()) . ' not found';
                $code  = 404;
                break;
            case NotFoundHttpException::class:
                $error = 'invalid route: ' . $request->getPathInfo();
                $code  = 404;
                break;
            case UnauthorizedException::class:
                $error = $e->getMessage() ?: 'Unauthorized';
                $code  = 401;
                break;
            case ValidationException::class:
                $error = $e->getMessage();
                $data  = $e->validator->errors()->all();
                $code  = 422;
                break;
            case AuthorizationException::class:
                $error = $e->getMessage() ?: 'authentication failed';
                $code  = 403;
                break;
            default:
                $error = (get_class($e)) . ' ' . $e->getMessage();
//                return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            $error = 'Invalid params';
            $data  = $e->validator->errors()->all();
            $code  = 422;
        }

        return response()->json(compact('error', 'data'), $code ?: 500);
    }
}
