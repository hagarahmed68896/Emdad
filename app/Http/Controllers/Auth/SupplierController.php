<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BusinessData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 

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
        // dd($request->all());

        // Validate the incoming request data
        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|digits:9|unique:users,phone_number',
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
            'account_type' => 'required|in:supplier', 
        ],
       [
            'full_name.required' => __('messages.nameError'),
            'email.required' => __('messages.emailError'),
            'email.email' => __('messages.emailValid'),
            'email.unique' => __('messages.emailUnique'),
            'password.min' => __('messages.passwordMin'),
            'password.string' => __('messages.passwordString'),
            'password.regex'=> __('messages.passwordRegex'),
            'password.confirmed' => __('messages.passwordConfirm'),
            'phone_number.required' => __('messages.phoneMSG'),
            'phone_number.unique'=> __('messages.phone_number_Unique'),
            'phone_number.digits' => __('messages.phone_number_max'),
            'terms.accepted' => __('messages.acceptTermsError'),
        ]);

        // This array will keep track of uploaded file paths for potential deletion on rollback
        $uploadedFilePaths = [];

        // Helper closure to handle file uploads and return only the filename
        $uploadFileAndGetFilename = function ($fileInputName) use ($request, &$uploadedFilePaths) {
            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                if (is_array($file)) {
                    $file = reset($file);
                }
                // Store the file in the 'uploads' directory on the 'public' disk.
                $path = $file->store('uploads', 'public');
                $uploadedFilePaths[] = $path; // Store the full path for rollback
                // Return only the base filename to store in the database.
                return basename($path);
            }
            return null; // No file uploaded for this field
        };


            // <--- ADDED: Start a database transaction
            DB::beginTransaction();

            // Store files and get their filenames.
            // We do this before user creation, but track paths for rollback.
            $nationalIdAttach = $uploadFileAndGetFilename('national_id_attach');
            $commercialRegistrationAttach = $uploadFileAndGetFilename('commercial_registration_attach');
            $nationalAddressAttach = $uploadFileAndGetFilename('national_address_attach');
            $ibanAttach = $uploadFileAndGetFilename('iban_attach');
            $taxCertificateAttach = $uploadFileAndGetFilename('tax_certificate_attach');


            // 1. Create the User record
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'account_type' => $request->account_type, // <--- CHANGED: Hardcoded 'supplier' for this controller
            ]);

            // 2. Prepare the BusinessData record, linked to the new user
            $businessData = new BusinessData();
            $businessData->user_id = $user->id; // Link to the newly created user

            // 3. Assign uploaded filenames to BusinessData
            $businessData->national_id_attach = $nationalIdAttach;
            $businessData->commercial_registration_attach = $commercialRegistrationAttach;
            $businessData->national_address_attach = $nationalAddressAttach;
            $businessData->iban_attach = $ibanAttach;
            $businessData->tax_certificate_attach = $taxCertificateAttach;

            // 4. Fill in other business-specific data fields
            $businessData->national_id = $request->national_id;
            $businessData->company_name = $request->company_name;
            $businessData->commercial_registration = $request->commercial_registration;
            $businessData->national_address = $request->national_address;
            $businessData->iban = $request->iban;
            $businessData->tax_certificate = $request->tax_certificate;

            // 5. Save the BusinessData record to the database
            $businessData->save();

            // <--- ADDED: Commit the transaction if all operations are successful
            DB::commit();

      Auth::login($user); // User is logged in on the server

        return response()->json([
            'success' => true,
            // You can optionally return user data here if needed for client-side state updates
            // 'user' => $user->only(['id', 'full_name', 'email', 'account_type']),
        ], 200);
        } 
    
    }