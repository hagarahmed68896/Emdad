<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Make sure this is imported
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        // Split full_name into first_name and last_name for the form
        $names = preg_split('/\s+/', trim($user->full_name), -1, PREG_SPLIT_NO_EMPTY);
        $user->first_name = $names[0] ?? '';
        $user->last_name = implode(' ', array_slice($names, 1)); // All remaining parts

          // --- ADD THIS PART TO FETCH FAVORITES ---
             $favorites = collect(); // Default to an empty collection

        if ($user) {
            // Ensure you're paginating here!
            // 'product.category' eager loads related models to prevent N+1 queries.
            $favorites = $user->favorites()->with('product.category')->paginate(3);
        }

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            // If it's AJAX, return only the partial view.
            // .render() is important to get the HTML string.
            return view('partials.favorites_list', compact('favorites'))->render();
        }
        // ----------------------------------------

        return view('profile.account', compact('user', 'favorites'));
    }

    /**
     * Update the user's profile details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
     public function updateDetails(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|digits:9|unique:users,phone_number,' . $user->id,
            'address' => 'nullable|string|max:255',
        ],
        // Optional: Custom error messages for this form
        [
            'first_name.required' => __('messages.first_name_required'),
            'last_name.required' => __('messages.last_name_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'email.unique' => __('messages.email_unique'),
            'phone_number.digits' => __('messages.phone_number_digits'),
            'phone_number.unique' => __('messages.phone_number_unique'),
            // Add other custom messages as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'full_name' => $request->input('first_name') . ' ' . $request->input('last_name'),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        // *** THIS IS THE CRITICAL CHANGE ***
        return response()->json([
            'success' => true,
            'message' => __('messages.account_details_updated_successfully') // Use translation key
        ]);
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'current_password' => [
            'required',
        ],
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/[A-Z]/',   // at least one uppercase
            'regex:/[0-9]/',   // at least one digit
        ],
    ],
    [
        'current_password.required' => __('messages.current_password_required'),
        'password.required' => __('messages.new_password_required'),
        'password.min' => __('messages.passwordMin'),
        'password.confirmed' => __('messages.passwordConfirm'),
        'password.regex' => __('messages.passwordRegex'),
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return response()->json(['errors' => [
            'current_password' => [__('messages.incorrect_current_password')]
        ]], 422);
    }

    Auth::user()->update([
        'password' => bcrypt($request->password)
    ]);

    return response()->json([
        'success' => true,
        'message' => __('messages.password_updated_successfully')
    ]);
}


    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        $user->update(['profile_picture' => $path]);

        return response()->json([
            'message' => 'تم تحديث صورة الملف الشخصي بنجاح!',
            'profile_picture_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Remove the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeProfilePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);

            return response()->json([
                'message' => 'تمت إزالة صورة الملف الشخصي بنجاح.',
                'profile_picture_url' => asset('images/Unknown_person.jpg') // Fallback to your specified placeholder
            ]);
        }

        return response()->json([
            'message' => 'لا توجد صورة ملف شخصي لإزالتها.',
        ], 400);
    }
}
