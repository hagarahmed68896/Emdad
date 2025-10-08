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
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    /**
     * Step 1: Send OTP for login/register/register_supplier
     */
    public function sendOtp(Request $request)
    {
        $authMethod = $request->auth_method;
        Log::info('Recaptcha value:', ['captcha' => $request->input('g-recaptcha-response')]);

    // ------------------ RESEND OTP ------------------
    if ($authMethod === 'resend') {
        $email = $request->email ?? null;
        $phone = $request->phone_number ?? null;

        if (!$email && !$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resend OTP: no user info provided.'
            ], 422);
        }

        $user = null;
        if ($email) {
            $user = User::where('email', $email)->first();
        } elseif ($phone) {
            $user = User::where('phone_number', $phone)->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $clean_phone_number = $user->phone_number;
        $otp = rand(1000, 9999);

        session([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Log::info("Resent OTP for {$clean_phone_number}: {$otp}");

        $taqnyatService = new TaqnyatOTP();
        $taqnyatService->sendOTP('+966' . $clean_phone_number, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP resent successfully',
            'otp' => $otp,
        ]);
    }

        if ($authMethod === 'login') {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha'
            ], [
                'email.required' => __('messages.emailError'),
                'email.email' => __('messages.emailValid'),
                'email.exists' => __('messages.email_failed'),
                'password.required' => __('messages.passwordMSG'),
                'g-recaptcha-response.required' => __('messages.recaptcha_required'),

            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'status' => false,
                    'message' => __('messages.invalid credentials')
                ], 422);
            }

            $clean_phone_number = $user->phone_number;
        } elseif ($authMethod === 'register') {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
                'phone_number' => 'required|digits:9|unique:users,phone_number',
                'terms' => 'accepted',
                'account_type' => 'required|in:supplier,customer',
                'g-recaptcha-response' => 'required|captcha'
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
                'g-recaptcha-response.required' => __('messages.recaptcha_required'),

            ]);
            $clean_phone_number = preg_replace('/\D/', '', $request->phone_number);
            if (str_starts_with($clean_phone_number, '966')) {
                $clean_phone_number = substr($clean_phone_number, 3);
            }
        } elseif ($authMethod === 'register_supplier') {
    $request->validate([
        'full_name' => 'required|string|max:255',
        'company_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone_number' => 'required|string|digits:9|unique:users,phone_number',
        'password' => 'required|string|min:8|confirmed',
        'g-recaptcha-response' => 'required|captcha',


        // الهوية الوطنية (10 أرقام تبدأ بـ1 أو 2)
        'national_id' => 'required|digits:10|regex:/^[12]\d{9}$/',

        // السجل التجاري (10 أرقام)
        'commercial_registration' => 'required|digits:10',

        // العنوان الوطني
        'national_address' => 'required|string|max:255',

        // الآيبان السعودي (24 خانة يبدأ بـ SA)
        'iban' => 'required|string|regex:/^SA\d{22}$/',

        // الشهادة الضريبية (15 رقم يبدأ بـ 3)
        'tax_certificate' => 'required|digits:15|regex:/^3\d{14}$/',

        // الشروط
        'terms' => 'accepted',

        // نوع الحساب
        'account_type' => 'required|in:supplier',

        // الملفات المرفقة
        'national_id_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'commercial_registration_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'national_address_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'iban_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'tax_certificate_attach' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ], [
        // ✅ custom messages
        'full_name.required' => __('messages.full_name_required'),
        'company_name.required' => __('messages.company_name_required'),

        'email.required' => __('messages.email_required'),
        'email.email' => __('messages.email_email'),
        'email.unique' => __('messages.email_unique'),

        'phone_number.required' => __('messages.phone_required'),
        'phone_number.digits' => __('messages.phone_digits'),
        'phone_number.unique' => __('messages.phone_unique'),

        'password.required' => __('messages.password_required'),
        'password.min' => __('messages.password_min'),
        'password.confirmed' => __('messages.password_confirmed'),

        'national_id.required' => __('messages.national_id_required'),
        'national_id.digits' => __('messages.national_id_digits'),
        'national_id.regex' => __('messages.national_id_regex'),

        'commercial_registration.required' => __('messages.cr_required'),
        'commercial_registration.digits' => __('messages.cr_digits'),

        'national_address.required' => __('messages.address_required'),

        'iban.required' => __('messages.iban_required'),
        'iban.regex' => __('messages.iban_regex'),

        'tax_certificate.required' => __('messages.tax_required'),
        'tax_certificate.digits' => __('messages.tax_digits'),
        'tax_certificate.regex' => __('messages.tax_regex'),

        'terms.accepted' => __('messages.terms_accepted'),
        'account_type.in' => __('messages.account_type_invalid'),

        'national_id_attach.required' => __('messages.attach_required'),
        'commercial_registration_attach.required' => __('messages.attach_required'),
        'national_address_attach.required' => __('messages.attach_required'),
        'iban_attach.required' => __('messages.attach_required'),
        'tax_certificate_attach.required' => __('messages.attach_required'),
        'g-recaptcha-response.required' => __('messages.recaptcha_required'),
    ]);

    // ✅ تنسيق رقم الهاتف
    $clean_phone_number = preg_replace('/\D/', '', $request->phone_number);
    if (str_starts_with($clean_phone_number, '966')) {
        $clean_phone_number = substr($clean_phone_number, 3);
    }
}


        $otp = rand(1000, 9999);
        $dataToStore = $request->except(['_token']);

        if ($authMethod === 'register_supplier') {
            $documentsToProcess = [
                'national_id_attach', 'commercial_registration_attach', 'national_address_attach',
                'iban_attach', 'tax_certificate_attach'
            ];
            foreach ($documentsToProcess as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('temp_supplier_docs');
                    $dataToStore[$field] = $path;
                } else {
                    $dataToStore[$field] = null;
                }
            }
        }

        // Store the user's phone number or email and the OTP in the session
        // This is a crucial step to link the OTP to the user's identity
        session([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
            'otp_phone' => $clean_phone_number ?? null,
            'otp_email' => $request->email ?? null,
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
            'phone_number' => $clean_phone_number,  // ✅ add this
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
    
        if ($authMethod === 'login') {
            // Retrieve email from the session and use it to find the user in the database
            $email = session('otp_email');
            
            if (!$email) {
                return response()->json(['success' => false, 'message' => 'User session data not found.'], 422);
            }
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
    
            if ($user->status !== 'active') {
                return response()->json(['success' => false, 'message' => __('messages.not_active account')], 403);
            }
    
            Auth::login($user);
        $this->mergeGuestCartWithUserCart($request, $user);

        } elseif ($authMethod === 'register') {
            $data = session('pending_registration');
            $user = User::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'],
                'account_type' => $data['account_type'],
            ]);
            Auth::login($user);
    
        } elseif ($authMethod === 'register_supplier') {
            $data = session('pending_registration');
            DB::beginTransaction();
            try {
                $user = User::create([
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone_number' => $data['phone_number'],
                    'password' => Hash::make($data['password']),
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
                            'supplier_id' => $user->id,
                            'file_path' => $data[$field],
                            'notes' => null,
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
    
        session()->forget(['otp', 'otp_expires_at', 'otp_phone', 'otp_email', 'otp_auth_method', 'pending_registration']);
    
        return response()->json(['success' => true, 'redirect' => route('home')]);
    }

protected function mergeGuestCartWithUserCart(Request $request, User $user)
{
    $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);

    $guestCart = json_decode($request->input('guest_cart'), true);
    if (!$guestCart || !is_array($guestCart)) {
        return;
    }

    foreach ($guestCart as $item) {
        $variants = [
            'color' => $item['color'] ?? null,
            'size'  => $item['size'] ?? null,
        ];

        // Try to find existing item with same product_id and variants
        $existingItem = $cart->items()
            ->where('product_id', $item['product_id'])
            ->where('options->variants', json_encode($variants))
            ->first();

        if ($existingItem) {
            // Increment quantity
            $existingItem->increment('quantity', $item['quantity']);
        } else {
            // Create new cart item
            $cart->items()->create([
                'product_id'       => $item['product_id'],
                'cart_id'          => $cart->id,
                'price_at_addition'=> $item['unit_price'],
                'quantity'         => $item['quantity'],
                'options'          => ['variants' => $variants],
            ]);
        }
    }
}




    
}