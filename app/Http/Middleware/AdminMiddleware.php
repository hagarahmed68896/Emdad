<?php

namespace App\Http\Middleware; // <-- Correct namespace

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated AND their account_type is 'admin'
        if (Auth::check() && Auth::user()->account_type === 'admin') {
            return $next($request); // Allow access if admin
        }

        // If not an admin, or not authenticated, redirect or abort
        // For security, if they somehow reached here but aren't admin, log them out.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to admin login with an error message
        return redirect()->route('admin.login.show')->withErrors(['error' => 'ليس لديك صلاحيات الوصول إلى هذه الصفحة.']);
    }
}
