<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BusinessData;
use Illuminate\Support\Facades\Storage; // IMPORTANT: Ensure this is imported for file storage
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Handle the registration of a new supplier.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        // Added 'max:2048' (2MB) for file sizes to prevent excessively large uploads.
        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:10|unique:users,phone_number', // Matches your form's name="phone_number"
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|string|max:255',
            'national_id_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'commercial_registration' => 'required|string|max:255',
            'commercial_registration_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'national_address' => 'required|string|max:255',
            'national_address_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'iban' => 'required|string|max:255',
            'iban_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tax_certificate' => 'required|string|max:255',
            'tax_certificate_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'terms' => 'accepted',
        ]);

        try {
            // 1. Create the User record
            // 'company_name' logically belongs to the User, as a general attribute of the account.
            $user = User::create([
                'full_name' => $request->full_name,
                'company_name' => $request->company_name, // Saving company_name with the User model
                'email' => $request->email,
                'phone_number' => $request->phone_number, // Ensure this matches your User model's column
                'password' => Hash::make($request->password),
                'account_type' => $request->account_type,
            ]);

            // 2. Prepare the BusinessData record, linked to the new user
            $businessData = new BusinessData();
            $businessData->user_id = $user->id; // Link to the newly created user

            // Helper closure to handle file uploads and return only the filename
            $uploadFileAndGetFilename = function ($fileInputName) use ($request) {
                if ($request->hasFile($fileInputName)) {
                    // Store the file in the 'uploads' directory on the 'public' disk.
                    // This makes the file publicly accessible via /storage/uploads/filename.
                    $path = $request->file($fileInputName)->store('uploads', 'public');
                    // Return only the base filename to store in the database.
                    return basename($path);
                }
                return null; // No file uploaded for this field
            };

            // 3. Process each file attachment and save its filename to BusinessData
            $businessData->national_id_attach = $uploadFileAndGetFilename('national_id_attach');
            $businessData->commercial_registration_attach = $uploadFileAndGetFilename('commercial_registration_attach');
            $businessData->national_address_attach = $uploadFileAndGetFilename('national_address_attach');
            $businessData->iban_attach = $uploadFileAndGetFilename('iban_attach');
            $businessData->tax_certificate_attach = $uploadFileAndGetFilename('tax_certificate_attach');

            // 4. Fill in other business-specific data fields
            // NOTE: company_name is NOT saved here, as it's already with the User model.
            $businessData->national_id = $request->national_id;
            $businessData->commercial_registration = $request->commercial_registration;
            $businessData->national_address = $request->national_address;
            $businessData->iban = $request->iban;
            $businessData->tax_certificate = $request->tax_certificate;

            // 5. Save the BusinessData record to the database
            $businessData->save();


            return redirect()->route('home')->with('success', 'Registration successful!');

        } catch (\Exception $e) {
            // Catch any errors during the process, log them, and provide user feedback
            Log::error('Supplier registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(), 
            ]);

            return back()->withInput()->withErrors([
                'registration_error' => 'An error occurred during registration. Please review your details and try again. If the problem persists, please contact support.'
            ]);
        }
    }
}
