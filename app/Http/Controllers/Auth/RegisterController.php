<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
            'phone_number' => 'required|digits:9|unique:users,phone_number',
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
            'password.regex'=> __('messages.passwordRegex'),
            'password.confirmed' => __('messages.passwordConfirm'),
            'phone_number.required' => __('messages.phoneMSG'),
            'phone_number.unique'=> __('messages.phone_number_Unique'),
            'phone_number.digits' => __('messages.phone_number_max'),
            'terms.accepted' => __('messages.acceptTermsError'),
        ]);

        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'account_type' => $request->account_type,
        ]);

        // Auth::login($user); // User is logged in on the server

           // Only send redirect for supplier
    // $redirectUrl = null;
    // if ($user->account_type === 'supplier') {
    //     $redirectUrl = route('supplier.home'); // or '/supplier/dashboard'
    // }

        return response()->json([
            'success' => true,
          
            // You can optionally return user data here if needed for client-side state updates
            // 'user' => $user->only(['id', 'full_name', 'email', 'account_type']),
        ], 200);
    }

// Controller (Laravel)
public function saveLocation(Request $request)
{
    $user = Auth::user();

    try {
        if ($request->filled('city')) {
            // Save only city name as address
            $user->address = $request->input('city');
            $user->lat = null;
            $user->lng = null;
        } elseif ($request->filled('address') && $request->filled('lat') && $request->filled('lng')) {
            $address = $request->input('address');
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            // ensure numeric lat/lng (server safety)
            if (!is_numeric($lat) || !is_numeric($lng)) {
                return response()->json(['success' => false, 'message' => 'Invalid coordinates'], 400);
            }

            $user->address = $address;
            $user->lat = (float) $lat;
            $user->lng = (float) $lng;
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
        }

        $user->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('saveLocation error: ' . $e->getMessage(), [
            'trace'   => $e->getTraceAsString(),
            'payload' => $request->all(),
            'user_id' => optional($user)->id,
        ]);

        return response()->json(['success' => false, 'message' => 'Server error'], 500);
    }
}





}