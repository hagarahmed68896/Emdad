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
        // 1. Initialize queries
        $productsQuery = Product::with('subCategory.category');
        // This query will be used to determine the *available* filter options
        // It should start with general filters (search, category, etc.)
        // but NOT specific specification filters, rating, price range, etc.,
        // as those should be used to filter the *products*, not the *available options*.
        // We will apply general context filters to filterOptionsQuery.
        $filterOptionsBaseQuery = Product::query();

        // 2. Apply general contextual filters to BOTH queries
        // These filters narrow down the *set of products* for which
        // we want to display filter options.

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
            $filterOptionsBaseQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply Sub Category Filter
        if ($request->filled('sub_category_id')) {
            $productsQuery->where('sub_category_id', $request->input('sub_category_id'));
            $filterOptionsBaseQuery->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Apply Trusted Factory (supplier_confirmed)
        if ($request->has('supplier_confirmed') && $request->input('supplier_confirmed') == '1') {
            $productsQuery->where('supplier_confirmed', true);
            $filterOptionsBaseQuery->where('supplier_confirmed', true);
        }

        // Filter by Delivery Date (only for products, not for options derivation)
        // If you want delivery options to reflect current delivery filter, apply it to options query too
        if ($request->filled('delivery_date')) {
            try {
                $maxDays = (int) $request->input('delivery_date');
                $productsQuery->where('estimated_delivery_days', '<=', $maxDays);
                // $filterOptionsBaseQuery->where('estimated_delivery_days', '<=', $maxDays); // Uncomment if options should narrow based on delivery
            } catch (\Exception $e) {
                Log::warning("Invalid delivery_date (expected integer days): " . $request->input('delivery_date') . " - " . $e->getMessage());
            }
        }

        // Filter by Minimum Order Quantity (only for products, not for options derivation)
        if ($request->filled('min_order_quantity')) {
            $productsQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity'));
            // $filterOptionsBaseQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity')); // Uncomment if options should narrow based on MOQ
        }

        // Filter by Price Range (only for products, not for options derivation)
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) $request->input('min_price'));
            // $filterOptionsBaseQuery->where('price', '>=', (float) $request->input('min_price')); // Uncomment if options should narrow based on price
        }
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) $request->input('max_price'));
            // $filterOptionsBaseQuery->where('price', '<=', (float) $request->input('max_price')); // Uncomment if options should narrow based on price
        }

        // Filter by Rating (only for products, not for options derivation)
        if ($request->filled('rating')) {
            $productsQuery->where('rating', '>=', (float) $request->input('rating'));
            // $filterOptionsBaseQuery->where('rating', '>=', (float) $request->input('rating')); // Uncomment if options should narrow based on rating
        }


        // 3. Apply SPECIFIC filter values (from request) to the $productsQuery only
        // These are the filters that actually narrow down the displayed products.
        $filterableSpecifications = Config::get('products.filterable_specifications', []);
        foreach ($filterableSpecifications as $specKey => $specConfig) {
            if ($request->has($specKey) && is_array($request->input($specKey))) {
                $selectedOptions = array_filter($request->input($specKey));

                if (!empty($selectedOptions)) {
                    $productsQuery->where(function($q) use ($specKey, $selectedOptions, $specConfig) {
                        foreach ($selectedOptions as $optionValue) {
                            if ($specKey === 'colors') {
                                // For colors, query by 'name' property within the JSON array
                                $q->orWhereJsonContains("specifications->{$specKey}", ['name' => $optionValue]);
                                // Also handle cases where color is a simple string directly in the array
                                $q->orWhereJsonContains("specifications->{$specKey}", $optionValue);
                            } elseif ($specConfig['type'] === 'array_of_strings') {
                                // For array of strings (like 'size'), check if the value exists in the array
                                $q->orWhereJsonContains("specifications->{$specKey}", $optionValue);
                            } else {
                                // For single string values (gender, material, processor, etc.)
                                $q->orWhere("specifications->{$specKey}", $optionValue);
                            }
                        }
                    });
                }
            }
        }

        // Filter by Description (if it's a top-level column, not in specifications JSON)
        if ($request->has('description') && is_array($request->input('description'))) {
            $selectedDescriptions = array_filter($request->input('description'));
            if (!empty($selectedDescriptions)) {
                $productsQuery->whereIn('description', $selectedDescriptions);
            }
        }


        // 4. Apply Sorting to the main products query
        // if ($request->filled('sort_by')) {
        //     switch ($request->input('sort_by')) {
        //         case 'price_asc':
        //             $productsQuery->orderBy('price', 'asc');
        //             break;
        //         case 'price_desc':
        //             $productsQuery->orderBy('price', 'desc');
        //             break;
        //         case 'latest':
        //             $productsQuery->latest();
        //             break;
        //         case 'rating_desc':
        //             $productsQuery->orderBy('rating', 'desc');
        //             break;
        //         default:
        //             $productsQuery->latest();
        //             break;
        //     }
        // } else {
        //     $productsQuery->latest();
        // }
        // Assuming $productsQuery is your Eloquent query builder instance (e.g., Product::query())

if ($request->filled('sort_by')) {
    switch ($request->input('sort_by')) {
        case 'price_asc':
            // Sort by discounted price if an active offer exists, otherwise by original price (ASC)
            $productsQuery->orderByRaw('
                CASE
                    WHEN is_offer = 1 AND offer_expires_at > NOW() THEN price * (1 - discount_percent / 100)
                    ELSE price
                END ASC
            ');
            break;
        case 'price_desc':
            // Sort by discounted price if an active offer exists, otherwise by original price (DESC)
            $productsQuery->orderByRaw('
                CASE
                    WHEN is_offer = 1 AND offer_expires_at > NOW() THEN price * (1 - discount_percent / 100)
                    ELSE price
                END DESC
            ');
            break;
        case 'latest':
            $productsQuery->latest();
            break;
        case 'rating_desc':
            $productsQuery->orderBy('rating', 'asc');
            break;
        default:
            // Default sorting if an invalid 'sort_by' value is provided
            $productsQuery->latest();
            break;
    }
} else {
    // Default sorting if 'sort_by' is not provided in the request
    $productsQuery->latest();
}

// ... rest of your controller logic to paginate or get results ...

        // 5. Get the paginated product results
        $products = $productsQuery->paginate(12)->withQueryString();

        // 6. Define fixed data for the view
        $deliveryOptions = [
            '5' => ['label_key' => 'delivery_in_days', 'days_param' => 5],
            '10' => ['label_key' => 'delivery_in_days', 'days_param' => 10],
            '15' => ['label_key' => 'delivery_in_days', 'days_param' => 15],
        ];

        $colorHexMap = [
            'Red' => '#FF0000', 'Blue' => '#0000FF', 'Green' => '#008000', 'Yellow' => '#FFFF00',
            'Orange' => '#FFA500', 'Purple' => '#800080', 'Black' => '#000000', 'White' => '#FFFFFF',
            'Gray' => '#808080', 'Brown' => '#A52A2A', 'Pink' => '#FFC0CB', 'Turquoise' => '#40E0D0',
            'Navy' => '#000080', 'Maroon' => '#800000', 'Silver' => '#C0C0C0', 'Gold' => '#FFD700',
            'Cyan' => '#00FFFF', 'Magenta' => '#FF00FF', 'Lime' => '#00FF00', 'Teal' => '#008080',
            'Olive' => '#808000', 'Nike Red' => '#BB0000', 'Adidas Blue' => '#0050A0',
            'Pink-purpple' => '#DDB7D3', 'Caffe' => '#e9dcd9',
            // Add Arabic names if your database uses them consistently
            'أحمر' => '#FF0000', 'أزرق' => '#0000FF', 'أخضر' => '#008000', 'أصفر' => '#FFFF00',
            'برتقالي' => '#FFA500', 'بنفسجي' => '#800080', 'أسود' => '#000000', 'أبيض' => '#FFFFFF',
            'رمادي' => '#808080', 'بني' => '#A52A2A', 'وردي' => '#FFC0CB', 'تركواز' => '#40E0D0',
            'كحلي' => '#000080', 'عنابي' => '#800000', 'فضي' => '#C0C0C0', 'ذهبي' => '#FFD700',
            'سماوي' => '#00FFFF', 'أرجواني' => '#FF00FF', 'ليموني' => '#00FF00', 'بطيخي' => '#008080',
            'زيتي' => '#808000', 'أحمر نايكي' => '#BB0000', 'أزرق أديداس' => '#0050A0',
            'Blue Titanium' => '#4682B4', // Make sure this is in your map if used
            'Brown Titanium' => '#8B4513', // Make sure this is in your map if used
            'Gold Titanium' => '#FFD700', // Make sure this is in your map if used
        ];

        // 7. Generate Available Filter Options for Dynamic Specifications
        // This must be done from $filterOptionsBaseQuery which has only the broad context filters applied.
        $availableSpecifications = [];
        foreach ($filterableSpecifications as $specKey => $specConfig) {
            $options = [];
            $allExtractedValues = $filterOptionsBaseQuery->clone()
                ->select(DB::raw("JSON_EXTRACT(specifications, '$.{$specKey}') as extracted_json"))
                ->get()
                ->pluck('extracted_json')
                ->filter(); // Remove nulls

            foreach ($allExtractedValues as $jsonValue) {
                $decoded = json_decode($jsonValue, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("JSON Decode Error for key '{$specKey}': " . json_last_error_msg() . " - Value: " . $jsonValue);
                    continue; // Skip this malformed JSON value
                }

                if ($specKey === 'colors') {
                    // Handle colors which can be ['Orange'] or [{'name': 'Blue', 'swatch': '...'}]
                    if (is_array($decoded)) {
                        foreach ($decoded as $color) {
                            if (is_array($color) && isset($color['name'])) {
                                $options[$color['name']] = ['name' => $color['name'], 'swatch_image' => $color['swatch_image'] ?? null];
                            } elseif (is_string($color)) {
                                $options[$color] = ['name' => $color, 'swatch_image' => null]; // For simple string colors
                            }
                        }
                    } elseif (is_string($decoded)) { // Handle case where 'colors' might contain a single string
                        $options[$decoded] = ['name' => $decoded, 'swatch_image' => null];
                    }
                } elseif ($specConfig['type'] === 'array_of_strings' && is_array($decoded)) {
                    // Handle generic arrays of strings (e.g., size, features, storage_gb)
                    foreach ($decoded as $item) {
                        if (is_string($item) || is_numeric($item)) { // Ensure it's a scalar value
                            $options[(string) $item] = (string) $item;
                        }
                    }
                } elseif ($specConfig['type'] === 'string' && (is_string($decoded) || is_numeric($decoded) || is_bool($decoded))) {
                    // Handle simple string values (e.g., gender, material, processor)
                    if (is_bool($decoded)) {
                        $options[(string)$decoded] = $decoded ? __('messages.yes') : __('messages.no');
                    } else {
                        $options[(string) $decoded] = (string) $decoded;
                    }
                }
            }
            // Sort and store the unique options
            if ($specKey === 'colors') {
                // Sort colors by name
                uksort($options, function($a, $b) {
                    return strcasecmp($a, $b);
                });
                $availableSpecifications[$specKey] = array_values($options); // Re-index after sorting
            } else {
                $availableSpecifications[$specKey] = array_values(array_unique($options));
                sort($availableSpecifications[$specKey]);
            }
        }

        // 8. Fetch distinct descriptions (assuming it's still a top-level column for filters)
        // This should also use the $filterOptionsBaseQuery for context.
        $availableDescriptions = $filterOptionsBaseQuery->clone()->distinct()->whereNotNull('description')
                               ->pluck('description')->sort()->values()->toArray();

        // 9. Fetch available subcategories (for the subcategory filter itself)
        // This usually should list all subcategories, or subcategories that have products under current general filters.
        $availableSubCategories = SubCategory::has('products')->get(); // Only show subcategories with products


        // 10. Prepare data for breadcrumbs
        $currentCategory = null;
        $currentSubCategory = null;
        if ($request->filled('sub_category_id')) {
            $currentSubCategory = SubCategory::with('category')->find($request->input('sub_category_id'));
            if ($currentSubCategory) {
                $currentCategory = $currentSubCategory->category;
            }
        }

        // For debugging available specifications, uncomment this:
        // dd($availableSpecifications);

        // 11. Pass all necessary data to the view
        return view('categories.product', compact(
            'products',
            'availableSpecifications',
            'availableSubCategories',
            'availableDescriptions',
            'currentCategory',
            'currentSubCategory',
            'deliveryOptions',
            'colorHexMap'
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
        // Fetch products that are currently on offer and where the offer has not expired
        $products = Product::with('subCategory.category')
                           ->where('is_offer', true)
                           ->where(function ($query) {
                               $query->whereNull('offer_expires_at') // Offer never expires
                                     ->orWhere('offer_expires_at', '>', now()); // Offer expires in the future
                           })
                           ->orderBy('created_at', 'desc')
                           ->take(4) // Limiting to 4 products for a typical "featured offers" section
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
                          ->where('supplier_name', $product->supplier_name)
                          ->where('id', '!=', $product->id)
                          ->inRandomOrder()
                          ->paginate(4);

        return view('categories.product_details', compact('product', 'category', 'subCategory', 'productName','relatedProducts'));
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
                        'swatch_image' => $swatchImagePath,
                    ];
                } elseif ($colorName && isset($colorData['swatch_image_path_existing'])) {
                    // Handle case where an existing swatch image path is sent (e.g., on product edit)
                    $processedColors[] = [
                        'name' => $colorName,
                        'swatch_image' => $colorData['swatch_image_path_existing'],
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
            'is_featured' => $validatedData['is_featured'] ?? false,
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

    // You might have other methods like edit, update, destroy, etc., here.
}
