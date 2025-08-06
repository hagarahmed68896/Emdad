<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;



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
        $data['colors'] = $request->input('colors', []);
        
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

    return view('supplier.products.edit', [
        'product' => $product,
        'categories' => $categories,
    ]);
}



public function update(Request $request, Product $product)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'model_number' => 'nullable|string',
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
    ]);

    $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . uniqid();

    // ✅ Main image
    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    // ✅ Attachment
    if ($request->hasFile('attachments')) {
        if ($product->attachments) {
            Storage::disk('public')->delete($product->attachments);
        }
        $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
    }

    // ✅ Gallery images
    $images = $product->images ?? [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $images[] = $file->store('products', 'public');
        }
    }

    // ✅ Remove any images that were deleted on the frontend
    $existingImages = $request->input('existing_images', []);
    $images = array_filter($images, function ($img) use ($existingImages) {
        return in_array($img, $existingImages);
    });

    $data['images'] = array_values($images); // re-index

    // ✅ If image not re-uploaded and original is deleted, fallback
    if (!$request->hasFile('image') && count($data['images'])) {
        $data['image'] = $data['images'][0];
    }

    // ✅ Wholesale tiers
    $wholesaleTiers = [];
    $from = $request->input('wholesale_from', []);
    $to = $request->input('wholesale_to', []);
    $prices = $request->input('wholesale_price', []);

    for ($i = 0; $i < count($from); $i++) {
        if ($from[$i] || $to[$i] || $prices[$i]) {
            $wholesaleTiers[] = [
                'from' => $from[$i],
                'to' => $to[$i],
                'price' => $prices[$i],
            ];
        }
    }

    $data['price_tiers'] = $wholesaleTiers;

    // ✅ Sizes and colors
    $data['sizes'] = $request->input('sizes', []);
    $data['colors'] = $request->input('colors', []);

    // ✅ Ensure product belongs to a supplier
    $user = Auth::user();
    if (!$user || !$user->business) {
        return response()->json([
            'message' => 'لا يمكن تعديل المنتج: المورّد غير معرف.',
        ], 422);
    }

    $data['business_data_id'] = $user->business->id;
    $data['min_order_quantity'] = $data['min_order_quantity'] ?? 1;

    // ✅ Update product
    $product->update($data);

    return response()->json([
        'success' => 'تم تحديث المنتج بنجاح',
        // 'redirect' => route('supplier.products.products')
    ]);
}


public function destroy($id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return redirect()->back()->with('success', 'Product deleted successfully');
}

}
