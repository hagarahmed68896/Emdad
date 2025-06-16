<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('partials.login');
    }

 public function login(Request $request)
    {
        // 1. Manually validate the request for basic required fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // If basic validation fails (e.g., empty email, invalid email format), return general validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Check if the email exists in the database first
        $user = User::where('email', $credentials['email'])->first();

        // If user not found, send 'email' specific error
        if (!$user) {
            return response()->json(['errors' => ['email' => __('messages.failed_email')]], 422);
            // Note: You'll need to add 'failed_email' to your resources/lang/en/auth.php
            // For example: 'failed_email' => 'No account found with that email address.',
        }

        // If user exists, attempt to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response()->json(['redirect' => route('home')], 200);
        }

        // If authentication fails at this point, it means the email was correct but the password was not
        return response()->json(['errors' => ['password' => __('messages.incorrect_password')]], 422);
        // Note: You'll need to add 'password' (or similar) to your resources/lang/en/auth.php
        // For example: 'password' => 'The provided password is incorrect.',
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
