<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSupplierController extends Controller
{
   // AdminSupplierController.php
public function index(Request $request)
{
    $search = $request->get('search');
    $statusFilter = $request->get('status');
    $sortFilter = $request->get('sort');
    $perPage = $request->get('per_page', 10);

$query = User::with(['business' => function ($q) {
    $q->withCount('products');
}])
->where('account_type', 'supplier');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhereHas('business', function ($q2) use ($search) {
                  $q2->where('company_name', 'like', "%$search%");
              });
        });
    }

    if ($statusFilter) {
        $query->where('status', $statusFilter);
    }

    if ($sortFilter) {
        switch ($sortFilter) {
            case 'full_name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'full_name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            case 'latest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
        }
    }

    $suppliers = $query->paginate($perPage);
  $totalUsers = User::count();
        $totalCustomers = User::where('account_type', 'customer')->count();
        $totalSuppliers = User::where('account_type', 'supplier')->count(); // Adjust 'supplier' if your type is different
        $totalDocuments = Document::count();

        // ✅ Avoid division by zero
$customerPercent   = $totalUsers > 0 ? round(($totalCustomers / $totalUsers) * 100, 2) : 0;
$supplierPercent   = $totalUsers > 0 ? round(($totalSuppliers / $totalUsers) * 100, 2) : 0;
$documentsPercent  = $totalUsers > 0 ? round(($totalDocuments / $totalUsers) * 100, 2) : 0;

return view('admin.supplier', [
    'suppliers' => $suppliers,
    'search' => $search,
    'statusFilter' => $statusFilter,
    'sortFilter' => $sortFilter,
    'perPage' => $perPage,
    'totalUsers' => $totalUsers,
    'totalCustomers' => $totalCustomers,
    'totalSuppliers' => $totalSuppliers,
    'totalDocuments' => $totalDocuments,
    'documentsPercent' => $documentsPercent,
    'supplierPercent' => $supplierPercent,
    'customerPercent' => $customerPercent,
]);

}

  // ✅ صفحة إضافة مستخدم
    public function create()
    {
        return view('admin.suppliers.create');
    }

    // ✅ تخزين مستخدم جديد
public function store(Request $request)
{
    $validated = $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone_number' => ['nullable', 'string', 'max:9', 'unique:users,phone_number'],
        'address' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        'password' => ['nullable', 'string', 'min:8'],
        'account_type' => ['required', 'string', 'in:supplier,customer,admin'],
        'company_name' => ['nullable', 'string', 'max:255'],
    ]);

    $password = $request->password ?? 'Password123';

    $user = User::create([
        'full_name' => $validated['full_name'],
        'email' => $validated['email'],
        'phone_number' => $validated['phone_number'],
        'address' => $validated['address'],
        'status' => $validated['status'],
        'password' => Hash::make($password),
        'account_type' => $validated['account_type'],
    ]);

    if ($validated['account_type'] === 'supplier') {
        $user->business()->create([
            'company_name' => $validated['company_name'],
        ]);
    }

    if ($request->wantsJson()) {
        return response()->json(['success' => 'تم إضافة المورد بنجاح!']);
    }

    return redirect()->route('admin.suppliers.index')
        ->with('success', 'تم إضافة المورد بنجاح!');
}

public function edit(User $supplier)
{
    // تأكد أن المورد فعلاً account_type = supplier
    if ($supplier->account_type !== 'supplier') {
        abort(404);
    }

    return view('admin.suppliers.edit', [
        'supplier' => $supplier->load('business'),
    ]);
}

public function update(Request $request, User $supplier)
{
    if ($supplier->account_type !== 'supplier') {
        abort(404);
    }

    $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($supplier->id)],
  'phone_number' => [
        'required',
        'digits:9',
        Rule::unique('users', 'phone_number')->ignore($supplier->id)],
                'address' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        'company_name' => ['nullable', 'string', 'max:255'],
                    'password' => 'nullable|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',

    ]);

    $supplier->update([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'address' => $request->address,
        'status' => $request->status,
        'password' => Hash::make($request->password),
    ]);

    if ($supplier->business) {
        $supplier->business->update([
            'company_name' => $request->company_name,
        ]);
    } else {
        $supplier->business()->create([
            'company_name' => $request->company_name,
        ]);
    }

    if ($request->expectsJson()) {
        return response()->json(['success' => 'تم التعديل بنجاح']);
    }

    return redirect()->route('admin.suppliers.index')->with('success', 'تم التعديل بنجاح');
}



    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'تم حذف المستخدم بنجاح!');
    }

public function exportCsv(Request $request)
{
    $query = User::with('business'); // ✅ جلب العلاقة

    // Filters
    $filterType = $request->input('account_type');
    $filterStatus = $request->input('status');
    $searchTerm = $request->input('search');
    $sortTerm = $request->input('sort');

    if ($filterType && $filterType !== 'all') {
        $query->where('account_type', $filterType);
    }
    if ($filterStatus && $filterStatus !== 'all') {
        $query->where('status', $filterStatus);
    }
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('full_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('email', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('business', function ($q2) use ($searchTerm) {
                  $q2->where('company_name', 'like', '%' . $searchTerm . '%');
              });
        });
    }

    if ($sortTerm) {
        switch ($sortTerm) {
            case 'full_name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'full_name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $usersToExport = $query->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=users_export_" . now()->format('Ymd_His') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use ($usersToExport) {
        $file = fopen('php://output', 'w');

        fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($file, [
            'ID',
            'الاسم الكامل',
            'اسم المورد',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'العنوان',
            'الحالة',
            'تاريخ الإنشاء',
        ]);

        foreach ($usersToExport as $user) {
            fputcsv($file, [
                $user->id,
                $user->full_name,
                $user->business->company_name ?? 'N/A',
                $user->email,
                $user->phone_number ?? 'N/A',
                $user->address ?? 'N/A',
                match($user->status) {
                    'active' => 'نشط',
                    'inactive' => 'غير نشط',
                    'banned' => 'محظور',
                    default => $user->status,
                },
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);
    };

    return new \Symfony\Component\HttpFoundation\StreamedResponse($callback, 200, $headers);
}


    // Add this method to your UserController
public function bulkDelete(Request $request)
{
    $userIds = $request->input('user_ids');

    if (!empty($userIds)) {
        User::whereIn('id', $userIds)->delete();
        return redirect()->back()->with('success', 'Selected users deleted successfully.');
    }

    return redirect()->back()->with('error', 'No users selected for deletion.');
}


public function toggleBan(User $supplier)
{
    if ($supplier->status === 'banned') {
        // Unban user
        $supplier->status = 'active'; // أو status سابق إذا عندك تخزين له
    } else {
        // Ban user
        $supplier->status = 'banned';
    }
    $supplier->save();

    return redirect()->back()->with('success', 'تم تحديث حالة المستخدم.');
}


}
