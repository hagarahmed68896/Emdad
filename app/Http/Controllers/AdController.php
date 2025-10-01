<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::where('supplier_id', Auth::id())->get();
        return view('supplier.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('supplier.ads.create');
    }

  public function store(Request $request)
{
    $data = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'image'       => 'nullable|image',
        'amount'      => 'required|numeric',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date|after:start_date',
    ]);

    if ($request->hasFile('image')) {
    $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
    $request->file('image')->move(public_path('storage/ads'), $filename);
    $data['image'] = 'ads/' . $filename;
}


    $data['supplier_id'] = Auth::id();
    $data['status'] = 'pending';

    Ad::create($data);

    // Reload the create view with success message
    return view('supplier.ads.create', [
        'success' => __('messages.ad_created')
    ]);
}


    // Show form to edit ad
    public function edit(Ad $ad)
    {
        if ($ad->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('supplier.ads.edit', compact('ad'));
    }

    // Update ad in DB
    public function update(Request $request, Ad $ad)
    {
        if ($ad->supplier_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|max:2048',
        ]);

    if ($request->hasFile('image')) {
    $filename = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
    $request->file('image')->move(public_path('storage/ads'), $filename);
    $validated['image'] = 'ads/' . $filename;
}


        $ad->update($validated);

   return view('supplier.ads.edit', [
    'ad' => $ad,
    'success' => __('messages.ad_updated')
]);

    }

    // Delete ad
  public function destroy(Ad $ad)
{
    if ($ad->supplier_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $ad->delete();

    return response()->json([
        'success' => __('messages.ad_deleted'),
        'ad_id' => $ad->id,
    ]);
}

}
