<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BusinessData;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class SupplierController extends Controller
{
    /**
     * Handle the registration of a new supplier.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // ✅ 1) Validate request
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
        ], [
            'full_name.required' => __('messages.nameError'),
            'email.required' => __('messages.emailError'),
            'email.email' => __('messages.emailValid'),
            'email.unique' => __('messages.emailUnique'),
            'password.min' => __('messages.passwordMin'),
            'password.string' => __('messages.passwordString'),
            'password.confirmed' => __('messages.passwordConfirm'),
            'phone_number.required' => __('messages.phoneMSG'),
            'phone_number.unique'=> __('messages.phone_number_Unique'),
            'phone_number.digits' => __('messages.phone_number_max'),
            'terms.accepted' => __('messages.acceptTermsError'),
        ]);

        DB::beginTransaction();

        try {
            // ✅ 2) Create user
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'account_type' => $request->account_type,
            ]);

            // ✅ 3) Save business data
            $businessData = BusinessData::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'national_id' => $request->national_id,
                'commercial_registration' => $request->commercial_registration,
                'national_address' => $request->national_address,
                'iban' => $request->iban,
                'tax_certificate' => $request->tax_certificate,
            ]);

            // ✅ 4) Save uploaded documents in `documents` table
            $documentsMap = [
                'national_id_attach' => 'National ID',
                'commercial_registration_attach' => 'Commercial Registration',
                'national_address_attach' => 'National Address',
                'iban_attach' => 'IBAN',
                'tax_certificate_attach' => 'Tax Certificate',
            ];

            foreach ($documentsMap as $field => $name) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $storedPath = $file->store('uploads/documents', 'public'); // Save in storage/app/public/uploads/documents
                    $relativePath = 'storage/' . $storedPath; // Public URL path

                    Document::create([
                        'document_name' => $name,
                        'supplier_id' => $user->id,
                        'file_path' => $relativePath, // ✅ هذا الذي يتخزن في DB
                        'notes' => null,
                    ]);
                }
            }

            DB::commit();

            Auth::login($user);

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
