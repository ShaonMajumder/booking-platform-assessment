<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    
    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            if (!$request->bearerToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Access token is missing.',
                ], 401);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Access token is invalid.',
            ], 401);
        }

        return redirect()->guest(route('login')); // fallback for web
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson() && $exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found.'
            ], 404);
        }

        return parent::render($request, $exception);
    }

}
