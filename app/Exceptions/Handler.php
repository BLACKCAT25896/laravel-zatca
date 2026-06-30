<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception): Response|JsonResponse
    {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => env('APP_DEBUG') ? $exception->getMessage() : 'Internal Server Error',
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
