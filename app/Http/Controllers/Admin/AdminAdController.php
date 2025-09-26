<?php

// app/Http/Controllers/Admin/AdController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdminAdController extends Controller
{
  public function index()
{
    $ads = Ad::latest()->paginate(10); // show 10 ads per page
    return view('admin.ads.index', compact('ads'));
}


    public function approve($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->update(['status' => 'approved']);
        return back()->with('success', 'Ad approved successfully.');
    }

    public function reject($id)
    {
        $ad = Ad::findOrFail($id);
        $ad->update(['status' => 'rejected']);
        return back()->with('error', 'Ad rejected.');
    }
}

