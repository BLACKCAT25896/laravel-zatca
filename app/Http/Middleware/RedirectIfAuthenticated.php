<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;

class RedirectIfAuthenticated extends RedirectIfAuthenticated
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('dashboard');
    }
}
