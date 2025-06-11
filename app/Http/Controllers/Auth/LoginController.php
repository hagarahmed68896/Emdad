<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Make sure this is present

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('layouts.app'); // Or 'auth.login' if it's a separate blade file
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');
        // Use 'remember' as the checkbox name in your Blade form
        $remember = $request->has('remember'); // Corrected from 'remember_me'

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/home')->with('success', __('messages.login_success')); // Corrected path
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => __('messages.login_failed'), // Or a more generic message like 'These credentials do not match our records.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}