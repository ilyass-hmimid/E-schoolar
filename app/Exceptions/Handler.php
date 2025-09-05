<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        return $this->handleException($e, $request) 
            ?? parent::render($request, $e);
    }

    /**
     * Handle the exception and return a response.
     *
     * @param  \Throwable  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|null
     */
    protected function handleException(Throwable $e, $request)
    {
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($e instanceof AccessDeniedHttpException || $e->getCode() === 403) {
            return response()->view('errors.403', [], 403);
        }

        if ($e instanceof HttpException && $e->getStatusCode() === 500) {
            return response()->view('errors.500', [], 500);
        }

        return null;
    }
}
