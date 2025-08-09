<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;



class SupplierProductController extends Controller
{
public function index()
{
$supplier = Auth::user()->business;

if (!$supplier) {
    $products = collect();
} else {
    $products = $supplier->products()->get();
}


    return view('supplier.products.products', compact('products'));
}


public function create()
{
        $categories = Category::with('subCategories')->get();
        return view('supplier.products.create', compact('categories'));
}

  public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'model_number' => ['nullable', 'string', Rule::unique('products', 'model_number')],
            'sub_category_id' => 'required|exists:sub_categories,id',
            'image' => 'nullable|image',
            'images.*' => 'nullable|image',
            'description' => 'nullable|string',
            'min_order_quantity' => 'nullable|integer',
            'discount_percent' => 'nullable|integer',
            'offer_start' => 'nullable|date',
            'offer_end' => 'nullable|date',
            'preparation_days' => 'nullable|integer',
            'shipping_days' => 'nullable|integer',
            'production_capacity' => 'nullable|string',
            'product_weight' => 'nullable|numeric',
            'package_dimensions' => 'nullable|string',
            'attachments' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'material_type' => 'nullable|string',
            'available_quantity' => 'nullable|integer',
            'sizes' => 'nullable|array',
            'colors' => 'nullable|array',
            'wholesale_from' => 'nullable|array',
            'wholesale_to' => 'nullable|array',
            'wholesale_price' => 'nullable|array',
            'product_status' => 'nullable|string|in:ready_for_delivery,made_to_order', 

        ],
     [
    'name.required' => __('messages.required_name'),
    'name.string' => __('messages.string_name'),
    'name.max' => __('messages.max_name'),

    'slug.required' => __('messages.required_slug'),
    'slug.unique' => __('messages.unique_slug'),

    'price.required' => __('messages.required_price'),
    'price.numeric' => __('messages.numeric_price'),
    'price.min' => __('messages.min_price'),

    'model_number.unique' => __('messages.unique_model_number'),

    'sub_category_id.required' => __('messages.required_sub_category')
]);

        // ✅ Generate a unique slug for the product
       $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . uniqid();

        // ✅ Handle attachments
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
        }

        // ✅ Handle multiple product images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('products', 'public');
            }
        }
        $data['images'] = $images;

        // ✅ Handle the main image and set a fallback
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif (!empty($images)) {
            // Use the first uploaded image as the main image if no dedicated main image is provided
            $data['image'] = $images[0];
        }

        // ✅ Automatically set `is_offer`
        $data['is_offer'] = ($request->filled('offer_start') || $request->filled('offer_end') || $request->filled('discount_percent')) ? 1 : 0;

        // ✅ Process wholesale tiers
        $wholesaleTiers = [];
        $from = $request->input('wholesale_from', []);
        $to = $request->input('wholesale_to', []);
        $prices = $request->input('wholesale_price', []);

        for ($i = 0; $i < count($from); $i++) {
            if (!empty($from[$i]) || !empty($to[$i]) || !empty($prices[$i])) {
                $wholesaleTiers[] = [
                    'from' => $from[$i],
                    'to' => $to[$i],
                    'price' => $prices[$i],
                ];
            }
        }
        $data['price_tiers'] = $wholesaleTiers;

        // ✅ Process sizes and colors (Already handled by the cast in the model)
        $data['sizes'] = $request->input('sizes', []);
        // $data['colors'] = $request->input('colors', []);
        

        $colors = collect($request->input('colors'))
    ->map(fn($colorJson) => json_decode($colorJson, true))
    ->toArray();

$data['colors'] = $colors;

        // ✅ Assign the product to the authenticated user's business
        $user = Auth::user();
        if (!$user || !$user->business) {
            return response()->json([
                'message' => 'لا يمكن حفظ المنتج: المورّد غير معرف.'
            ], 422);
        }

        $data['business_data_id'] = $user->business->id;

        // ✅ Set a default minimum order quantity
        $data['min_order_quantity'] = $data['min_order_quantity'] ?? 1;

        // ✅ Create the product
        Product::create($data);

        return response()->json([
            'success' => 'تم حفظ المنتج بنجاح',
        ]);
    }

public function edit(Product $product)
{
    // Eager load relationships to avoid multiple queries in the view
    $product->load(['subCategory.category']);

    // Fetch all categories and their subcategories for the dropdowns
    $categories = Category::with('subCategories')->get();

    // Make sure 'colors' is cast to an array in your Product model
    $productColors = $product->colors ?? [];

    // Format the colors array correctly for the front-end
    $productColorsJson = array_map(function($color) {
        $imagePath = $color['image'];

        // Check if the image is a Base64 string or a file path.
        if (str_starts_with($imagePath, 'data:image')) {
            // It's a Base64 string, use it directly.
            $formattedImage = $imagePath;
        } else {
            // It's a file path, so prepend the storage URL.
            $formattedImage = asset('storage/' . $imagePath);
        }

        return [
            'name' => $color['name'],
            'image' => $formattedImage,
        ];
    }, $productColors);

    return view('supplier.products.edit', [
        'product' => $product,
        'categories' => $categories,
        'productColorsJson' => $productColorsJson, // Pass the formatted array
    ]);
}




public function update(Request $request, Product $product)
{
    // Validate the request data
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'model_number' => 'nullable|string',
        'sub_category_id' => 'required|exists:sub_categories,id',
        'image' => 'nullable|image',
        'images.*' => 'nullable|image',
        'description' => 'nullable|string',
        'min_order_quantity' => 'nullable|integer|min:1',
        'discount_percent' => 'nullable|integer|between:0,100',
        'offer_start' => 'nullable|date',
        'offer_end' => 'nullable|date',
        'preparation_days' => 'nullable|integer|min:0',
        'shipping_days' => 'nullable|integer|min:0',
        'production_capacity' => 'nullable|string',
        'product_weight' => 'nullable|numeric|min:0',
        'package_dimensions' => 'nullable|string',
        'attachments' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        'material_type' => 'nullable|string',
        'available_quantity' => 'nullable|integer|min:0',
        'sizes' => 'nullable|array',
        'colors' => 'nullable|array',
        'wholesale_from' => 'nullable|array',
        'wholesale_from.*' => 'nullable|integer|min:1',
        'wholesale_to' => 'nullable|array',
        'wholesale_to.*' => 'nullable|integer|min:1',
        'wholesale_price' => 'nullable|array',
        'wholesale_price.*' => 'nullable|numeric|min:0',
        'existing_images' => 'nullable|array',
        'existing_images.*' => 'string',
        'product_status' => 'nullable|string|in:ready_for_delivery,made_to_order', 


    ],
     [
    'name.required' => __('messages.required_name'),
    'name.string' => __('messages.string_name'),
    'name.max' => __('messages.max_name'),

    'slug.required' => __('messages.required_slug'),
    'slug.unique' => __('messages.unique_slug'),

    'price.required' => __('messages.required_price'),
    'price.numeric' => __('messages.numeric_price'),
    'price.min' => __('messages.min_price'),

    'model_number.unique' => __('messages.unique_model_number'),

    'sub_category_id.required' => __('messages.required_sub_category')
]);

    // Check if the product name has changed and update the slug accordingly
    if ($product->name !== $data['name']) {
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(8);
    } 
    
    // --- Handle File Uploads ---

    // Handle main image
    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    // Handle attachment
    if ($request->hasFile('attachments')) {
        if ($product->attachments) {
            Storage::disk('public')->delete($product->attachments);
        }
        $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
    }

    // Handle gallery images
    $existingImages = $request->input('existing_images', []);
    $updatedImages = $existingImages; // Start with the existing images that were not removed
    
    // Add new images to the array
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $updatedImages[] = $file->store('products', 'public');
        }
    }
    
    // Delete images that are no longer in the existing_images array
    if ($product->images) {
        $imagesToDelete = array_diff($product->images, $existingImages);
        foreach ($imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    $data['images'] = $updatedImages;

    // Set the main image to the first gallery image if no main image was provided
    if (!$request->hasFile('image') && !isset($data['image']) && count($data['images'])) {
        $data['image'] = $data['images'][0];
    }
    
    // --- Handle Dynamic Data (Wholesale, Sizes, Colors) ---

    // Wholesale tiers
    $wholesaleTiers = [];
    $from = $request->input('wholesale_from', []);
    $to = $request->input('wholesale_to', []);
    $prices = $request->input('wholesale_price', []);

    // Filter out rows that are empty
    for ($i = 0; $i < count($from); $i++) {
        if (isset($from[$i], $to[$i], $prices[$i]) && $from[$i] && $to[$i] && $prices[$i]) {
            $wholesaleTiers[] = [
                'from' => $from[$i],
                'to' => $to[$i],
                'price' => $prices[$i],
            ];
        }
    }
    $data['price_tiers'] = $wholesaleTiers;

    // Sizes and colors
    $data['sizes'] = $request->input('sizes', []);
$data['colors'] = array_map(function ($color) {
    if (is_string($color)) {
        $decoded = json_decode($color, true);
        return is_array($decoded) ? $decoded : [];
    }
    return is_array($color) ? $color : [];
}, $request->input('colors', []));

    
    // Set default min_order_quantity if not provided
    $data['min_order_quantity'] = $data['min_order_quantity'] ?? 1;

    // --- Final Update ---
    $product->update($data);

    return response()->json([
        'success' => 'تم تحديث المنتج بنجاح',
    ]);
}


public function destroy($id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return redirect()->back()->with('success', 'Product deleted successfully');
}

}
