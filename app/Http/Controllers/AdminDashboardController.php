<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCustomers = User::where('account_type', 'customer')->count();
        $totalSuppliers = User::where('account_type', 'supplier')->count();
        $totalDocuments = 123; // مثال ثابت

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCustomers',
            'totalSuppliers',
            'totalDocuments'
        ));
    }
}
