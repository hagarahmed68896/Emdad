<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\TaqnyatOTP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BusinessData;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class OtpController extends Controller
{
    /**
     * Step 1: Send OTP for login/register/register_supplier
     */
    public function sendOtp(Request $request)
    {
        $authMethod = $request->auth_method;

        if ($authMethod === 'login') {
            $request->validate([
                'phone_number' => 'required|digits:9|exists:users,phone_number',
            ], [
                'phone.required' => __('messages.phoneMSG'),
                'phone.exists'   => __('messages.phone_failed'),
            ]);
        } elseif ($authMethod === 'register') {
            $request->validate([
                'full_name'      => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
                'phone_number'   => 'required|digits:9|unique:users,phone_number',
                'terms'          => 'accepted',
                'account_type'   => 'required|in:supplier,customer',
            ], [
                'full_name.required' => __('messages.nameError'),
                'email.required' => __('messages.emailError'),
                'email.email' => __('messages.emailValid'),
                'email.unique' => __('messages.emailUnique'),
                'password.min' => __('messages.passwordMin'),
                'password.string' => __('messages.passwordString'),
                'password.regex' => __('messages.passwordRegex'),
                'password.confirmed' => __('messages.passwordConfirm'),
                'phone_number.required' => __('messages.phoneMSG'),
                'phone_number.unique' => __('messages.phone_number_Unique'),
                'phone_number.digits' => __('messages.phone_number_max'),
                'terms.accepted' => __('messages.acceptTermsError'),
            ]);
        } elseif ($authMethod === 'register_supplier') {
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
                'national_id_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'commercial_registration_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'national_address_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'iban_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'tax_certificate_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        $clean_phone_number = preg_replace('/\D/', '', $request->phone_number);
        if (str_starts_with($clean_phone_number, '966')) {
            $clean_phone_number = substr($clean_phone_number, 3);
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Prepare data to store in session
        $dataToStore = $request->except(['_token']);

        // Handle file uploads for supplier registration
        if ($authMethod === 'register_supplier') {
            $documentsToProcess = [
                'national_id_attach',
                'commercial_registration_attach',
                'national_address_attach',
                'iban_attach',
                'tax_certificate_attach'
            ];

            foreach ($documentsToProcess as $field) {
                if ($request->hasFile($field)) {
                    // Store the file and replace the UploadedFile object with the file path
                    $path = $request->file($field)->store('temp_supplier_docs');
                    $dataToStore[$field] = $path;
                } else {
                    $dataToStore[$field] = null;
                }
            }
        }

        session([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
            'otp_phone' => $clean_phone_number,
            'otp_auth_method' => $authMethod,
            'pending_registration' => in_array($authMethod, ['register', 'register_supplier']) ? $dataToStore : null,
        ]);
        Log::info('OtpController reached');

        $taqnyatService = new TaqnyatOTP();
        $taqnyatService->sendOTP('+966' . $clean_phone_number, $otp);

        return response()->json([
            'success' => true,
            'status' => true,
            'message' => 'OTP sent successfully',
            'show_otp' => true,
            'otp' => $otp,
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
            return response()->json(['success' => false, 'message' => __('messages.otp_icorrect')], 422);
        }

        $authMethod = session('otp_auth_method');
        $phone = session('otp_phone');

        if ($authMethod === 'login') {
            $user = User::where('phone_number', $phone)->firstOrFail();

                 // Check if the user's account is active before logging in
            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.not_active account'),
                ], 403); // Use a 403 Forbidden status code
            }
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