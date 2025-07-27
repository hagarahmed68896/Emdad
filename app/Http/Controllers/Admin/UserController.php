<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Symfony\Component\HttpFoundation\StreamedResponse;


class UserController extends Controller
{
    // ✅ عرض المستخدمين مع الفلاتر
 
    public function index(Request $request)
    {
        $query = User::query();
        $query->where('account_type', 'customer'); // Keep this if this view is specifically for customers

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting logic
        if ($request->filled('sort')) {
            switch ($request->sort) {
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
                // No default case needed if you want no specific order when 'all' or empty
            }
        } else {
            // Default sorting if no sort is specified, e.g., by latest creation date
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);
   // --- Define the variables for total_numbers.blade.php ---
        $totalUsers = User::count();
        $totalCustomers = User::where('account_type', 'customer')->count();
        $totalSuppliers = User::where('account_type', 'supplier')->count(); // Adjust 'supplier' if your type is different
        $totalDocuments = Document::count();
        // --- End variable definitions ---

        return view('admin.customer', [
            'users' => $users,
            'statusFilter' => $request->status,
            'search' => $request->search,
            'perPage' => $perPage,
            'sortFilter' => $request->sort, // Pass the sort filter back to the view
                       // --- Pass the new variables to the view ---
            'totalUsers' => $totalUsers,
            'totalCustomers' => $totalCustomers,
            'totalSuppliers' => $totalSuppliers,
            'totalDocuments' => $totalDocuments,
            // --- End passing new variables ---
        ]);
    }

    // ✅ صفحة إضافة مستخدم
    public function create()
    {
        return view('admin.users.create');
    }

    // ✅ تخزين مستخدم جديد
  public function store(Request $request)
{
    $validated = $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone_number' => 'required|digits:9|unique:users,phone_number',
        'address' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        'account_type' => ['required', 'string', 'in:supplier,customer,admin'],
    ]);


    User::create($validated);

    return response()->json(['success' => 'تم إضافة المستخدم بنجاح!']);
}


    // ✅ صفحة تعديل مستخدم
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // ✅ تحديث بيانات مستخدم
 public function update(Request $request, User $user)
{
    $request->validate([
        'full_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'required|digits:9|unique:users,phone_number',
        'address' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
            'password' => 'nullable|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/',
    ]);

    $user->update([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'address' => $request->address,
        'status' => $request->status,
        'password' => $request->password ? Hash::make($request->password) : $user->password,
    ]);

    // ✅ يرجع JSON إذا الطلب Ajax
    if ($request->wantsJson()) {
        return response()->json(['success' => 'تم تحديث المستخدم بنجاح!']);
    }

    return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح!');
}


    // ✅ حذف مستخدم
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح!');
    }

 public function exportCsv(Request $request)
    {
        $query = User::query();

        // Apply filters, similar to your index method
        $filterType = $request->input('account_type');
        $filterStatus = $request->input('status');
        $searchTerm = $request->input('search');
        $sortTerm = $request->input('sort'); // Get the sort parameter

        if ($filterType && $filterType !== 'all') {
            $query->where('account_type', $filterType);
        }
        if ($filterStatus && $filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply sorting logic to the export as well
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
            $query->orderBy('created_at', 'desc'); // Default sort for export
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

            // Add BOM for UTF-8 compatibility in Excel (important for Arabic characters)
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // Add headers
            fputcsv($file, [
                'ID',
                'الاسم الكامل',
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

        return new StreamedResponse($callback, 200, $headers);
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


public function toggleBan(User $user)
{
    if ($user->status === 'banned') {
        // Unban user
        $user->status = 'active'; // أو status سابق إذا عندك تخزين له
    } else {
        // Ban user
        $user->status = 'banned';
    }
    $user->save();

    return redirect()->back()->with('success', 'تم تحديث حالة المستخدم.');
}


}

