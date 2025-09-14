<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Document;
use App\Models\Order;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;


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

        // ✅ Avoid division by zero
$customerPercent   = $totalUsers > 0 ? round(($totalCustomers / $totalUsers) * 100, 2) : 0;
$supplierPercent   = $totalUsers > 0 ? round(($totalSuppliers / $totalUsers) * 100, 2) : 0;
$documentsPercent  = $totalUsers > 0 ? round(($totalDocuments / $totalUsers) * 100, 2) : 0;

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
            'documentsPercent' => $documentsPercent,
            'supplierPercent' => $supplierPercent,
            'customerPercent' => $customerPercent,
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
        'password' => ['nullable', 'string', 'min:8'],
        'address' => ['nullable', 'string', 'max:255'],
        'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        'account_type' => ['required', 'string', 'in:supplier,customer,admin'],
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
  'phone_number' => [
        'required',
        'digits:9',
        Rule::unique('users', 'phone_number')->ignore($user->id)],        'address' => ['nullable', 'string', 'max:255'],
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

public function show(User $user)
{
    $invoices = $user->invoices;

    // ✅ الطلبات مع البحث
    $ordersQuery = Order::with('orderItems')
        ->where('user_id', $user->id);

    if (request('tab') === 'orders' && request('search')) {
        $searchTerm = request('search');
        $ordersQuery->where(function ($query) use ($searchTerm) {
            $query->where('order_number', 'like', "%$searchTerm%")
                  ->orWhere('id', $searchTerm);
        });
    }

    $orders = $ordersQuery->paginate(10);

    // ✅ التقييمات مع البحث
    $reviewsQuery = $user->reviews()->with('product');

    if (request('tab') === 'reviews' && request('search')) {
        $searchTerm = request('search');
        $reviewsQuery->where(function ($query) use ($searchTerm) {
            $query->where('id', $searchTerm)
                  ->orWhereHas('product', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%$searchTerm%");
                  });
        });
    }

    $reviews = $reviewsQuery->paginate(10);

    // ✅ الفواتير مع البحث (جديد)
    $billsQuery = Bill::with('order')
        ->where('user_id', $user->id);

    if (request('tab') === 'bills' && request('search')) {
        $searchTerm = request('search');
        $billsQuery->where(function ($query) use ($searchTerm) {
            $query->where('bill_number', 'like', "%$searchTerm%")
                  ->orWhere('id', $searchTerm)
                  ->orWhereHas('order', function ($q) use ($searchTerm) {
                      $q->where('id', $searchTerm);
                  });
        });
    }

    $bills = $billsQuery->paginate(10);

    return view('admin.users.show', compact('user', 'orders', 'invoices', 'reviews', 'bills'));
}

public function editInvoice(Bill $invoice)
{
    return view('admin.bills.edit', [
        'bill' => $invoice
    ]);
}
public function updateInvoice(Request $request, Bill $invoice)
{
    $validated = $request->validate([
        'customer_name' => 'required',
        'order_number'  => 'required',
        'payment_way'   => 'required',
        'total_price'   => 'required|numeric',
        'status'        => 'required',
    ]);

    $user = User::where('full_name', $validated['customer_name'])
                ->where('account_type', 'customer')
                ->first();

    if (!$user) {
        return response()->json(['errors' => ['customer_name' => ['لم يتم العثور على هذا العميل.']]], 422);
    }

    $order = Order::where('order_number', $validated['order_number'])->first();
    if (!$order) {
        return response()->json(['errors' => ['order_number' => ['لم يتم العثور على هذا الطلب.']]], 422);
    }

    $invoice->update([
        'user_id'     => $user->id,
        'order_id'    => $order->id,
        'payment_way' => $validated['payment_way'],
        'total_price' => $validated['total_price'],
        'status'      => $validated['status'],
    ]);

    return response()->json(['message' => 'تم تحديث الفاتورة بنجاح!']);
}

  public function showPdf($id)
{
    $invoice = Bill::with('user', 'order.orderItems')->findOrFail($id);

    $pdf = Pdf::loadView('admin.bill_pdf', compact('invoice'))
        ->setOptions([
            'defaultFont' => 'Amiri',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

    return $pdf->stream('فاتورة-' . $invoice->bill_number . '.pdf');
}




}

