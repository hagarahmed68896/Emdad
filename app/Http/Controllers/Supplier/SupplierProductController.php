<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;


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

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    if ($request->hasFile('attachments')) {
        $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
    }
$images = [];
if ($request->hasFile('images')) {
    foreach ($request->file('images') as $file) {
        $images[] = $file->store('products', 'public');
    }
}
$data['images'] = $images;

// Optional: Fallback to use first image as the main image
if ($request->hasFile('image')) {
    $data['image'] = $request->file('image')->store('products', 'public');
} elseif (count($images)) {
    $data['image'] = $images[0];
}



    // ✅ حزم الجملة wholesale
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

    // ✅ الأحجام والألوان
    $data['sizes'] = $request->input('sizes', []);
    $data['colors'] = $request->input('colors', []);

    // ✅ المورد
    $user = Auth::user();
    // dd($user);
   if (!$user || !$user->business) {
    return response()->json([
        'message' => 'لا يمكن حفظ المنتج: المورّد غير معرف.'
    ], 422);
}

$data['business_data_id'] = $user->business->id;
$data['min_order_quantity'] = $data['min_order_quantity'] ?? 1;

    Product::create($data);

    return response()->json([
        'success' => 'تم حفظ المنتج بنجاح',
        // 'redirect' => route('supplier.products.products')
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
        'images' => 'nullable|array',
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
        'removed_images' => 'nullable|json',
    ]);

    // Handle removed images
    if ($request->filled('removed_images')) {
        $removedImages = json_decode($request->input('removed_images'), true);
        
        $existingImages = json_decode($product->images, true) ?? [];
        $updatedImages = array_values(array_diff($existingImages, $removedImages));

        foreach ($removedImages as $removedImagePath) {
            Storage::disk('public')->delete($removedImagePath);
        }

        $data['images'] = json_encode($updatedImages);
    } else {
        $data['images'] = $product->images;
    }

    // Handle new images
    if ($request->hasFile('images')) {
        $newImages = [];
        $existingImages = json_decode($data['images'], true) ?? [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $newImages[] = $path;
        }

        $data['images'] = json_encode(array_merge($existingImages, $newImages));
    }

    // Handle new attachments
    if ($request->hasFile('attachments')) {
        // Delete old attachment if it exists
        if ($product->attachments) {
            Storage::disk('public')->delete($product->attachments);
        }
        $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
    }

    // Process wholesale tiers
    $wholesaleTiers = [];
    $from = json_decode($request->input('wholesale_from', '[]'));
    $to = json_decode($request->input('wholesale_to', '[]'));
    $prices = json_decode($request->input('wholesale_price', '[]'));

    for ($i = 0; $i < count($from); $i++) {
        if ($from[$i] || $to[$i] || $prices[$i]) {
            $wholesaleTiers[] = [
                'from' => $from[$i],
                'to' => $to[$i],
                'price' => $prices[$i],
            ];
        }
    }
    $data['price_tiers'] = json_encode($wholesaleTiers);
    
    // Process sizes and colors
    $data['sizes'] = json_decode($request->input('sizes', '[]'));
    $data['colors'] = json_decode($request->input('colors', '[]'));
    
    // Update the product
    $product->update($data);

    return response()->json([
        'success' => 'تم تعديل المنتج بنجاح',
        'redirect' => route('supplier.products.products')
    ]);
}

public function destroy($id)
{
    $product = Product::findOrFail($id);
    $product->delete();

    return redirect()->back()->with('success', 'Product deleted successfully');
}

}
