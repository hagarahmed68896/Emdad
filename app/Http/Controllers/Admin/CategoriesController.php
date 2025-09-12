<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $totalCategories = Category::count();
        $totalSubCategories = SubCategory::count();
        $availableProducts = Product::where('is_available', true)->count();
        $unavailableProducts = Product::where('is_available', false)->count();
   $totalProducts = $availableProducts + $unavailableProducts;

    // ✅ حساب النسب
    $availablePercentage = $totalProducts > 0 ? round(($availableProducts / $totalProducts) * 100, 2) : 0;
    $unavailablePercentage = $totalProducts > 0 ? round(($unavailableProducts / $totalProducts) * 100, 2) : 0;
    $categoriesPercentage = $totalCategories > 0 ? round(($totalCategories / $totalCategories) * 100, 2) : 0;

        $categoriesQuery = Category::query();
        $subCategoriesQuery = SubCategory::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $categoriesQuery->where('name', 'like', "%{$search}%");
            $subCategoriesQuery->where('name', 'like', "%{$search}%");
        }

        if ($request->status === 'category') {
            $categories = $categoriesQuery->get();
            $subCategories = collect();
        } elseif ($request->status === 'sub_category') {
            $categories = collect();
            $subCategories = $subCategoriesQuery->get();
        } else {
            $categories = $categoriesQuery->get();
            $subCategories = $subCategoriesQuery->get();
        }

        $items = collect();
    $statusFilter = $request->status ?? '';
    $sortFilter = $request->sort ?? '';
foreach ($categories as $category) {
    $items->push((object)[
        'type' => 'category',
        'id' => $category->id,
        'name' => $category->name,
        'description' => $category->description ?? '-',
        'parent' => '-',
        'products_count' => $category->products()->count(),
        'iconUrl' => $category->iconUrl,
        'created_at' => $category->created_at,
    ]);
}

foreach ($subCategories as $subCategory) {
    $items->push((object)[
        'type' => 'sub_category',
        'id' => $subCategory->id,
        'name' => $subCategory->name,
        'description' => '-',
        'parent' => $subCategory->category ? $subCategory->category->name : '-',
        'products_count' => $subCategory->products()->count(),
        'iconUrl' => $subCategory->iconUrl,
        'created_at' => $subCategory->created_at,
        'statusFilter' => $statusFilter,
        'sortFilter' => $sortFilter,
    ]);
}

        if ($request->sort === 'name_asc') {
            $items = $items->sortBy('name');
        } elseif ($request->sort === 'name_desc') {
            $items = $items->sortByDesc('name');
        } elseif ($request->sort === 'oldest') {
            $items = $items->sortBy('created_at');
        } else {
            $items = $items->sortByDesc('created_at');
        }

        // ✅ إضافة Pagination يدويًا
        $perPage = 10;
        $page = $request->get('page', 1);
        $itemsPaginated = $items->forPage($page, $perPage);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsPaginated,
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.categories.index', [
            'items' => $paginator,
            'totalCategories' => $totalCategories,
            'totalSubCategories' => $totalSubCategories,
            'availableProducts' => $availableProducts,
               'availablePercentage' => $availablePercentage,
        'unavailablePercentage' => $unavailablePercentage,
        'categoriesPercentage' => $categoriesPercentage,
            'unavailableProducts' => $unavailableProducts,
            'search' => $request->search,
            'selectedStatus' => $request->status ?? 'all',
            'sort' => $request->sort ?? 'latest',
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:category,sub_category',
            'name' => 'required|string|max:255',
            'iconUrl' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $slug = Str::slug($request->name);
        if (DB::table('categories')->where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $iconPath = null;
        if ($request->hasFile('iconUrl')) {
            $iconPath = $request->file('iconUrl')->store('icons', 'public');
        }

        if ($request->type === 'category') {
            Category::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'iconUrl' => $iconPath,
            ]);
        } elseif ($request->type === 'sub_category') {
            SubCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'iconUrl' => $iconPath,
                'category_id' => $request->category_id,
            ]);
        }

        return response()->json(['message' => 'تمت الإضافة بنجاح.']);
    }

public function destroy($id)
{
    $category = Category::findOrFail($id);

    // إذا كانت الفئة فرعية وبها منتجات → لا تحذفها
    if ($category->type === 'sub_category' && $category->products()->count() > 0) {
        return back()->with('error', 'لا يمكن حذف الفئة الفرعية لأنها تحتوي على منتجات.');
    }

    $category->delete();

    return back()->with('success', 'تم حذف الفئة بنجاح.');
}


public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);
    $types = $request->input('types', []);

    foreach ($ids as $index => $id) {
        $type = $types[$index] ?? null;

        if ($type === 'category') {
            $category = Category::find($id);
            if ($category) {
                $category->delete();
            }
        } elseif ($type === 'sub_category') {
            $subCategory = SubCategory::find($id);
            if ($subCategory && $subCategory->products()->count() > 0) {
                return back()->with('error', 'لا يمكن حذف الفئة الفرعية لأنها تحتوي على منتجات.');
            }
            if ($subCategory) {
                $subCategory->delete();
            }
        }
    }

    return back()->with('success', 'تم الحذف بنجاح.');
}


// ✅ تحرير الفئة العامة
  public function edit($id)
{
    $category = Category::findOrFail($id);
    $category->type = 'category';  // ✅ تحديد النوع يدويًا
    $categories = Category::all();
    return view('admin.categories.edit', compact('category', 'categories'));
}

public function editSubCategory($id)
{
    $category = SubCategory::findOrFail($id);
    $category->type = 'sub_category'; // ✅ تحديد النوع يدويًا
    $categories = Category::all();
    return view('admin.categories.edit', compact('category', 'categories'));
}



    // ✅ تحديث الفئة العامة
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'iconUrl' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if ($request->hasFile('iconUrl')) {
            $validated['iconUrl'] = $request->file('iconUrl')->store('categories', 'public');
        }

        $category->update($validated);

        return response()->json(['success' => true, 'message' => 'تم التحديث بنجاح.']);
    }



    // ✅ تحديث الفئة الفرعية
    public function updateSubCategory(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'iconUrl' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        if ($request->hasFile('iconUrl')) {
            $validated['iconUrl'] = $request->file('iconUrl')->store('sub_categories', 'public');
        }

        $subCategory->update($validated);

        return response()->json(['success' => true, 'message' => 'تم التحديث بنجاح.']);
    }




    public function exportCsv(Request $request)
    {
        $categories = Category::query();
        $subCategories = SubCategory::query();

        if ($request->filled('search')) {
            $categories->where('name', 'like', "%{$request->search}%");
            $subCategories->where('name', 'like', "%{$request->search}%");
        }

        $categories = $categories->get();
        $subCategories = $subCategories->with('category')->get();

        $callback = function () use ($categories, $subCategories) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID', 'النوع', 'الاسم', 'الوصف', '   تابعة لـ', 'عدد المنتجات', 'تاريخ الإنشاء'
            ]);

            foreach ($categories as $cat) {
                fputcsv($file, [
                    $cat->id,
                    'Category',
                    $cat->name,
                    $cat->description ?? '-',
                    '-',
                    $cat->products()->count(),
                    $cat->created_at,
                ]);
            }

            foreach ($subCategories as $sub) {
                fputcsv($file, [
                    $sub->id,
                    'Sub Category',
                    $sub->name,
                    '-',
                    $sub->category ? $sub->category->name : '-',
                    $sub->products()->count(),
                    $sub->created_at,
                ]);
            }

            fclose($file);
        };

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=categories_export_" . now()->format('Ymd_His') . ".csv",
        ];

        return new StreamedResponse($callback, 200, $headers);
    }
}
