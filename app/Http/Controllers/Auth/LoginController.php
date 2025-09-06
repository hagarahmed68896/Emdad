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
        // 1. Validate phone number with custom messages
        $messages = [
            'phone_number.required' => __('messages.phoneMSG'),
            'phone_number.digits'   => __('messages.phone_number_max'),
            'phone_number.exists'   => __('messages.phone_failed'),
        ];

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|digits:9|exists:users,phone_number',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $phone = $request->phone_number;

        // 2. Find the user by their phone number
        $user = User::where('phone_number', $phone)->first();

        // 3. Generate a 4-digit OTP and store it in the session
        $otp = rand(1000, 9999);
        session([
            'otp_user_id' => $user->id,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5), // OTP is valid for 5 minutes
        ]);
        
        // At this point, you would send the OTP via SMS or another service.
        // For example:
        // OtpService::send($phone, $otp);

        // 4. Return the JSON response to show the OTP modal on the front end
        return response()->json([
            'show_otp' => true,
            'phone' => $phone,
        ], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}