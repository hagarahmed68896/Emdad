<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutAdminOnFrontend
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') { // Or account_type === 'admin'
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $next($request);
    }
}
