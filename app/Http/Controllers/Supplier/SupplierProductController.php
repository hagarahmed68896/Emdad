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
use App\Models\Offer;
use Illuminate\Support\Facades\Log;

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
        // ✅ File size limit in KB (e.g., 5120 KB = 5 MB)
        $maxFileSizeKB = 5120;

        // ✅ Validate product + offer fields with max size rule
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'price'             => 'required|numeric|min:0',
            'model_number'      => ['nullable', 'string', Rule::unique('products', 'model_number')],
            'sub_category_id'   => 'required|exists:sub_categories,id',
            'image'             => "nullable|image|max:$maxFileSizeKB",
            'images.*'          => "nullable|image|max:$maxFileSizeKB",
            'description'       => 'nullable|string',
            'min_order_quantity'=> 'nullable|integer',
            'preparation_days'  => 'nullable|integer',
            'shipping_days'     => 'nullable|integer',
            'production_capacity'=> 'nullable|string',
            'product_weight'    => 'nullable|numeric',
            'package_dimensions'=> 'nullable|string',
            'attachments'       => "nullable|file|mimes:pdf,jpg,jpeg,png|max:$maxFileSizeKB",
            'material_type'     => 'nullable|string',
            'available_quantity'=> 'nullable|integer',
            'sizes'             => 'nullable|array',
            'colors'            => 'nullable|array',
            'wholesale_from'    => 'nullable|array',
            'wholesale_to'      => 'nullable|array',
            'wholesale_price'   => 'nullable|array',
            'product_status'    => 'nullable|string|in:ready_for_delivery,made_to_order',

            'offer_name'        => 'nullable|string|max:255',
            'offer_description' => 'nullable|string',
            'offer_image'       => "nullable|image|max:$maxFileSizeKB",
            'discount_percent'  => 'nullable|integer|min:0|max:100',
            'offer_start'       => 'nullable|date',
            'offer_end'         => 'nullable|date|after_or_equal:offer_start',
        ]);

        // ✅ Extra safety: manually check file sizes
        foreach (['image', 'offer_image', 'attachments'] as $singleFileField) {
            if ($request->hasFile($singleFileField) && $request->file($singleFileField)->getSize() > ($maxFileSizeKB * 1024)) {
                return response()->json(['error' => "$singleFileField size exceeds {$maxFileSizeKB}KB"], 422);
            }
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->getSize() > ($maxFileSizeKB * 1024)) {
                    return response()->json(['error' => "One of the gallery images exceeds {$maxFileSizeKB}KB"], 422);
                }
            }
        }

        // ✅ Generate slug
        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

        // ✅ Handle attachments
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments')->store('attachments', 'public');
        }

        // ✅ Multiple images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('products', 'public');
            }
        }
        $data['images'] = $images;

        // ✅ Main image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif (!empty($images)) {
            $data['image'] = $images[0];
        }

        // ✅ Wholesale tiers
        $wholesaleTiers = [];
        $from    = $request->input('wholesale_from', []);
        $to      = $request->input('wholesale_to', []);
        $prices  = $request->input('wholesale_price', []);

        for ($i = 0; $i < count($from); $i++) {
            if (!empty($from[$i]) || !empty($to[$i]) || !empty($prices[$i])) {
                $wholesaleTiers[] = [
                    'from'  => $from[$i],
                    'to'    => $to[$i],
                    'price' => $prices[$i],
                ];
            }
        }
        $data['price_tiers'] = $wholesaleTiers;

        // ✅ Colors
$colors = collect($request->input('colors'))
    ->map(function ($colorJson) {
        $color = json_decode($colorJson, true);

        if (!empty($color['image']) && str_starts_with($color['image'], 'data:image')) {
            // Extract base64 data
            $image_parts = explode(";base64,", $color['image']);
            $image_base64 = base64_decode($image_parts[1]);
            
            // Create a unique name for the file
            $extension = explode('/', mime_content_type($color['image']))[1];
            $fileName = 'color_images/' . uniqid() . '.' . $extension;

            // Save file in storage
            Storage::disk('public')->put($fileName, $image_base64);

            // Store the path instead of base64 text
            $color['image'] = $fileName;
        }

        return $color;
    })
    ->toArray();

$data['colors'] = $colors;



        // ✅ Assign business
        $user = Auth::user();
        if (!$user || !$user->business) {
            return response()->json(['message' => 'لا يمكن حفظ المنتج: المورّد غير معرف.'], 422);
        }
        $data['business_data_id'] = $user->business->id;

        $data['min_order_quantity'] = $data['min_order_quantity'] ?? 1;

        $data['is_feature'] = true;

        $product = Product::create($data);

        // ✅ Offer
        if ($request->filled('offer_name') || $request->filled('discount_percent')) {
            $offerData = [
                'product_id'       => $product->id,
                'name'             => $request->input('offer_name'),
                'description'      => $request->input('offer_description'),
                'discount_percent' => $request->input('discount_percent'),
                'offer_start'      => $request->input('offer_start'),
                'offer_end'        => $request->input('offer_end'),
            ];
            if ($request->hasFile('offer_image')) {
                $offerData['image'] = $request->file('offer_image')->store('offers', 'public');
            }
            \App\Models\Offer::create($offerData);
        }

        return response()->json(['success' => 'تم حفظ المنتج بنجاح']);
    }

    public function edit(Product $product)
{
    // Eager load relationships to avoid multiple queries in the view
    $product->load(['subCategory.category']);

    // Fetch all categories and their subcategories for the dropdowns
    $categories = Category::with('subCategories')->get();

    

    // Make sure 'colors' is cast to an array in your Product model
    $productColors = $product->colors ?? [];
if (is_string($productColors)) {
    $decoded = json_decode($productColors, true);
    $productColors = is_array($decoded) ? $decoded : [];
}
    // Format the colors array correctly for the front-end
  $productColorsJson = array_map(function($color) {
    $imagePath = $color['image'] ?? '';

    if (str_starts_with($imagePath, 'data:image')) {
        $formattedImage = $imagePath;
    } 
    else if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
        // Already a full URL — don't prepend asset()
        $formattedImage = $imagePath;
    } 
    else {
        // Prepend asset URL only if not full URL
        $formattedImage = asset('storage/' . ltrim($imagePath, '/'));
    }

    return [
        'name' => $color['name'] ?? '',
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
    Log::info('Update method called for product id: ' . $product->id);

    $maxFileSizeKB = 5120;

    // ✅ Validate input
    $data = $request->validate([
        'name'               => 'required|string|max:255',
        'price'              => 'required|numeric|min:0',
        'model_number'       => 'nullable|string',
        'sub_category_id'    => 'required|exists:sub_categories,id',
        'image'              => "nullable|image|max:$maxFileSizeKB",
        'images.*'           => "nullable|image|max:$maxFileSizeKB",
        'description'        => 'nullable|string',
        'min_order_quantity' => 'nullable|integer|min:1',
        'preparation_days'   => 'nullable|integer|min:0',
        'shipping_days'      => 'nullable|integer|min:0',
        'production_capacity'=> 'nullable|string',
        'product_weight'     => 'nullable|numeric|min:0',
        'package_dimensions' => 'nullable|string',
        'attachments'        => "nullable|file|mimes:pdf,jpg,jpeg,png|max:$maxFileSizeKB",
        'material_type'      => 'nullable|string',
        'available_quantity' => 'nullable|integer|min:0',
        'sizes'              => 'nullable|array',
        'colors'             => 'nullable|array',
        'wholesale_from'     => 'nullable|array',
        'wholesale_from.*'   => 'nullable|integer|min:1',
        'wholesale_to'       => 'nullable|array',
        'wholesale_to.*'     => 'nullable|integer|min:1',
        'wholesale_price'    => 'nullable|array',
        'wholesale_price.*'  => 'nullable|numeric|min:0',
        'existing_images'    => 'nullable|array',
        'existing_images.*'  => 'string',
        'product_status'     => 'nullable|string|in:ready_for_delivery,made_to_order',

        // Offer
        'offer_name'         => 'nullable|string|max:255',
        'offer_description'  => 'nullable|string',
        'offer_image'        => "nullable|image|max:$maxFileSizeKB",
        'discount_percent'   => 'nullable|integer|min:0|max:100',
        'offer_start'        => 'nullable|date',
        'offer_end'          => 'nullable|date|after_or_equal:offer_start',
    ]);
    Log::info('Validated data:', $data);

    // -------------------------
    // ✅ Start with full data (include 'name')
    // -------------------------
    $productData = $data;

    // -------------------------
    // ✅ Slug update if name changes
    // -------------------------
    if ($product->name !== $data['name']) {
        $productData['slug'] = Str::slug($data['name']) . '-' . Str::random(8);
    }

    // -------------------------
    // ✅ Main image
    // -------------------------
    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $productData['image'] = $request->file('image')->store('products', 'public');
    }

    // -------------------------
    // ✅ Attachments
    // -------------------------
    if ($request->hasFile('attachments')) {
        if ($product->attachments) {
            Storage::disk('public')->delete($product->attachments);
        }
        $productData['attachments'] = $request->file('attachments')->store('attachments', 'public');
    }

    // -------------------------
    // ✅ Gallery images
    // -------------------------

    $existingImages = $request->input('existing_images', []);
    $updatedImages = $existingImages;

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $updatedImages[] = $file->store('products', 'public');
        }
    }

    // Remove deleted images from storage
    if ($product->images) {
        $imagesToDelete = array_diff($product->images, $existingImages);
        foreach ($imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    $productData['images'] = $updatedImages;
    if (!isset($productData['image']) && count($productData['images'])) {
        $productData['image'] = $productData['images'][0];
    }

    // -------------------------
    // ✅ Wholesale tiers
    // -------------------------
    $wholesaleTiers = [];
    foreach ($request->input('wholesale_from', []) as $i => $from) {
        $to = $request->input("wholesale_to.$i");
        $price = $request->input("wholesale_price.$i");
        if (!empty($from) && !empty($to) && !empty($price)) {
            $wholesaleTiers[] = compact('from', 'to', 'price');
        }
    }
    $productData['price_tiers'] = $wholesaleTiers;

    // -------------------------
    // ✅ Colors
    // -------------------------
$productData['colors'] = collect($request->input('colors', []))
    ->map(function ($color) {
        if (is_string($color)) {
            $decoded = json_decode($color, true);
            $color = is_array($decoded) ? $decoded : [];
        }

        // ✅ Prefer image if available
        $image = $color['image'] ?? null;

        if (!empty($image) && str_starts_with($image, 'data:image')) {
            // Handle new base64 upload
            $image_parts = explode(";base64,", $image);
            $image_base64 = base64_decode($image_parts[1]);

            $extension = explode('/', mime_content_type($image))[1];
            $fileName = 'color_images/' . uniqid() . '.' . $extension;

            Storage::disk('public')->put($fileName, $image_base64);

            $image = $fileName;
        }

        return [
            'name'  => $color['name'] ?? null,
            // ✅ if image exists, save it; otherwise fall back to hex
            'hex'   => empty($image) ? ($color['hex'] ?? null) : null,
            'image' => $image,
        ];
    })
    ->toArray();



    // -------------------------
    // ✅ Default min_order_quantity if missing
    // -------------------------
    $productData['min_order_quantity'] = $productData['min_order_quantity'] ?? 1;

    // -------------------------
    // ✅ Save product changes
    // -------------------------
    $product->update($productData);
    Log::info('Product updated successfully.');

    // -------------------------
    // ✅ Offer create/update/delete
    // -------------------------
    $hasOfferData = $request->filled('offer_name') || $request->filled('discount_percent') || $request->hasFile('offer_image');

    if ($hasOfferData) {
        $offerData = [
            'name'             => $request->input('offer_name') ?? $product->offer->name ?? '',
            'description'      => $request->input('offer_description'),
            'discount_percent' => $request->input('discount_percent'),
            'offer_start'      => $request->input('offer_start'),
            'offer_end'        => $request->input('offer_end'),
        ];

        if ($request->hasFile('offer_image')) {
            if ($product->offer && $product->offer->image) {
                Storage::disk('public')->delete($product->offer->image);
            }
            $offerData['image'] = $request->file('offer_image')->store('offers', 'public');
        }

        if ($product->offer) {
            $product->offer->update($offerData);
            Log::info('Offer updated successfully.');
        } else {
            $offerData['product_id'] = $product->id;
            Offer::create($offerData);
            Log::info('Offer created successfully.');
        }
    } elseif ($product->offer) {
        if ($product->offer->image) {
            Storage::disk('public')->delete($product->offer->image);
        }
        $product->offer->delete();
        Log::info('Offer deleted successfully.');
    }

    return response()->json(['success' => 'تم تحديث المنتج بنجاح']);
}




    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    public function bulkUploadPage()
{
    return view('supplier.products.bulk-upload'); // Create this Blade view
}

    public function bulkUpload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,csv|max:5120', // max 5MB
    ]);

    $user = Auth::user();
    if (!$user || !$user->business) {
        return response()->json(['error' => 'لا يمكن حفظ المنتجات: المورّد غير معرف.'], 422);
    }

    try {
        \Maatwebsite\Excel\Facades\Excel::import(
            new \App\Imports\ProductsImport($user),
            $request->file('file')
        );

        return response()->json(['success' => 'تم رفع المنتجات بنجاح']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'حدث خطأ أثناء رفع المنتجات: ' . $e->getMessage()], 500);
    }
}

}
