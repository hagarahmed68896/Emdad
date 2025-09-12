<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class BannedUserController extends Controller
{
  public function index(Request $request)
    {
        // ✅ Counts
        $totalUsers = User::count();
        $totalCustomers = User::where('account_type', 'customer')->count();
        $totalSuppliers = User::where('account_type', 'supplier')->count();
        $totalCategories = Category::count();
        $totalProducts = Product::count();

        // ✅ Banned counts
        $totalBannedUsers = UserBlock::count();
        $totalBannedCustomers = UserBlock::whereHas('blocked', function ($q) {
            $q->where('account_type', 'customer');
        })->count();
        $totalBannedSuppliers = UserBlock::whereHas('blocked', function ($q) {
            $q->where('account_type', 'supplier');
        })->count();

        // ✅ Query banned users with relationships
        $query = User::whereIn('id', UserBlock::pluck('blocked_id'))
            ->with(['blocks', 'blockedBy']); 

        // ✅ Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // ✅ Filter (customer/supplier)
        if ($request->has('account_type')) {
            $query->where('account_type', $request->input('account_type'));
        }

        // ✅ Pagination
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return view('admin.banned_users', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalCustomers' => $totalCustomers,
            'totalSuppliers' => $totalSuppliers,
            'totalCategories' => $totalCategories,
            'totalProducts' => $totalProducts,
            'totalBannedUsers' => $totalBannedUsers,
            'totalBannedCustomers' => $totalBannedCustomers,
            'totalBannedSuppliers' => $totalBannedSuppliers,
            'perPage' => $perPage,
            'search' => $request->input('search'),
        ]);
    }

    // ✅ Ban a user
    public function ban($id)
    {
        $adminId = Auth::id(); // the blocker (admin)
        UserBlock::firstOrCreate([
            'blocker_id' => $adminId,
            'blocked_id' => $id,
        ]);

        return back()->with('success', 'تم حظر المستخدم بنجاح.');
    }

    // ✅ Unban a user
    public function unban($id)
    {
        $adminId = Auth::id();
        UserBlock::where('blocker_id', $adminId)
            ->where('blocked_id', $id)
            ->delete();

        return back()->with('success', 'تم إلغاء حظر المستخدم بنجاح.');
    }
}
