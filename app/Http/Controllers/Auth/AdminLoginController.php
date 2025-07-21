<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        // If an admin is already authenticated, redirect them to the admin dashboard
        if (Auth::check() && Auth::user()->account_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // If a non-admin user is logged in, log them out for safety
        if (Auth::check()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        return view('admin.admin_login');
    }

    /**
     * Handle an admin login attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $request->wantsJson()
                ? response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422)
                : redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->account_type === 'admin') {
                $request->session()->regenerate();

                return $request->wantsJson()
                    ? response()->json([
                        'status' => 'success',
                        'message' => 'مرحباً أيها المشرف، تم تسجيل الدخول بنجاح!',
                        'redirect' => route('admin.dashboard'),
                    ])
                    :  redirect()->route('admin.dashboard')->with('success', 'مرحباً أيها المشرف، تم تسجيل الدخول بنجاح!');

            }

            // Not admin: force logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->wantsJson()
                ? response()->json([
                    'status' => 'error',
                    'errors' => [
                        'email' => ['ليس لديك صلاحيات المشرف.'],
                    ],
                ], 403)
                : throw ValidationException::withMessages([
                    'email' => 'ليس لديك صلاحيات المشرف.',
                ]);
        }

        return $request->wantsJson()
            ? response()->json([
                'status' => 'error',
                'errors' => [
                    'email' => [__('auth.failed')],
                ],
            ], 401)
            : throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
    }

    /**
     * Log the admin user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.show')
            ->with('success', 'تم تسجيل الخروج من لوحة المشرف.');
    }
}
