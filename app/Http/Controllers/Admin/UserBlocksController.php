<?php

// app/Http/Controllers/Admin/UserBlocksController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


class UserBlocksController extends Controller
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

    // ✅ Percentages (avoid divide by zero)
    $bannedUsersPercentage = $totalUsers > 0 ? round(($totalBannedUsers / $totalUsers) * 100, 2) : 0;
    $bannedCustomersPercentage = $totalCustomers > 0 ? round(($totalBannedCustomers / $totalCustomers) * 100, 2) : 0;
    $bannedSuppliersPercentage = $totalSuppliers > 0 ? round(($totalBannedSuppliers / $totalSuppliers) * 100, 2) : 0;

    // ✅ Query banned users
    $query = User::whereIn('id', UserBlock::pluck('blocked_id'))
        ->with(['blocks', 'blockedBy']); 

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%");
        });
    }

    if ($request->has('account_type')) {
        $query->where('account_type', $request->input('account_type'));
    }

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
        'bannedUsersPercentage' => $bannedUsersPercentage,
        'bannedCustomersPercentage' => $bannedCustomersPercentage,
        'bannedSuppliersPercentage' => $bannedSuppliersPercentage,
        'perPage' => $perPage,
        'search' => $request->input('search'),
    ]);
}


    // ✅ Ban a user
public function ban($id)
{
    $adminId = Auth::id(); // the blocker (admin)

    // create ban record if not exists
    UserBlock::firstOrCreate([
        'blocker_id' => $adminId,
        'blocked_id' => $id,
    ]);

    // update user status to banned
    User::where('id', $id)->update(['status' => 'banned']);

    return back()->with('success', 'تم حظر المستخدم بنجاح.');
}

public function unban($id)
{
    // remove all block records for this user
    UserBlock::where('blocked_id', $id)->delete();

    // update user status to active
    User::where('id', $id)->update(['status' => 'active']);

    return back()->with('success', 'تم إلغاء حظر المستخدم بنجاح.');
}

}

