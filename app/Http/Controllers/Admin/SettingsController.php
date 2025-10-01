<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.settings.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        $filename = time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
        $request->file('profile_picture')->move(public_path('storage/profile_pictures'), $filename);
        $path = 'profile_pictures/' . $filename;
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صورة الملف الشخصي بنجاح!',
            'profile_picture_url' => asset('storage/' . $path),
        ]);
    }

    public function removeProfilePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);

            return response()->json([
                'success' => true,
                'message' => 'تمت إزالة صورة الملف الشخصي بنجاح.',
                'profile_picture_url' => asset('images/Unknown_person.jpg'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'لا توجد صورة لإزالتها.',
        ], 400);
    }

   public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // 'current_password' and 'password' rules will be conditional
        ];

        // --- IMPORTANT CHANGE HERE ---
        // Only add password-related rules if 'password' field is provided and not empty
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
            // If a new password is provided, then current_password is required
            $rules['current_password'] = ['required', 'string', 'min:8'];
        }

        $validatedData = $request->validate($rules);

        // Update basic info
        $user->full_name = $validatedData['full_name'];
        $user->phone_number = $validatedData['phone_number'];
        $user->email = $validatedData['email'];

        // Handle password update ONLY if 'password' was actually provided in the request
        if ($request->filled('password')) {
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json(['errors' => ['current_password' => ['كلمة المرور الحالية غير صحيحة.']]], 422);
            }
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return response()->json(['message' => 'تم تحديث الملف الشخصي بنجاح.']);
    }
}
