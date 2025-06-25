<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator; // Ensure this is imported for pagination styling

class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Ensure Paginator is configured for Tailwind in AppServiceProvider's boot method
        Paginator::useTailwind(); // Redundant here if in AppServiceProvider, but harmless.

        $user = Auth::user();

        // Split full_name into first_name and last_name for the form
        $names = preg_split('/\s+/', trim($user->full_name), -1, PREG_SPLIT_NO_EMPTY);
        $user->first_name = $names[0] ?? '';
        $user->last_name = implode(' ', array_slice($names, 1)); // All remaining parts

        // --- Fetch Favorites (if needed for the initial load) ---
        $favorites = collect();
        if ($user) {
            // Only paginate if the request is not AJAX or if it's explicitly for favorites via AJAX
            // This prevents loading all favorites data when just switching to 'Account' or 'Notifications' section
            if (!$request->ajax() || ($request->ajax() && $request->route()->getName() === 'profile.favorites.ajax')) {
                 $favorites = $user->favorites()->with('product.category')->paginate(3);
            }
        }

        // --- Pass notification settings to the view ---
        // $user->notification_settings will be an array because of the cast in the User model.
        // Provide default values if the user hasn't saved any settings yet.
        $notificationSettings = $user->notification_settings ?? $this->getDefaultNotificationSettings();


        // Handle AJAX requests for partial content
        if ($request->ajax()) {
            // You need to distinguish which partial is requested for AJAX
            // This assumes your AJAX requests hit this 'show' method directly for favorites
            // If you have separate routes for notifications AJAX, this might differ
            if ($request->path() === 'profile/favorites') { // Adjust this path to your actual route for favorites AJAX
                return view('partials.favorites_list', compact('favorites'))->render();
            }
            // Add conditions for other AJAX partials if they are handled by this same 'show' method
            // For notification settings, the Alpine.js component directly fetches/posts.
        }

        // Return the main view with all necessary data
        return view('profile.account', compact('user', 'favorites', 'notificationSettings'));
    }

    /**
     * Update the user's profile details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
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

    /**
     * Update the user's notification settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'receive_in_app' => 'boolean',
                'receive_chat' => 'boolean',
                'order_status_updates' => 'boolean',
                'offers_discounts' => 'boolean',
                'viewed_products_offers' => 'boolean',
            ]);

            // Merge new settings with existing ones (important for partial updates)
            // $user->notification_settings is already an array because of the cast.
            $currentSettings = $user->notification_settings ?? $this->getDefaultNotificationSettings();
            $newSettings = array_merge($currentSettings, $validatedData);

            $user->notification_settings = $newSettings;
            $user->save(); // Save the user model, which will serialize notification_settings to JSON

            return response()->json(['message' => __('messages.notifications_updated_success'), 'settings' => $user->notification_settings]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating notification settings: ' . $e->getMessage());
            return response()->json(['message' => __('messages.notifications_updated_error')], 500);
        }
    }

    /**
     * Define default notification settings.
     * This is useful if a user doesn't have any settings saved yet.
     *
     * @return array
     */
    protected function getDefaultNotificationSettings(): array
    {
        return [
            'receive_in_app' => false, // Default to false or true as per your requirement
            'receive_chat' => false,
            'order_status_updates' => false,
            'offers_discounts' => false,
            'viewed_products_offers' => false,
        ];
    }
}