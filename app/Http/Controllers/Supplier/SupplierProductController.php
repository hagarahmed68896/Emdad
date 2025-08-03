<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;

class SupplierProductController extends Controller
{
    public function index()
    {
        $supplierId = Auth::user()->business_data_id;

        $products = Product::where('business_data_id', $supplierId)->get();

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

}
