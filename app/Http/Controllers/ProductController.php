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

        // Filter by Color (Multi-select checkbox: name="color[]" -> JSON array in DB)
        if ($request->has('color') && is_array($request->input('color'))) {
            $selectedColors = array_filter($request->input('color'));
            if (!empty($selectedColors)) {
                $productsQuery->where(function($q) use ($selectedColors) {
                    foreach ($selectedColors as $color) {
                        $q->orWhereJsonContains('color', $color);
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedColors) {
                    foreach ($selectedColors as $color) {
                        $q->orWhereJsonContains('color', $color);
                    }
                });
            }
        }

        // Filter by Size (Multi-select checkbox: name="size[]" -> JSON array in DB)
        if ($request->has('size') && is_array($request->input('size'))) {
            $selectedSizes = array_filter($request->input('size'));
            if (!empty($selectedSizes)) {
                $productsQuery->where(function($q) use ($selectedSizes) {
                    foreach ($selectedSizes as $size) {
                        $q->orWhereJsonContains('size', $size);
                    }
                });
                // Apply to options query
                $filterOptionsQuery->where(function($q) use ($selectedSizes) {
                    foreach ($selectedSizes as $size) {
                        $q->orWhereJsonContains('size', $size);
                    }
                });
            }
        }

        // Filter by Gender (Multi-select checkbox: name="gender[]")
        if ($request->has('gender') && is_array($request->input('gender'))) {
            $selectedGenders = array_filter($request->input('gender'));
            if (!empty($selectedGenders)) {
                $productsQuery->whereIn('gender', $selectedGenders);
                // Apply to options query
                $filterOptionsQuery->whereIn('gender', $selectedGenders);
            }
        }

        // Filter by Material (Multi-select checkbox: name="material[]")
        if ($request->has('material') && is_array($request->input('material'))) {
            $selectedMaterials = array_filter($request->input('material'));
            if (!empty($selectedMaterials)) {
                $productsQuery->whereIn('material', $selectedMaterials);
                // Apply to options query
                $filterOptionsQuery->whereIn('material', $selectedMaterials);
            }
        }

        // Filter by Description (Multi-select checkbox: name="description[]")
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

        // Filter: Delivery Date (estimated_delivery_date)
        if ($request->filled('delivery_date')) {
            try {
                $deliveryDate = Carbon::parse($request->input('delivery_date'));
                $productsQuery->where('estimated_delivery_date', '<=', $deliveryDate->toDateString());
                // Apply to options query
                $filterOptionsQuery->where('estimated_delivery_date', '<=', $deliveryDate->toDateString());
            } catch (\Exception $e) {
                Log::warning("Invalid delivery_date format: " . $request->input('delivery_date') . " - " . $e->getMessage());
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

        // Option 1: Delivery in a few days (e.g., 3-7 days)
        $date1 = Carbon::today()->addDays(rand(3, 7));
        $deliveryOptions[$date1->toDateString()] = [
            'label_key' => 'delivery_by_date',
            'date_param' => $date1->isoFormat('MMMM D'), // e.g., "July 9"
        ];

        // Option 2: Delivery within approximately 2 weeks (e.g., 8-14 days)
        $date2 = Carbon::today()->addDays(rand(8, 14));
        $deliveryOptions[$date2->toDateString()] = [
            'label_key' => 'delivery_by_date',
            'date_param' => $date2->isoFormat('MMMM D'), // e.g., "July 15"
        ];

        // Option 3: Delivery within approximately 3 weeks (e.g., 15-21 days)
        $date3 = Carbon::today()->addDays(rand(15, 21));
        $deliveryOptions[$date3->toDateString()] = [
            'label_key' => 'delivery_by_date',
            'date_param' => $date3->isoFormat('MMMM D'), // e.g., "July 22"
        ];
        // --- End of Dynamic Delivery Options Generation ---

        // --- Start of Centralized Color Hex Map Definition ---
        // Define a comprehensive color-to-hex map.
        // You will need to manually update this array whenever you introduce a new color.
        $colorHexMap = [
            'Red'       => '#FF0000',
            'Blue'      => '#0000FF',
            'Green'     => '#008000',
            'Yellow'    => '#FFFF00',
            'Orange'    => '#FFA500',
            'Purple'    => '#800080',
            'Black'     => '#000000',
            'White'     => '#FFFFFF',
            'Gray'      => '#808080',
            'Brown'     => '#A52A2A',
            'Pink'      => '#FFC0CB',
            'Turquoise' => '#40E0D0',
            'Navy'      => '#000080',
            'Maroon'    => '#800000',
            'Silver'    => '#C0C0C0',
            'Gold'      => '#FFD700',
            'Cyan'      => '#00FFFF',
            'Magenta'   => '#FF00FF',
            'Lime'      => '#00FF00',
            'Teal'      => '#008080',
            'Olive'     => '#808000',
            'Nike Red'  => '#BB0000',
            'Adidas Blue' => '#0050A0',
            // You can add many more. If a color is not found here, it will default to '#ccc' in the Blade view.
        ];
        // --- End of Centralized Color Hex Map Definition ---


        // Now, derive available filter options from the context-aware $filterOptionsQuery
        $availableColors = $filterOptionsQuery->clone()->pluck('color')
                                             ->filter(function ($value) { return !empty($value); })
                                             ->flatten()->unique()->sort()->values();
        $availableSizes = $filterOptionsQuery->clone()->pluck('size')
                                             ->filter(function ($value) { return !empty($value); })
                                             ->flatten()->unique()->sort()->values();

        $availableGenders = $filterOptionsQuery->clone()->distinct()->pluck('gender')
                                               ->filter()->sort()->values();

        $availableMaterials = $filterOptionsQuery->clone()->distinct()->pluck('material')
                                                           ->filter()->sort()->values();

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

        return view('product.show', compact('product', 'category', 'subCategory', 'productName'));
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
        $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Gray', 'Brown', 'Purple', 'Orange', 'Pink', 'Turquoise', 'Navy', 'Maroon', 'Silver', 'Gold', 'Cyan', 'Magenta', 'Lime', 'Teal', 'Olive', 'Nike Red', 'Adidas Blue'];
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size','Over Size'];
        $genders = ['Male', 'Female', 'Unisex', 'Kids'];
        $materials = ['Cotton', 'Polyester', 'Wool', 'Leather', 'Denim', 'Silk', 'Nylon'];

        return view('products.create', compact('subCategories', 'colors', 'sizes', 'genders', 'materials'));
    }

    /**
     * Store a newly created product in storage.
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For single main image upload
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For multiple gallery images
            'sub_category_id' => 'required|exists:sub_categories,id',
            'is_offer' => 'boolean',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'offer_expires_at' => 'nullable|date|after_or_equal:today',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_confirmed' => 'boolean', // Assuming this is a boolean checkbox
            'min_order_quantity' => 'required|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_featured' => 'boolean', // Assuming this is a boolean checkbox
            'color' => 'nullable|array', // Allow 'color' to be an array of strings
            'color.*' => 'string|max:255', // Validate each item in the color array
            'size' => 'nullable|array',   // Allow 'size' to be an array of strings
            'size.*' => 'string|max:255', // Validate each item in the size array
            'gender' => 'nullable|string|max:255', // Updated to string, as per Blade's multiple checkboxes (can be handled by whereIn on backend if array of values is sent)
            'material' => 'nullable|string|max:255',
            'estimated_delivery_date' => 'nullable|date|after_or_equal:today', // Added validation for the new field
        ]);

        // Handle main image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Handle multiple images upload (if 'images' is an array of files)
        $additionalImagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $additionalImagesPaths[] = $file->store('products/gallery', 'public');
            }
        }

        // Create the product
        $product = Product::create([
            'name' => $validatedData['name'],
            'slug' => $validatedData['slug'],
            'description' => $validatedData['description'] ?? null,
            'price' => $validatedData['price'],
            'image' => $imagePath, // Save the main image path
            'images' => $additionalImagesPaths, // Save additional image paths as JSON array in database
            'sub_category_id' => $validatedData['sub_category_id'],
            'is_offer' => $validatedData['is_offer'] ?? false, // Default to false if not present
            'discount_percent' => $validatedData['discount_percent'] ?? null,
            'offer_expires_at' => $validatedData['offer_expires_at'] ?? null,
            'supplier_name' => $validatedData['supplier_name'] ?? null,
            'supplier_confirmed' => $validatedData['supplier_confirmed'] ?? false, // Default to false
            'min_order_quantity' => $validatedData['min_order_quantity'],
            'rating' => $validatedData['rating'] ?? null,
            'is_featured' => $validatedData['is_featured'] ?? false, // Default to false
            'color' => $validatedData['color'] ?? null, // Save as JSON array in database
            'size' => $validatedData['size'] ?? null,   // Save as JSON array in database
            'gender' => $validatedData['gender'] ?? null,
            'material' => $validatedData['material'] ?? null,
            'estimated_delivery_date' => $validatedData['estimated_delivery_date'] ?? null, // Save the new field
        ]);

        // Notify admin(s) about the new product
        try {
            // Example: Notify a specific user (e.g., the admin with ID 1)
            $adminUser = User::find(1); // Assuming admin user has ID 1. Adjust as needed.
            if ($adminUser) {
                // Ensure NewProductFromSupplier notification exists and is properly configured.
                // If supplier_image is not directly on product, you might need a default or derive from supplier model.
                $supplierImage = $product->supplier_image_url ?? asset('images/default_supplier.png'); // Placeholder for supplier image
                $adminUser->notify(new NewProductFromSupplier($product, $product->supplier_name ?? 'Unknown Supplier', $supplierImage));
            }

        } catch (\Exception $e) {
            Log::error("Failed to send new product notification: " . $e->getMessage());
            // You might want to flash an error message to the user here too, but proceed with redirect.
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    // You might have other methods like edit, update, destroy, etc., here.
}