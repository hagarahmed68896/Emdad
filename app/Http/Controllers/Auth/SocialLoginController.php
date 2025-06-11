<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists, log them in
                Auth::login($user);
            } else {
                // If user doesn't exist, create a new one
                $newUser = User::create([
                    'full_name' => $googleUser->getName(), // Assuming 'full_name' in your User model
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(), // Store Google ID for future reference
                    'password' => bcrypt(uniqid()), // Generate a random password (user won't use it for social login)
                    // You might want to add a 'provider' column (e.g., 'google', 'facebook') to your users table
                    // and a 'provider_id' column to store the social ID
                ]);
                Auth::login($newUser);
            }

            return redirect()->intended('/dashboard'); // Or wherever you want to redirect after login

        } catch (\Exception $e) {
            // Handle error, e.g., redirect to login with an error message
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $facebookUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'full_name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'password' => bcrypt(uniqid()),
                ]);
                Auth::login($newUser);
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Facebook login failed. Please try again.');
        }
    }
}