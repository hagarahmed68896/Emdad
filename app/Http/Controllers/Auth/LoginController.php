<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
    ]);
    $credentials = $request->only('email', 'password');
    $remember = $request->has('remember_me');
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return redirect()->intended(' home')->with('success', __('messages.login_success'));

    }
    throw ValidationException::withMessages([
        'email' => __('messages.login_failed'),
       
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


