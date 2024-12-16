<?php

namespace App\Exceptions;

use App\Enums\ResponseEnum;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        $this->reportable(function (Throwable $e) {});
    }

    public function report(Throwable $exception)
    {
        Log::error('>>Exception occurred<<', [
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {

            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'error' => 'This action is unauthorized',
                ], 403);
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['error' => 'Resource not found'], ResponseEnum::NOT_FOUND);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json(['error' => 'Unauthenticated'], ResponseEnum::UNAUTHORIZED);
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'error'    => 'Validation Error',
                    'messages' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json(['error' => 'Endpoint not found'], ResponseEnum::NOT_FOUND);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json(['error' => 'Method not allowed'], ResponseEnum::METHOD_NOT_ALLOWED);
            }

            if ($exception instanceof HttpException) {
                return response()->json(['error' => $exception->getMessage()], $exception->getStatusCode());
            }

            return response()->json(['error' => 'An unexpected error occurred'], ResponseEnum::INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
