<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\NewProductFromSupplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class ProductController extends Controller
{
    /**
     * Display a listing of products with filtering, sorting, and pagination.
     * This will serve as the main product listing page (e.g., /products).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */

   public function index(Request $request)
    {
        // Initial query builders for products and filter options
        $productsQuery = Product::with('subCategory.category');
        $filterOptionsBaseQuery = Product::query();

        // === Filtering Logic ===

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
            $filterOptionsBaseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        // Subcategory filter
        if ($request->filled('sub_category_id')) {
            $productsQuery->where('sub_category_id', $request->input('sub_category_id'));
            $filterOptionsBaseQuery->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Supplier confirmed filter (using whereHas for the relationship)
        if ($request->has('supplier_confirmed') && $request->input('supplier_confirmed') == '1') {
            $productsQuery->whereHas('supplier', function ($q) {
                $q->where('supplier_confirmed', true);
            });
            $filterOptionsBaseQuery->whereHas('supplier', function ($q) {
                $q->where('supplier_confirmed', true);
            });
        }

        // Delivery date filter
        if ($request->filled('delivery_date')) {
            $productsQuery->where('estimated_delivery_days', '<=', (int) $request->input('delivery_date'));
        }

        // Quantity, price, rating
        if ($request->filled('min_order_quantity')) {
            $productsQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity'));
        }
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) $request->input('max_price'));
        }
        if ($request->filled('rating')) {
            $productsQuery->where('rating', '>=', (float) $request->input('rating'));
        }

        // === Filter by COLORS ===
        if ($request->has('colors') && is_array($request->input('colors'))) {
            $selectedColors = array_filter($request->input('colors'));
            if (!empty($selectedColors)) {
              $productsQuery->where(function ($query) use ($selectedColors) {
    foreach ($selectedColors as $value) {
        $query->orWhereJsonContains('colors', ['name' => $value]);
    }
});

            }
        }

        // === Filter by SIZES ===
        if ($request->has('sizes') && is_array($request->input('sizes'))) {
            $selectedSizes = array_filter($request->input('sizes'));
            if (!empty($selectedSizes)) {
             $productsQuery->where(function ($query) use ($selectedSizes) {
    foreach ($selectedSizes as $size) {
        $query->orWhereJsonContains('sizes', $size);
    }
});

            }
        }

        // === Filter by MATERIAL TYPE ===
        if ($request->filled('material_type')) {
            $productsQuery->where('material_type', $request->input('material_type'));
        }

        // === Filter by Description ===
        if ($request->has('description') && is_array($request->input('description'))) {
            $selectedDescriptions = array_filter($request->input('description'));
            if (!empty($selectedDescriptions)) {
                $productsQuery->whereIn('description', $selectedDescriptions);
            }
        }

// === Sorting ===
if ($request->filled('sort_by')) {
    switch ($request->input('sort_by')) {
        case 'price_asc':
            $productsQuery->orderByRaw("
                CASE 
                    WHEN offers.id IS NOT NULL THEN products.price * (1 - offers.discount_percent / 100)
                    ELSE products.price 
                END ASC
            ");
            break;
        case 'price_desc':
            $productsQuery->orderByRaw("
                CASE 
                    WHEN offers.id IS NOT NULL THEN products.price * (1 - offers.discount_percent / 100)
                    ELSE products.price 
                END DESC
            ");
            break;
        case 'latest':
            $productsQuery->orderBy('products.created_at', 'desc');
            break;
        case 'rating_desc':
            $productsQuery->orderBy('products.rating', 'desc');
            break;
        default:
            $productsQuery->orderBy('products.created_at', 'desc');
            break;
    }
} else {
    $productsQuery->orderBy('products.created_at', 'desc');
}

        $products = $productsQuery->paginate(12)->withQueryString();

        // === Fetching Available Filter Values (for UI) ===
        $availableSpecifications = [];

        // Available COLORS
        $availableSpecifications['colors'] = $filterOptionsBaseQuery->clone()
            ->pluck('colors')
            ->filter()
            ->flatten(1)
            ->unique('name')
            ->map(function ($color) {
                $imagePath = $color['image'] ?? null;
                $isBase64 = $imagePath && str_starts_with($imagePath, 'data:image');
                $imageSrc = $isBase64 ? $imagePath : asset($imagePath);
                return [
                    'name' => $color['name'] ?? null,
                    'image' => $imageSrc,
                ];
            })
            ->values()
            ->toArray();

        // Available SIZES
        $availableSpecifications['sizes'] = $filterOptionsBaseQuery->clone()
            ->pluck('sizes')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        // Available MATERIAL TYPES
        $availableSpecifications['material_type'] = $filterOptionsBaseQuery->clone()
            ->whereNotNull('material_type')
            ->pluck('material_type')
            ->unique()
            ->values()
            ->toArray();

        // === Other available filters ===
        $availableDescriptions = $filterOptionsBaseQuery->clone()
            ->distinct()
            ->whereNotNull('description')
            ->pluck('description')
            ->sort()
            ->values()
            ->toArray();

        $availableSubCategories = SubCategory::has('products')->get();

   // هنولّد 3 مواعيد توصيل مبنية على اليوم الحالي
    $daysArray = [2, 4, 7]; // أقرب مواعيد منطقية بدل [5, 10, 15]

    $deliveryOptions = collect($daysArray)->mapWithKeys(function ($days) {
        return [
            $days => [
                'label_key'  => 'delivery_by_date',
                'date_param' => Carbon::today()->addDays($days)->translatedFormat('j F Y'),
            ]
        ];
    });

$colorsData = include resource_path('data/colors.php');

$colorHexMap = [];
foreach ($colorsData as $color) {
    if (!empty($color['en']) && !empty($color['hex'])) {
        $colorHexMap[strtolower($color['en'])] = $color['hex'];
    }
    if (!empty($color['ar']) && !empty($color['hex'])) {
        $colorHexMap[mb_strtolower($color['ar'])] = $color['hex'];
    }
}



        // Category context
        $currentCategory = null;
        $currentSubCategory = null;
        if ($request->filled('sub_category_id')) {
            $currentSubCategory = SubCategory::with('category')->find($request->input('sub_category_id'));
            if ($currentSubCategory) {
                $currentCategory = $currentSubCategory->category;
            }
        }


        return view('categories.product', compact(
            'products',
            'availableSpecifications',
            'availableSubCategories',
            'availableDescriptions',
            'currentCategory',
            'currentSubCategory',
            'deliveryOptions',
            'colorHexMap',
        ));
    }




    /**
     * Display a listing of products that are currently on offer.
     * This will serve as the main page for "أفضل العروض".
     *
     * @return \Illuminate\View\View
     */
public function offers()
{
    // Fetch products that have an active, non-expired offer
    $products = Product::with(['subCategory.category', 'offer'])
       ->whereHas('offer', function ($query) {
       $query->where('start_date', '<=', now())
          ->where(function ($q) {
              $q->whereNull('offer_expires_at')
                ->orWhere('offer_expires_at', '>', now());
          });
})

        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();

    return view('offers', compact('products'));
}


    /**
     * Display the specified product by its slug.
     *
     * @param  string  $slug The slug of the product to display.
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Find the product by its 'slug' column, eager load subcategory and its parent category
        $product = Product::where('slug', $slug)->with('subCategory.category')->firstOrFail();

        // Prepare variables for breadcrumbs on the single product page
        $category = $product->subCategory->category ?? null;
        $subCategory = $product->subCategory ?? null;
        $productName = $product->name; // Or $product->name_ar if you use localized names

        // Get "You may also like" products: same subcategory AND same supplier name
 $relatedProducts = Product::where('sub_category_id', $product->sub_category_id)
                          ->where('business_data_id', $product->supplier->id)
                          ->where('id', '!=', $product->id)
                          ->inRandomOrder()
                          ->paginate(4);

        return view('categories.product_details', compact('product', 'category', 'subCategory', 'productName','relatedProducts'));
    }

    public function show_notify(Product $product)
{
    // Eager load category + subcategory
    $product->load('subCategory.category');

    $category = $product->subCategory->category ?? null;
    $subCategory = $product->subCategory ?? null;
    $productName = $product->name;

    $relatedProducts = Product::where('sub_category_id', $product->sub_category_id)
        ->where('business_data_id', $product->supplier->id)
        ->where('id', '!=', $product->id)
        ->inRandomOrder()
        ->paginate(4);

    return view('categories.product_details', compact(
        'product',
        'category',
        'subCategory',
        'productName',
        'relatedProducts'
    ));
}


    /**
     * Display a listing of featured products.
     *
     * @return \Illuminate\View\View
     */
    public function showFeaturedProducts()
    {
        $products = Product::where('is_featured', true)
                           ->orderBy('created_at', 'desc')
                           ->limit(8) // Limit to 8 products for a grid layout
                           ->get();

        return view('products.featured', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch data needed for the form, e.g., subcategories
        $subCategories = SubCategory::all();

        // Define common attributes for dropdowns/radio buttons
        // These will now be part of the 'specifications' JSON in the store method
        $colors = [ 'أحمر',
        'أزرق' ,
        'أخضر' ,
        'أصفر' , 
        'برتقالي' ,  
        'بنفسجي'  ,
        'أسود'  ,
        'أبيض'  ,
        'رمادي' ,    
        'بني'   , 
        'وردي'  ,
        'تركواز',
        'كحلي' ,
        'عنابي',
        'فضي',
        'ذهبي',
        'سماوي',      
        'أرجواني',
        'ليموني',   
        'بطيخي',
        'زيتي',      
        'أحمر نايكي',
        'أزرق أديداس'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size','Over Size'];
        $genders = ['Male', 'Female', 'Unisex', 'Kids'];
        $materials = ['Cotton', 'Polyester', 'Wool', 'Leather', 'Denim', 'Silk', 'Nylon'];

        return view('products.create', compact('subCategories', 'colors', 'sizes', 'genders', 'materials'));
    }

    /**
     * Store a newly created product in storage.
     * This method is updated to handle structured 'images' and 'specifications->colors' data.
     * It assumes the form sends data in a way that can be processed into these structures.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For single main image upload (default)

            // Validate 'images' as a nested array (e.g., images[color_name][])
            'images' => 'nullable|array',
            'images.*' => 'nullable|array', // Each key (color name) should contain an array
            'images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Actual image files

            'sub_category_id' => 'required|exists:sub_categories,id',
            'is_offer' => 'boolean',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'offer_expires_at' => 'nullable|date|after_or_equal:today',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_confirmed' => 'boolean',
            'min_order_quantity' => 'required|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_featured' => 'boolean',
            'is_main_featured' => 'boolean',
            'model_number' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'reviews_count' => 'nullable|integer|min:0',
            'estimated_delivery_days' => 'nullable|integer|min:1', // Corrected field name and type

            // Validate 'specifications.colors' as an array of objects
            'specifications.colors' => 'nullable|array',
            'specifications.colors.*.name' => 'required_with:specifications.colors.*.swatch_image|string|max:255',
            'specifications.colors.*.swatch_image' => 'required_with:specifications.colors.*.name|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            // Validate other specification fields (gender, material, size as before)
            'specifications.size' => 'nullable|array',
            'specifications.size.*' => 'string|max:255',
            'specifications.gender' => 'nullable|string|max:255',
            'specifications.material' => 'nullable|string|max:255',
            'specifications_extra' => 'nullable|array', // For any other dynamic fields not explicitly validated
        ]);

        // Handle main product image upload (if a single default image is provided)
        $mainImagePath = null;
        if ($request->hasFile('image')) {
            $mainImagePath = $request->file('image')->store('products', 'public');
        }

        // Process color-specific images and build the 'images' JSON object
        $allProductImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $colorName => $colorFiles) {
                if (is_array($colorFiles)) {
                    foreach ($colorFiles as $file) {
                        if ($file->isValid()) {
                            $allProductImages[$colorName][] = $file->store('products/gallery', 'public');
                        }
                    }
                }
            }
        }
        // Add the main image as a 'default' entry if no other color images are present or explicitly for default
        if ($mainImagePath && empty($allProductImages['default'])) {
            $allProductImages['default'] = [$mainImagePath];
        }


        // Prepare the 'specifications' array, including handling color swatches
        $specifications = [];

        // Process color specifications (name and swatch_image)
        $processedColors = [];
        if ($request->has('specifications.colors') && is_array($request->input('specifications.colors'))) {
            foreach ($request->input('specifications.colors') as $colorIndex => $colorData) {
                $colorName = $colorData['name'] ?? null;
                $swatchImageFile = $request->file("specifications.colors.{$colorIndex}.swatch_image") ?? null;

                if ($colorName && $swatchImageFile && $swatchImageFile->isValid()) {
                    $swatchImagePath = $swatchImageFile->store('products/swatches', 'public');
                    $processedColors[] = [
                        'name' => $colorName,
                        'image' => $swatchImagePath,
                    ];
                } elseif ($colorName && isset($colorData['swatch_image_path_existing'])) {
                    // Handle case where an existing swatch image path is sent (e.g., on product edit)
                    $processedColors[] = [
                        'name' => $colorName,
                        'image' => $colorData['swatch_image_path_existing'],
                    ];
                }
            }
        }
        $specifications['colors'] = $processedColors;

        // Add other specification fields from specifications group
        $specifications['size'] = $validatedData['specifications']['size'] ?? null;
        $specifications['gender'] = $validatedData['specifications']['gender'] ?? null;
        $specifications['material'] = $validatedData['specifications']['material'] ?? null;

        // Merge any other dynamic specifications from 'specifications_extra'
        if ($request->has('specifications_extra') && is_array($validatedData['specifications_extra'])) {
            $specifications = array_merge($specifications, $validatedData['specifications_extra']);
        }


        // Create the product
        $product = Product::create([
            'name' => $validatedData['name'],
            'name_en' => $validatedData['name_en'],
            'slug' => $validatedData['slug'],
            'description' => $validatedData['description'] ?? null,
            'price' => $validatedData['price'],
            'image' => $mainImagePath, // Main single image (can be default)
            'images' => $allProductImages, // JSON object of color-specific image arrays
            'sub_category_id' => $validatedData['sub_category_id'],
            'is_offer' => $validatedData['is_offer'] ?? false,
            'discount_percent' => $validatedData['discount_percent'] ?? null,
            'offer_expires_at' => $validatedData['offer_expires_at'] ?? null,
            'supplier_name' => $validatedData['supplier_name'] ?? null,
            'supplier_confirmed' => $validatedData['supplier_confirmed'] ?? false,
            'min_order_quantity' => $validatedData['min_order_quantity'],
            'rating' => $validatedData['rating'] ?? null,
            'is_featured' => $validatedData['is_featured'] ?? true,
            'is_main_featured' => $validatedData['is_main_featured'] ?? false,
            'model_number' => $validatedData['model_number'] ?? null,
            'quality' => $validatedData['quality'] ?? null,
            'shipping_cost' => $validatedData['shipping_cost'] ?? null,
            'reviews_count' => $validatedData['reviews_count'] ?? 0,
            'estimated_delivery_days' => $validatedData['estimated_delivery_days'] ?? null, // Corrected field name

            'price_tiers' => $validatedData['price_tiers'] ?? [], // Assuming form provides this as an array
            'specifications' => $specifications, // Save the consolidated specifications
        ]);

        // Notify admin(s) about the new product
        try {
            $adminUser = User::find(1);
            if ($adminUser) {
                $supplierImage = $product->supplier_image_url ?? asset('images/default_supplier.png');
                $adminUser->notify(new NewProductFromSupplier($product, $product->supplier_name ?? 'Unknown Supplier', $supplierImage));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send new product notification: " . $e->getMessage());
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

        public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    // You might have other methods like edit, update, destroy, etc., here.
public function downloadAttachment(Product $product)
{
    // 1. Check if the product has an attachment path
    if (empty($product->attachments)) {
        abort(404, 'No attachment found for this product.');
    }

    // 2. Specify the 'public' disk. This tells Laravel to look inside storage/app/public.
    $disk = 'public';
    $path = $product->attachments;

    // 3. Check if the file exists on the 'public' disk.
    if (!Storage::disk($disk)->exists($path)) {
        abort(404, 'File not found on server.');
    }

    // 4. Get the full, absolute path to the file.
    $filePath = Storage::disk($disk)->path($path);

    // 5. Get the original filename from the path.
    $fileName = basename($path);

    // 6. Return a download response.
    return response()->download($filePath, $fileName);
}
}
