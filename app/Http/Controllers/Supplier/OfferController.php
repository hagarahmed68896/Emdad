<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth; 


class OfferController extends Controller
{
    // Show all offers
    public function index()
    {
        $offers = Offer::with('product')->latest()->paginate(10);
        return view('offers.index', compact('offers'));
    }

    // Show create form
    public function create()
    {
        $products = Product::all();
        return view('supplier.offers.create', compact('products'));
    }

    // Store new offer
public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'product_id'       => 'required|exists:products,id',
        'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'offer_start'      => 'required|date',
        'offer_end'        => 'required|date|after_or_equal:offer_start',
        'discount_percent' => 'required|numeric|min:0|max:100',
        'description'      => 'nullable|string',
        'product_status'   => 'nullable|string'
    ]);

    // Create offer
    $offer = new Offer();
    $offer->name             = $validated['name'];
    $offer->product_id       = $validated['product_id'];
    $offer->offer_start      = $validated['offer_start'];
    $offer->offer_end        = $validated['offer_end'];
    $offer->discount_percent = $validated['discount_percent'];
    $offer->description      = $validated['description'] ?? null;
    // $offer->user_id          = Auth::id();

    if ($request->hasFile('image')) {
        $offer->image = $request->file('image')->store('offers', 'public');
    }

    $offer->save();

    // Update product status if provided
    if ($offer->product && isset($validated['product_status'])) {
        $offer->product->update([
            'product_status' => $validated['product_status']
        ]);
    }

    // Notify customers who favorited this product
    $favoritedUsers = $offer->product->favorites()->with('user')->get()->pluck('user');

    foreach ($favoritedUsers as $customer) {
        $settings = $customer->notification_settings ?? [];

        if (
            isset($settings['receive_in_app'] ) &&  $settings['receive_in_app'] &&
            isset($settings['offers_discounts']) && $settings['offers_discounts']
        ) {
            $customer->notify(new \App\Notifications\NewOfferNotification($offer));
        }
    }

    return response()->json(['success' => 'تم اضافة العرض بنجاح']);
}



    // Show edit form
    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        $products = Product::all();
        return view('supplier.offers.edit', compact('offer', 'products'));
    }

    // Update offer
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'product_id'       => 'required|exists:products,id',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'offer_start'      => 'required|date',
            'offer_end'        => 'required|date|after_or_equal:offer_start',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'description'      => 'nullable|string',
            'product_status'   => 'nullable|string'
        ]);
        $offer = Offer::findOrFail($id);


        // **Add the authenticated user's ID**
        // $offer->user_id = Auth::id(); // or auth()->id();


        $offer->name             = $validated['name'];
        $offer->product_id       = $validated['product_id'];
        $offer->offer_start      = $validated['offer_start'];
        $offer->offer_end        = $validated['offer_end'];
        $offer->discount_percent = $validated['discount_percent'];
        $offer->description      = $validated['description'] ?? null;

        if ($request->hasFile('image')) {
            $offer->image = $request->file('image')->store('offers', 'public');
        }

        if ($offer->product) {
        $offer->product->update([
            'product_status' => $validated['product_status']
        ]);
    
    }
        $offer->save();

        return response()->json(['success' => 'تم تعديل العرض بنجاح']);
    }

    // Delete offer
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return redirect()->route('offers.index')->with('success', 'تم حذف العرض بنجاح');
    }
}
