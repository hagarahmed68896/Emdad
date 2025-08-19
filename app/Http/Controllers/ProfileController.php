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
use App\Models\Category;

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

    // جلب المستخدم مع بيانات البزنس والمستندات
    $user = Auth::user()->load('business.documents');

    // تقسيم الاسم الكامل إلى اسم أول واسم عائلة
    $names = preg_split('/\s+/', trim($user->full_name), -1, PREG_SPLIT_NO_EMPTY);
    $user->first_name = $names[0] ?? '';
    $user->last_name = implode(' ', array_slice($names, 1));

    // المفضلات
    $favorites = collect();
    if ($user) {
        $favorites = $user->favorites()->with('product.subCategory.category')->paginate(3);
    }

    $notificationSettings = $user->notification_settings ?? $this->getDefaultNotificationSettings();

    if ($request->ajax() && $request->path() === 'profile/favorites') {
        return view('partials.favorites_list', compact('favorites'));
    }

    // عربة التسوق
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

    // الإشعارات
    $notifications = collect();
    $unreadNotificationCount = 0;
    if (Auth::check()) {
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        $unreadNotificationCount = Auth::user()->unreadNotifications->count();
    }

    $section = $request->query('section');
    $products = collect();
    $businessData = null;
    $offers = collect();

    if (Auth::check()) {
        $favorites = Auth::user()->favorites()->with('product.subCategory.category')->get();
        $offers = Auth::user()
            ->offers()
            ->with(['product.offer', 'product.subCategory.category'])
            ->paginate(20);

        if ($user->account_type === 'supplier') {
            $businessData = $user->business;

            if ($businessData) {
                $products = $businessData->products()->paginate(3);
            }
        }
    }



    // إرسال البيانات إلى الـ Blade
    return view('profile.account', compact(
        'user',
        'favorites',
        'notificationSettings',
        'section',
        'cartItems',
        'notifications',
        'unreadNotificationCount',
        'products',
        'offers',
        'businessData',
 
    ));
}


    // In your App\Http\Controllers\ProfileController.php

private function getDocumentType($key)
{
    switch ($key) {
        case 'national_id':
            return 'National ID';
        case 'commercial_registration':
            return 'Commercial Registration';
        case 'national_address':
            return 'National Address';
        case 'iban':
            return 'IBAN';
        case 'tax_certificate':
            return 'Tax Certificate';
        case 'certificate': 
            return 'Certificate';
        default:
            return null;
    }
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

    // 1. Base validation rules
    $rules = [
        'first_name'   => 'required|string|max:255',
        'last_name'    => 'nullable|string|max:255',
        'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone_number' => 'nullable|string|digits:9|unique:users,phone_number,' . $user->id,
        'address'      => 'nullable|string|max:255',
    ];

    $messages = [
        'first_name.required' => __('messages.first_name_required'),
        'email.required'      => __('messages.email_required'),
        'email.email'         => __('messages.email_invalid'),
        'email.unique'        => __('messages.email_unique'),
        'phone_number.digits' => __('messages.phone_number_digits'),
        'phone_number.unique' => __('messages.phone_number_unique'),
    ];

    // 2. Supplier-specific rules
    if ($user->account_type === 'supplier') {
        $rules = array_merge($rules, [
            'business.company_name'           => 'required|string|max:255',
            'business.description'            => 'nullable|string|max:1000',
            'business.experience_years'       => 'nullable|integer|min:0',
            'business.start_date'             => 'nullable|date',
            'business.product_category'       => 'nullable|string',
            'business.national_id'            => 'required|string|max:255',
            'business.commercial_registration'=> 'required|string|max:255',
            'business.national_address'       => 'required|string|max:255',
            'business.iban'                   => 'required|string|max:255',
            'business.tax_certificate'        => 'required|string|max:255',
            'documents.*'                     => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $messages = array_merge($messages, [
            'business.company_name.required'            => 'Company name is required.',
            'business.national_id.required'             => 'National ID is required.',
            'business.commercial_registration.required' => 'Commercial registration is required.',
            'business.national_address.required'        => 'National address is required.',
            'business.iban.required'                    => 'IBAN is required.',
            'business.tax_certificate.required'         => 'Tax certificate number is required.',
        ]);
    }

    // 3. Validate
    $validated = $request->validate($rules, $messages);

    // 4. Update user
    $user->update([
        'full_name'    => $request->input('first_name') . ' ' . $request->input('last_name'),
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'address'      => $request->address,
    ]);

    // 5. Update supplier business + documents
    if ($user->account_type === 'supplier') {
        $businessData = $user->business()->firstOrNew(['user_id' => $user->id]);

        $businessData->fill([
            'company_name'           => $request->input('business.company_name'),
            'description'            => $request->input('business.description'),
            'experience_years'       => $request->input('business.experience_years'),
            'national_id'            => $request->input('business.national_id'),
            'commercial_registration'=> $request->input('business.commercial_registration'),
            'national_address'       => $request->input('business.national_address'),
            'iban'                   => $request->input('business.iban'),
            'tax_certificate'        => $request->input('business.tax_certificate'),
            'start_date'             => $request->input('business.start_date'),
        ]);

        if ($request->filled('business.start_date')) {
            $businessData->start_date = \Carbon\Carbon::parse($request->input('business.start_date'))->format('Y-m-d H:i:s');
        }

        $businessData->save();

        // Handle documents
        $documentTypes = [
            'national_id'            => 'National ID',
            'commercial_registration'=> 'Commercial Registration',
            'national_address'       => 'National Address',
            'iban'                   => 'IBAN',
            'tax_certificate'        => 'Tax Certificate',
            'certificate'            => 'Certificate',
        ];

        

     // In your controller or service class where you handle the upload
foreach ($request->file('documents') as $key => $file) {
    if  ($file->isValid()) {
        $docType = $this->getDocumentType($key); // A helper function to map keys to document names

        // Store the file and get the hashed path
        $path = $file->store('supplier_documents', 'public');

        // Find or create the document record and save both the path and the original name
 $user->business->documents()->updateOrCreate(
    ['document_name' => $docType],
    [
        'supplier_id'   => $user->id, // 👈 required for new rows
        'file_path'     => $path,
        'original_name' => $file->getClientOriginalName(),
        'status'        => 'pending'
    ]
);

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
        $validator = Validator::make(
            $request->all(),
            [
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
            ]
        );

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
