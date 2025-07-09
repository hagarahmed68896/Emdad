<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\NewProductFromSupplier;
use Illuminate\Support\Facades\Log;
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
        $productsQuery = Product::with('subCategory.category');

        // Initialize $filterOptionsQuery at the beginning to prevent "undefined variable" error.
        // This query will be used to fetch the available filter options based on the currently applied filters.
        $filterOptionsQuery = Product::query();

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
            // Apply search filter to the options query as well
            $filterOptionsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply Sub Category Filter
        if ($request->filled('sub_category_id')) {
            $productsQuery->where('sub_category_id', $request->input('sub_category_id'));
            // Apply sub_category_id filter to the options query as well
            $filterOptionsQuery->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Filter by Color (Multi-select checkbox: name="color[]" -> now in specifications JSON as array of objects)
        if ($request->has('color') && is_array($request->input('color'))) {
            $selectedColors = array_filter($request->input('color'));
            if (!empty($selectedColors)) {
                $productsQuery->where(function($q) use ($selectedColors) {
                    foreach ($selectedColors as $colorName) {
                        // Query for 'name' property within the 'colors' array in 'specifications'
                        $q->orWhereJsonContains('specifications->colors', ['name' => $colorName]);
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedColors) {
                    foreach ($selectedColors as $colorName) {
                        $q->orWhereJsonContains('specifications->colors', ['name' => $colorName]);
                    }
                });
            }
        }

        // Filter by Size (Multi-select checkbox: name="size[]" -> now in specifications JSON)
        if ($request->has('size') && is_array($request->input('size'))) {
            $selectedSizes = array_filter($request->input('size'));
            if (!empty($selectedSizes)) {
                $productsQuery->where(function($q) use ($selectedSizes) {
                    foreach ($selectedSizes as $size) {
                        $q->orWhereJsonContains('specifications->size', $size); // Query within JSON
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedSizes) {
                    foreach ($selectedSizes as $size) {
                        $q->orWhereJsonContains('specifications->size', $size); // Query within JSON
                    }
                });
            }
        }

        // Filter by Gender (Multi-select checkbox: name="gender[]" -> now in specifications JSON)
        if ($request->has('gender') && is_array($request->input('gender'))) {
            $selectedGenders = array_filter($request->input('gender'));
            if (!empty($selectedGenders)) {
                $productsQuery->where(function($q) use ($selectedGenders) {
                    foreach ($selectedGenders as $gender) {
                        $q->orWhere('specifications->gender', $gender); // Query within JSON
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedGenders) {
                    foreach ($selectedGenders as $gender) {
                        $q->orWhere('specifications->gender', $gender); // Query within JSON
                    }
                });
            }
        }

        // Filter by Material (Multi-select checkbox: name="material[]" -> now in specifications JSON)
        if ($request->has('material') && is_array($request->input('material'))) {
            $selectedMaterials = array_filter($request->input('material'));
            if (!empty($selectedMaterials)) {
                $productsQuery->where(function($q) use ($selectedMaterials) {
                    foreach ($selectedMaterials as $material) {
                        $q->orWhere('specifications->material', $material); // Query within JSON
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedMaterials) {
                    foreach ($selectedMaterials as $material) {
                        $q->orWhere('specifications->material', $material); // Query within JSON
                    }
                });
            }
        }

        // Filter by Description (This was a top-level column, assuming it remains so or is moved to specifications if it's dynamic)
        if ($request->has('description') && is_array($request->input('description'))) {
            $selectedDescriptions = array_filter($request->input('description'));
            if (!empty($selectedDescriptions)) {
                $productsQuery->whereIn('description', $selectedDescriptions);
                // Apply to options query
                $filterOptionsQuery->whereIn('description', $selectedDescriptions);
            }
        }

        // Filter by Rating
        if ($request->filled('rating')) {
            $productsQuery->where('rating', '>=', (float) $request->input('rating'));
            // Apply to options query
            $filterOptionsQuery->where('rating', '>=', (float) $request->input('rating'));
        }

        // Filter by Price Range
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) $request->input('min_price'));
            // Apply to options query
            $filterOptionsQuery->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) $request->input('max_price'));
            // Apply to options query
            $filterOptionsQuery->where('price', '<=', (float) $request->input('max_price'));
        }

        // Filter by Minimum Order Quantity
        if ($request->filled('min_order_quantity')) {
            $productsQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity'));
            // Apply to options query
            $filterOptionsQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity'));
        }

        // Filter by Delivery Option (e.g., free_shipping)
        if ($request->has('delivery_option') && is_array($request->input('delivery_option'))) {
            $selectedDeliveryOptions = array_filter($request->input('delivery_option'));
            if (!empty($selectedDeliveryOptions)) {
                if (in_array('free_shipping', $selectedDeliveryOptions)) {
                    // Assuming 'free_shipping' is a top-level boolean column.
                    // If it's in specifications, it would be ->where('specifications->free_shipping', true);
                    $productsQuery->where('free_shipping', true);
                    // Apply to options query
                    $filterOptionsQuery->where('free_shipping', true);
                }
            }
        }

        // Filter: Trusted Factory (supplier_confirmed)
        if ($request->has('supplier_confirmed') && $request->input('supplier_confirmed') == '1') {
            $productsQuery->where('supplier_confirmed', true);
            // Apply to options query
            $filterOptionsQuery->where('supplier_confirmed', true);
        }

        // Filter: Estimated Delivery Days (estimated_delivery_days)
        if ($request->filled('delivery_date')) {
            try {
                $maxDays = (int) $request->input('delivery_date'); // Assuming delivery_date input now means max_days
                $productsQuery->where('estimated_delivery_days', '<=', $maxDays);
                // Apply to options query
                $filterOptionsQuery->where('estimated_delivery_days', '<=', $maxDays);
            } catch (\Exception $e) {
                Log::warning("Invalid delivery_date (expected integer days): " . $request->input('delivery_date') . " - " . $e->getMessage());
                // For filter options, we can silently ignore if the date is malformed
            }
        }

        // Apply Sorting
        if ($request->filled('sort_by')) {
            switch ($request->input('sort_by')) {
                case 'price_asc':
                    $productsQuery->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $productsQuery->orderBy('price', 'desc');
                    break;
                case 'latest':
                    $productsQuery->latest();
                    break;
                case 'rating_desc':
                    $productsQuery->orderBy('rating', 'desc');
                    break;
                default:
                    $productsQuery->latest();
                    break;
            }
        } else {
            $productsQuery->latest();
        }

        // Get the paginated product results
        $products = $productsQuery->paginate(12)->withQueryString();

        // --- Start of Dynamic Delivery Options Generation ---
        $deliveryOptions = [];
        $deliveryOptions['5'] = ['label_key' => 'delivery_in_days', 'days_param' => 5];
        $deliveryOptions['10'] = ['label_key' => 'delivery_in_days', 'days_param' => 10];
        $deliveryOptions['15'] = ['label_key' => 'delivery_in_days', 'days_param' => 15];
        // --- End of Dynamic Delivery Options Generation ---

        // --- Start of Centralized Color Hex Map Definition (no longer strictly needed here if using swatch images, but kept for reference) ---
        // Define a comprehensive color-to-hex map.
        // You will need to manually update this array whenever you introduce a new color.
        $colorHexMap = [
            'Red'       => '#FF0000', 'Blue'      => '#0000FF', 'Green'     => '#008000', 'Yellow'    => '#FFFF00',
            'Orange'    => '#FFA500', 'Purple'    => '#800080', 'Black'     => '#000000', 'White'     => '#FFFFFF',
            'Gray'      => '#808080', 'Brown'     => '#A52A2A', 'Pink'      => '#FFC0CB', 'Turquoise' => '#40E0D0',
            'Navy'      => '#000080', 'Maroon'    => '#800000', 'Silver'    => '#C0C0C0', 'Gold'      => '#FFD700',
            'Cyan'      => '#00FFFF', 'Magenta'   => '#FF00FF', 'Lime'      => '#00FF00', 'Teal'      => '#008080',
            'Olive'     => '#808000', 'Nike Red'  => '#BB0000', 'Adidas Blue' => '#0050A0',
            'Pink-purpple' => '#DDB7D3', 'Caffe' => '#e9dcd9',
        ];
        // --- End of Centralized Color Hex Map Definition ---


        // Now, derive available filter options from the context-aware $filterOptionsQuery
        // These now pluck from the 'specifications' JSON column and extract 'name' for colors
        $availableColors = $filterOptionsQuery->clone()->pluck('specifications')
                                             ->filter(function ($value) { return !empty($value); })
                                             ->map(function ($specs) {
                                                 $decodedSpecs = is_string($specs) ? json_decode($specs, true) : $specs;
                                                 return collect($decodedSpecs['colors'] ?? [])->pluck('name')->all();
                                             })
                                             ->flatten()->unique()->sort()->values();

        $availableSizes = $filterOptionsQuery->clone()->pluck('specifications')
                                            ->filter(function ($value) { return !empty($value); })
                                            ->map(function ($specs) {
                                                $decodedSpecs = is_string($specs) ? json_decode($specs, true) : $specs;
                                                return $decodedSpecs['size'] ?? [];
                                            })
                                            ->flatten()->unique()->sort()->values();

        $availableGenders = $filterOptionsQuery->clone()->pluck('specifications')
                                                ->filter(function ($value) { return !empty($value); })
                                                ->map(function ($specs) {
                                                    $decodedSpecs = is_string($specs) ? json_decode($specs, true) : $specs;
                                                    return $decodedSpecs['gender'] ?? null;
                                                })
                                                ->filter()->unique()->sort()->values();

        $availableMaterials = $filterOptionsQuery->clone()->pluck('specifications')
                                                ->filter(function ($value) { return !empty($value); })
                                                ->map(function ($specs) {
                                                    $decodedSpecs = is_string($specs) ? json_decode($specs, true) : $specs;
                                                    return $decodedSpecs['material'] ?? null;
                                                })
                                                ->filter()->unique()->sort()->values();

        // Note: For 'description', if it's a long text field, collecting distinct values might lead to many options.
        // Consider if this filter is truly meant for pre-defined "tags" or just for a general text search.
        $availableDescriptions = $filterOptionsQuery->clone()->distinct()->whereNotNull('description')
                                                    ->pluck('description')->sort()->values();

        $availableSubCategories = SubCategory::all(); // This should likely be fetched without filtering by current product query, unless you only want subcategories that have products matching current filters. For a general list of subcategories, `SubCategory::all()` is fine.

        // Prepare data for breadcrumbs
        $currentCategory = null;
        $currentSubCategory = null;
        if ($request->filled('sub_category_id')) {
            $currentSubCategory = SubCategory::with('category')->find($request->input('sub_category_id'));
            if ($currentSubCategory) {
                $currentCategory = $currentSubCategory->category;
            }
        }

        // Pass all necessary data to the view
        return view('categories.product', compact(
            'products',
            'availableColors',
            'availableSizes',
            'availableGenders',
            'availableMaterials',
            'availableSubCategories',
            'availableDescriptions',
            'currentCategory',
            'currentSubCategory',
            'deliveryOptions',
            'colorHexMap' // <-- Make sure to pass this to your view
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

        return view('categories.product_details', compact('product', 'category', 'subCategory', 'productName'));
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
        $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Gray', 'Brown', 'Purple', 'Orange', 'Pink', 'Turquoise', 'Navy', 'Maroon', 'Silver', 'Gold', 'Cyan', 'Magenta', 'Lime', 'Teal', 'Olive', 'Nike Red', 'Adidas Blue'];
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
