<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; // Make sure to import your User model
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite; // Import Socialite facade
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Hash; // For generating a password
use Illuminate\Support\Str; // For generating random strings
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Http\Controllers\Controller;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            // Get user information from Google
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists in your database
            $user = User::where('provider', 'google')
                        ->where('provider_id', $googleUser->getId())
                        ->first();

                          dd([
            'googleUser_id' => $googleUser->getId(),
            'googleUser_email' => $googleUser->getEmail(),
            'user_found_in_db' => $user ? $user->toArray() : null, // Show user data if found, null otherwise
            'condition_result' => !$user, // Will be true if user not found, false if found
        ]);

            if (!$user) {
                // If the user doesn't exist, create a new one
                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(), // Save Google's ID
                    'provider' => 'google', // Store the provider name
                    'provider_id' => $googleUser->getId(), // Store the provider's user ID
                    'profile_picture' => $googleUser->getAvatar(), // Assuming you have this column
                    'password' => Hash::make(Str::random(16)), // Generate a random password for social users
                    // 'phone_number' => null, // Or try to get it if available from socialite, otherwise null
                    // 'account_type' => 'user', // Default account type
                ]);
            }

            // Log the user in
            Auth::login($user);

            // Redirect to a dashboard or home page
            return redirect()->intended('/home'); // Or whatever your intended redirect is

        } catch (\Exception $e) {
            // Handle exceptions (e.g., user denies access, Google API error)
            // Log the error for debugging
            Log::error('Google Socialite Callback Error: ' . $e->getMessage(), ['exception' => $e]);

            // Redirect back to login with an error message
            return response(redirect('/login')->with('error', 'Unable to login with Google. Please try again.'));
        }
    }
}