<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// use Laravel\Pail\ValueObjects\Origin\Console;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
$validator = Validator::make($request->all(), [
    'full_name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|string|min:8|confirmed',
    'phone_number' => 'required|string|max:15',
    'terms' => 'accepted',
    'account_type' => 'required|string|in:supplier,customer',

],
[
    'full_name.required' => __('messages.nameError'),
    'email.required' => __('messages.emailError'),
    'email.email' => __('messages.emailValid'),
    'email.unique' => __('messages.emailUnique'),
    'password.min' => __('messages.passwordMin'),
    'password.string' => __('messages.passwordString'),
    'password.confirmed' => __('messages.passwordConfirm'),
    'phone_number.required' => __('messages.phoneMSG'),

]);
if ($validator->fails()) {
    return redirect()->back()
        ->withErrors($validator)
        ->withInput();
}
$user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'account_type' => $request->account_type,
        ]);

        Auth::login($user);
        Log::info('User registered successfully: ' . $user->email);
        return redirect()->route('home')->with('success', __('messages.register_success'));
    }
}
