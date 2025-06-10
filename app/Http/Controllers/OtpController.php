<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User; // Assuming your User model is here
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // For sending email
use Illuminate\Support\Facades\Cache; // For rate limiting

class OtpController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function showVerificationForm(Request $request)
    {
        // 1. Determine the user we're trying to verify
        $userId = Auth::id() ?? session('pending_verification_user_id');

        // If no user ID is found, they shouldn't be on this page.
        if (!$userId) {
            return redirect('/login')->with('error', 'Authentication required for OTP verification.');
        }

        $user = User::find($userId);

        if (!$user) {
            // User not found in DB, something is wrong with the session ID.
            Auth::logout(); // Clear any partial auth state
            session()->forget('pending_verification_user_id');
            return redirect('/login')->with('error', 'User information not found. Please try again.');
        }

        // 2. Determine the identifier type (email/phone) and the actual identifier value
        $identifierType = session('otp_identifier_type', 'email'); // Default to 'email' if not set
        $identifier = null; // Initialize $identifier to null

        // Priority: If a phone number is available and the method is 'phone', use it.
        // Otherwise, or if phone is not available, default to email.
        if ($identifierType === 'phone' && !empty($user->phone_number)) {
            $identifier = $user->phone_number;
        } else {
            // Default to email or fallback to email if phone was chosen but missing
            $identifierType = 'email'; // Ensure identifierType is 'email'
            $identifier = $user->email;
            // Update the session if we had to fallback from phone to email
            if (session('otp_identifier_type') !== 'email') {
                session(['otp_identifier_type' => 'email']);
                session()->flash('warning', 'No phone number found. Sending OTP to your email instead.');
            }
        }

        // Ensure $identifier is not null at this point.
        if (is_null($identifier)) {
            // This case should ideally not happen with the logic above,
            // but as a failsafe, redirect to login.
            Auth::logout();
            session()->forget('pending_verification_user_id');
            return redirect('/login')->with('error', 'Could not determine verification identifier. Please log in again.');
        }

        // 3. Logic to send/resend OTP if necessary
        $cooldownKey = 'otp_cooldown_' . $userId . '_' . $identifierType;
        $canSend = !Cache::has($cooldownKey); // Check if cooldown is active

        $latestActiveOtp = Otp::where('user_id', $user->id)
                               ->where('identifier_type', $identifierType)
                               ->where('expires_at', '>', Carbon::now())
                               ->latest()
                               ->first();

        // Condition to send a new OTP on load:
        // - No active OTP
        // - OR an explicit resend request ($request->has('resend'))
        // - OR method switch request ($request->has('switch_method'))
        $shouldSendNewOtp = !$latestActiveOtp || $request->has('resend') || $request->has('switch_method');

        if ($shouldSendNewOtp && $canSend) {
            $this->sendOtp($user, $identifierType, $identifier);
            Cache::put($cooldownKey, true, 30); // 30 seconds cooldown
        } elseif ($shouldSendNewOtp && !$canSend) {
            // If they tried to resend/switch but are still on cooldown, flash a message
            session()->flash('info', 'Please wait before resending the OTP.');
        }

        // 4. Pass the $identifier and $identifierType to the view
        return view('partials.otp', compact('identifier', 'identifierType'));
    }

    /**
     * Send OTP to the user.
     */
    public function sendOtp(User $user, string $identifierType, string $identifier)
    {
        // Generate a 4-digit OTP
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(5); // OTP valid for 5 minutes

        // Delete any existing active OTPs for this user and identifier type
        Otp::where('user_id', $user->id)
            ->where('identifier_type', $identifierType)
            ->where('expires_at', '>', Carbon::now())
            ->delete();

        // Store the new OTP (store hashed code for security)
        Otp::create([
            'user_id' => $user->id,
            'code' => Hash::make($otpCode), // Hash the OTP
            'identifier_type' => $identifierType,
            'identifier' => $identifier,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP based on identifier type
        if ($identifierType === 'email') {
            // In a real application, you'd use a Mail notification or queue this.
            // Example: Mail::to($identifier)->send(new OtpMail($otpCode));
            logger()->info("Sending OTP via email to $identifier: $otpCode"); // For debugging
            session()->flash('success', "OTP sent to your email: {$identifier}");
        } elseif ($identifierType === 'phone') {
            // In a real application, integrate with an SMS gateway (e.g., Twilio, Nexmo)
            logger()->info("Sending OTP via SMS to $identifier: $otpCode"); // For debugging
            session()->flash('success', "OTP sent to your phone: {$identifier}");
        }
    }

    /**
     * Verify the OTP submitted by the user.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'digits:6'],
        ]);

        $userId = Auth::id() ?? session('pending_verification_user_id');
        $user = User::find($userId);

        if (!$user) {
            return back()->withErrors(['otp_code' => 'Verification failed. Please try again.']);
        }

        $enteredOtp = $request->input('otp_code');
        $identifierType = session('otp_identifier_type', 'email');

        // Find the latest active OTP for the user and identifier type
        $otp = Otp::where('user_id', $user->id)
                  ->where('identifier_type', $identifierType)
                  ->where('expires_at', '>', Carbon::now())
                  ->latest()
                  ->first();

        if (!$otp || !Hash::check($enteredOtp, $otp->code)) {
            // Increment failed attempts for rate limiting or account lockout
            // Cache::increment('failed_otp_attempts_' . $user->id);
            return back()->withErrors(['otp_code' => 'Invalid or expired OTP.']);
        }

        // OTP is valid! Delete it to prevent reuse.
        $otp->delete();

        // Log the user in if they weren't already (e.g., during registration flow)
        if (!Auth::check()) {
            Auth::login($user);
            session()->forget('pending_verification_user_id'); // Clear session if used
        }

        // Redirect to dashboard or intended page
        return redirect('/dashboard')->with('status', 'OTP verified successfully!');
    }

    /**
     * Handle resending OTP.
     */
    public function resendOtp(Request $request)
    {
        $userId = Auth::id() ?? session('pending_verification_user_id');
        $user = User::find($userId);

        if (!$user) {
            return back()->with('error', 'User not found for OTP resend.');
        }

        $identifierType = session('otp_identifier_type', 'email');
        $identifier = $identifierType === 'email' ? $user->email : $user->phone_number;

        // Add a check here for phone_number if identifierType is 'phone' and it's empty
        if ($identifierType === 'phone' && empty($identifier)) {
            session(['otp_identifier_type' => 'email']); // Fallback to email
            $identifier = $user->email;
            session()->flash('warning', 'No phone number found. Resending OTP to your email instead.');
        }


        $cooldownKey = 'otp_cooldown_' . $userId . '_' . $identifierType;
        if (Cache::has($cooldownKey)) {
            return back()->with('info', 'Please wait before resending the OTP.');
        }

        $this->sendOtp($user, $identifierType, $identifier);
        Cache::put($cooldownKey, true, 30); // 30 seconds cooldown

        return back();
    }

    /**
     * Handle switching OTP delivery method.
     */
    public function switchOtpMethod(Request $request)
    {
        $request->validate([
            'method' => ['required', 'in:email,phone'],
        ]);

        $userId = Auth::id() ?? session('pending_verification_user_id');
        $user = User::find($userId);

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $newMethod = $request->input('method');
        $identifier = null; // Initialize identifier for the new method

        if ($newMethod === 'email') {
            $identifier = $user->email;
        } elseif ($newMethod === 'phone') {
            $identifier = $user->phone_number;
            if (empty($identifier)) {
                // If the user tries to switch to phone but has no phone number,
                // don't proceed with phone OTP, flash an error, and stay on current method/page.
                session()->flash('error', 'No phone number registered for this account. Cannot switch to phone OTP.');
                return redirect()->route('otp.verify.show'); // Redirect back to show the existing OTP screen
            }
        }

        // Set the new identifier type in the session for subsequent requests
        session(['otp_identifier_type' => $newMethod]);

        $cooldownKey = 'otp_cooldown_' . $userId . '_' . $newMethod;
        if (Cache::has($cooldownKey)) {
             return redirect()->route('otp.verify.show')->with('info', 'Switched method. Please wait before requesting a new OTP.');
        }

        // Send OTP via the new method
        $this->sendOtp($user, $newMethod, $identifier); // Pass the correct identifier

        Cache::put($cooldownKey, true, 30); // 30 seconds cooldown

        // Redirect back to the OTP verification form, which will now display the updated identifier
        return redirect()->route('otp.verify.show');
    }

    // Helper method to set the user for pending verification after initial login/registration
    // You would call this after a successful login attempt where you want to enforce OTP
    public static function setPendingVerification(User $user, string $identifierType = 'email')
    {
        session([
            'pending_verification_user_id' => $user->id,
            'otp_identifier_type' => $identifierType,
        ]);
    }
}