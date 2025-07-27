<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;

class BannedUserController extends Controller
{
       public function index(Request $request)
    {
        $query = User::query();
        $query->where('status', 'banned'); 

    

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting logic
        // if ($request->filled('sort')) {
        //     switch ($request->sort) {
        //         case 'full_name_asc':
        //             $query->orderBy('full_name', 'asc');
        //             break;
        //         case 'full_name_desc':
        //             $query->orderBy('full_name', 'desc');
        //             break;
        //         case 'latest':
        //             $query->orderBy('created_at', 'desc');
        //             break;
        //         case 'oldest':
        //             $query->orderBy('created_at', 'asc');
        //             break;
        //         // No default case needed if you want no specific order when 'all' or empty
        //     }
        // } else {
        //     // Default sorting if no sort is specified, e.g., by latest creation date
        //     $query->orderBy('created_at', 'desc');
        // }

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);
   $totalBannedUsers = User::where('status', 'banned')->count();
$totalBannedSuppliers = User::where('status', 'banned')->where('account_type', 'supplier')->count();
$totalBannedCustomers = User::where('status', 'banned')->where('account_type', 'customer')->count();

        return view('admin.banned_users', [
            'users' => $users,
            'search' => $request->search,
            'perPage' => $perPage,
            'totalBannedUsers' => $totalBannedUsers,
            'totalBannedSuppliers' => $totalBannedSuppliers,
            'totalBannedCustomers' => $totalBannedCustomers,
        ]);
    }
}
