<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Services\TaqnyatOTP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BusinessData;
use App\Models\Document;



class OtpController extends Controller
{
    /**
     * Step 1: Send OTP for login/register/register_supplier
     */

public function sendOtp(Request $request)
{
    $authMethod = $request->auth_method;

    if ($authMethod === 'login') {
        // Login validation
        $request->validate([
            'phone_number' => 'required|digits:9|exists:users,phone_number',
        ], [
            'phone.required' => __('messages.phoneMSG'),
            'phone.exists'   => __('messages.phone_failed'),
        ]);
    } elseif ($authMethod === 'register') {
        // Normal user registration validation
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
            'phone_number'   => 'required|digits:9|unique:users,phone_number',
            'terms'          => 'accepted',
            'account_type'   => 'required|in:supplier,customer',
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
    } elseif ($authMethod === 'register_supplier') {
        // Supplier registration validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|digits:9|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|string|max:255',
            'commercial_registration' => 'required|string|max:255',
            'national_address' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'tax_certificate' => 'required|string|max:255',
            'terms' => 'accepted',
            'account_type' => 'required|in:supplier',
        ]);
    }

    //  ✅ Ensure phone number is in the correct 9-digit format before sending
    $clean_phone_number = preg_replace('/\D/', '', $request->phone_number);
    if (str_starts_with($clean_phone_number, '966')) {
        $clean_phone_number = substr($clean_phone_number, 3);
    }
    
    // Generate OTP
    $otp = rand(1000, 9999);

    // Save OTP + request data in session
    session([
        'otp' => $otp,
        'otp_expires_at' => now()->addMinutes(5),
        'otp_phone' => $clean_phone_number,
        'otp_auth_method' => $authMethod,
        'pending_registration' => in_array($authMethod, ['register', 'register_supplier']) ? $request->all() : null,
    ]);
    Log::info('OtpController reached');

    // ✅ Send OTP via SMS with the full international number
    $taqnyatService = new TaqnyatOTP();
    $taqnyatService->sendOTP('+966' . $clean_phone_number, $otp);

    return response()->json([
        'success'  => true,
        'status' => true,
        'message' => 'OTP sent successfully',
        'show_otp' => true,
        'otp' => $otp, // ⚠️ Note: remove this line for production
    ]);
}

    /**
     * Step 2: Verify OTP and login/register
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:4',
        ]);

        if (session('otp') != $request->otp || now()->gt(session('otp_expires_at'))) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 422);
        }

        $authMethod = session('otp_auth_method');
        $phone = session('otp_phone');

        if ($authMethod === 'login') {
            $user = User::where('phone_number', $phone)->firstOrFail();
            Auth::login($user);

        } elseif ($authMethod === 'register') {
            $data = session('pending_registration');
            $user = User::create([
                'full_name'    => $data['full_name'],
                'email'        => $data['email'],
                'password'     => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'account_type' => $data['account_type'],
            ]);
            Auth::login($user);

        } elseif ($authMethod === 'register_supplier') {
            $data = session('pending_registration');

            DB::beginTransaction();
            try {
                $user = User::create([
                    'full_name'    => $data['full_name'],
                    'email'        => $data['email'],
                    'phone_number' => $data['phone_number'],
                    'password'     => Hash::make($data['password']),
                    'account_type' => $data['account_type'],
                ]);

                $businessData = BusinessData::create([
                    'user_id' => $user->id,
                    'company_name' => $data['company_name'],
                    'national_id' => $data['national_id'],
                    'commercial_registration' => $data['commercial_registration'],
                    'national_address' => $data['national_address'],
                    'iban' => $data['iban'],
                    'tax_certificate' => $data['tax_certificate'],
                ]);

                // Handle documents if uploaded
                $documentsMap = [
                    'national_id_attach' => 'National ID',
                    'commercial_registration_attach' => 'Commercial Registration',
                    'national_address_attach' => 'National Address',
                    'iban_attach' => 'IBAN',
                    'tax_certificate_attach' => 'Tax Certificate',
                ];

                foreach ($documentsMap as $field => $name) {
                    if (isset($data[$field])) {
                        Document::create([
                            'document_name' => $name,
                            'supplier_id'   => $user->id,
                            'file_path'     => $data[$field], // You might store temp path before OTP
                            'notes'         => null,
                        ]);
                    }
                }

                DB::commit();
                Auth::login($user);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        // Clear OTP from session
        session()->forget(['otp', 'otp_expires_at', 'otp_phone', 'otp_auth_method', 'pending_registration']);

        return response()->json(['success' => true, 'redirect' => route('home')]);
    }
}
