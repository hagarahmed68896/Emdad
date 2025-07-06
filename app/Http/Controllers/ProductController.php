<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\NewProductFromSupplier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App; // Import App facade for locale checks

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
        // 1. Start with the base query for the products to be displayed.
        // Eager load subCategory and its parent category to prevent N+1 queries.
        $productsQuery = Product::with('subCategory.category');

        // 2. Apply Filters Conditionally to $productsQuery

        // Filter by Search (Product Name/Description) - This should apply to both main product list AND filter options
        if ($request->filled('search')) {
            $search = $request->input('search');
            $productsQuery->where(function($q) use ($search) {
                // Search in product name or description
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by Sub Category (e.g., if linking from a subcategory page or a filter)
        // THIS IS CRUCIAL FOR PRESERVING THE SUBCATEGORY CONTEXT - Applies to both main list AND filter options
        if ($request->filled('sub_category_id')) {
            $productsQuery->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Filter by Color (Multi-select checkbox: name="color[]")
        if ($request->has('color') && is_array($request->input('color'))) {
            $selectedColors = array_filter($request->input('color')); // Filter out empty strings if any
            if (!empty($selectedColors)) {
                $productsQuery->where(function($q) use ($selectedColors) {
                    foreach ($selectedColors as $color) {
                        $q->orWhereJsonContains('color', $color); // Use orWhereJsonContains for multiple colors
                    }
                });
            }
        }

        // Filter by Size (Multi-select checkbox: name="size[]")
        if ($request->has('size') && is_array($request->input('size'))) {
            $selectedSizes = array_filter($request->input('size'));
            if (!empty($selectedSizes)) {
                $productsQuery->where(function($q) use ($selectedSizes) {
                    foreach ($selectedSizes as $size) {
                        $q->orWhereJsonContains('size', $size); // Use orWhereJsonContains for multiple sizes
                    }
                });
            }
        }

        // Filter by Gender (Single select radio: name="gender")
        if ($request->filled('gender')) {
            $productsQuery->where('gender', $request->input('gender'));
        }

        // Filter by Material (Single select radio: name="material")
        if ($request->filled('material')) {
            $productsQuery->where('material', $request->input('material'));
        }

        // Filter by Description (Multi-select checkbox: name="description[]")
        // This filter affects the products displayed, just like others.
        if ($request->has('description') && is_array($request->input('description'))) {
            $selectedDescriptions = array_filter($request->input('description'));
            if (!empty($selectedDescriptions)) {
                $productsQuery->whereIn('description', $selectedDescriptions);
            }
        }

        // Filter by Rating (e.g., '4.5')
        if ($request->filled('rating')) {
            $productsQuery->where('rating', '>=', (float) $request->input('rating'));
        }

        // Filter by Price Range
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) $request->input('max_price'));
        }

        // Filter by Minimum Order Quantity
        if ($request->filled('min_order_quantity')) {
            $productsQuery->where('min_order_quantity', '>=', (int) $request->input('min_order_quantity'));
        }

        // NEW: Filter by Delivery Option (e.g., free_shipping)
        if ($request->has('delivery_option') && is_array($request->input('delivery_option'))) {
            $selectedDeliveryOptions = array_filter($request->input('delivery_option'));
            if (!empty($selectedDeliveryOptions)) {
                if (in_array('free_shipping', $selectedDeliveryOptions)) {
                    $productsQuery->where('free_shipping', true);
                }
            }
        }

        // 3. Apply Sorting
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

        // 4. Get the paginated product results
        $products = $productsQuery->paginate(12)->withQueryString();

        // 5. Prepare data for dynamic filter options in the sidebar
        // IMPORTANT: These filters should now be based on the *context* of the current page,
        // specifically the sub_category_id if one is selected, and potentially the search term.

        // Start with a new query for filter options, but apply the core filters first.
        $filterOptionsQuery = Product::query();

        // Apply global search filter to options as well
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filterOptionsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Apply sub_category_id filter to options as well
        if ($request->filled('sub_category_id')) {
            $filterOptionsQuery->where('sub_category_id', $request->input('sub_category_id'));
        }

        // Now, derive available filter options from this context-aware query
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

        // THIS IS THE CRUCIAL CHANGE: availableDescriptions now uses $filterOptionsQuery
        $availableDescriptions = $filterOptionsQuery->clone()->distinct()->whereNotNull('description')
                                       ->pluck('description')->sort()->values();

        // Subcategories are usually always all listed, or filtered by a main category in a different context.
        // This is generally okay as you might want to show all subcategories for navigation,
        // even if only one is currently selected.
        $availableSubCategories = SubCategory::all();

        // 6. Prepare data for breadcrumbs (to indicate the current category/subcategory)
        $currentCategory = null;
        $currentSubCategory = null;
        if ($request->filled('sub_category_id')) {
            $currentSubCategory = SubCategory::with('category')->find($request->input('sub_category_id'));
            if ($currentSubCategory) {
                $currentCategory = $currentSubCategory->category;
            }
        }

        // 7. Pass all necessary data to the view
        return view('categories.product', compact(
            'products',
            'availableColors',
            'availableSizes',
            'availableGenders',
            'availableMaterials',
            'availableSubCategories',
            'availableDescriptions',
            'currentCategory',
            'currentSubCategory'
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
        $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Gray', 'Brown', 'Purple', 'Orange'];
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
            'gender' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
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