<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            $response->header('Content-Type', 'application/json');
            $response->header('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }
}
