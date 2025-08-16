<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\BusinessData;
use App\Models\Document;


class ProfileController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @return \Illuminate\View\View
     */
  public function show(Request $request)
    {
        Paginator::useTailwind();

        // Fix: Correctly eager load the nested relationship.
        // The documents are related to the business data, which is related to the user.
        $user = Auth::user()->load('business.documents');

        $names = preg_split('/\s+/', trim($user->full_name), -1, PREG_SPLIT_NO_EMPTY);
        $user->first_name = $names[0] ?? '';
        $user->last_name = implode(' ', array_slice($names, 1));

        $favorites = collect();
        if ($user) {
            $favorites = $user->favorites()->with('product.subCategory.category')->paginate(3);
        }

        $notificationSettings = $user->notification_settings ?? $this->getDefaultNotificationSettings();

        if ($request->ajax()) {
            if ($request->path() === 'profile/favorites') {
                return view('partials.favorites_list', compact('favorites'));
            }
        }

        // Logic for Cart
        $cartItems = collect();
        $cart = null;

        if (Auth::check()) {
            $cart = Auth::user()->cart;
        } else {
            $sessionId = Session::getId();
            $cart = Cart::where('session_id', $sessionId)
                        ->where('status', 'active')
                        ->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with('product')->get();
        }

        // Logic for Notifications
        $notifications = collect();
        $unreadNotificationCount = 0;
        if (Auth::check()) {
            $notifications = Auth::user()->notifications()->latest()->take(5)->get();
            $unreadNotificationCount = Auth::user()->unreadNotifications->count();
        }

        $section = $request->query('section');
        $products = collect();
        $businessData = null; // Initialize the variable for business data

        if (Auth::check()) {
            $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();
            $offers = Auth::user()
                ->offers()
                ->with([
                    'product.offer',
                    'product.subCategory.category'
                ])
                ->paginate(20);

            if (Auth::user()->account_type === 'supplier') {
                // Now retrieve the business data from the eager-loaded user model
                $businessData = $user->business;

                if ($businessData) {
                    $products = $businessData->products()->paginate(3);
                }
            }
        }

        // Pass the business data and documents to the view
        // The documents are now accessible via $businessData->documents
        return view('profile.account',
            compact('user',
                'favorites',
                'notificationSettings',
                'section',
                'cartItems',
                'notifications',
                'unreadNotificationCount',
                'products',
                'offers',
                'businessData' // This is the business data loaded with documents
            ));
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

    // 1. Define base validation rules for all users
    $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone_number' => 'nullable|string|digits:9|unique:users,phone_number,' . $user->id,
        'address' => 'nullable|string|max:255',
    ];
    
    // 2. Define base validation messages
    $messages = [
        'first_name.required' => __('messages.first_name_required'),
        'last_name.required' => __('messages.last_name_required'),
        'email.required' => __('messages.email_required'),
        'email.email' => __('messages.email_invalid'),
        'email.unique' => __('messages.email_unique'),
        'phone_number.digits' => __('messages.phone_number_digits'),
        'phone_number.unique' => __('messages.phone_number_unique'),
    ];

    // 3. Conditionally add supplier-specific rules and messages
    if ($user->account_type === 'supplier') {
        $rules = array_merge($rules, [
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'experience_years' => 'nullable|integer|min:0',
            'created_at' => 'nullable|date',
            'product_category' => 'nullable|string', 
            'certificate' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'national_id' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'commercial_register' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'national_address' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'iban' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'training_certificate' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ]);
        
        $messages = array_merge($messages, [
            'company_name.required' => 'Company name is required.',
            'product_category.required' => 'Product category is required.',
            'certificate.mimes' => 'The certificate must be a file of type: jpeg, png, pdf.',
            // Add more specific messages for supplier fields
        ]);
    }

    // 4. Create the validator object with both rules and messages
    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    // 5. Update the core user model
    $user->update([
        'full_name' => $request->input('first_name') . ' ' . $request->input('last_name'),
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'address' => $request->address,
    ]);

    // 6. Conditionally update supplier details and documents
    if ($user->account_type === 'supplier') {
        $businessData = $user->business()->firstOrNew(['user_id' => $user->id]);
        $businessData->fill([
            'company_name' => $request->company_name,
            'supplier_desc' => $request->description,
            'exp_years' => $request->experience_years,
            'company_created_at' => $request->created_at,
            'product_category_id' => $request->product_category,
        ]);
        $businessData->save();

        $documentFields = [
            'certificate',
            'national_id',
            'commercial_register',
            'national_address',
            'iban',
            'training_certificate'
        ];
        
        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = $file->store('supplier_documents', 'public');
                $oldDocument = Document::where('user_id', $user->id)
                    ->where('document_type', $field)
                    ->first();
                    
                if ($oldDocument) {
                    Storage::disk('public')->delete($oldDocument->file_path);
                    $oldDocument->delete();
                }

                Document::create([
                    'user_id' => $user->id,
                    'document_type' => $field,
                    'file_path' => $path,
                ]);
            }
        }
    }
    
    return response()->json([
        'success' => true,
        'message' => __('messages.account_details_updated_successfully') 
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

            $currentSettings = $user->notification_settings ?? $this->getDefaultNotificationSettings();
            $newSettings = array_merge($currentSettings, $validatedData);

            $user->notification_settings = $newSettings;
            $user->save(); 

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
            'receive_in_app' => false, 
            'receive_chat' => false,
            'order_status_updates' => false,
            'offers_discounts' => false,
            'viewed_products_offers' => false,
        ];
    }
}