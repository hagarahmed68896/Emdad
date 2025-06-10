<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BusinessData;

class SupplierController extends Controller
{
     public function register(Request $request)
    {
      // Validate the request
        $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:8|confirmed',
            'national_id' => 'required|string|max:255',
            'national_id_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'commercial_registration' => 'required|string|max:255',
            'commercial_registration_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'national_address' => 'required|string|max:255',
            'national_address_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'iban' => 'required|string|max:255',
            'iban_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'tax_certificate' => 'required|string|max:255',
            'tax_certificate_attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'terms' => 'accepted',
        ]);
        // Create the user
        $user = User::create([
            'full_name' => $request->full_name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type, 
        ]);
         // Handle file uploads and save paths
        $businessData = new BusinessData();
        $businessData->user_id = $user->id; // Set the foreign key
        if ($request->hasFile('national_id_attach')) {
            $businessData->national_id_attach = $request->file('national_id_attach')->store('uploads');
        }
        if ($request->hasFile('commercial_registration_attach')) {
            $businessData->commercial_registration_attach = $request->file('commercial_registration_attach')->store('uploads');
        }
        if ($request->hasFile('national_address_attach')) {
            $businessData->national_address_attach = $request->file('national_address_attach')->store('uploads');
        }
        if ($request->hasFile('iban_attach')) {
            $businessData->iban_attach = $request->file('iban_attach')->store('uploads');
        }
        if ($request->hasFile('tax_certificate_attach')) {
            $businessData->tax_certificate_attach = $request->file('tax_certificate_attach')->store('uploads');
        }
        // Fill in other business data fields
        $businessData->national_id = $request->national_id;
        $businessData->commercial_registration = $request->commercial_registration;
        $businessData->national_address = $request->national_address;
        $businessData->iban = $request->iban;
        $businessData->tax_certificate = $request->tax_certificate;
        // Save the business data
        $businessData->save();
        // Redirect or return response
        return redirect()->route('home')->with('success', 'Registration successful!');
    
}
}