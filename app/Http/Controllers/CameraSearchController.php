<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CameraSearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['image_base64'=>'required|string']);
        $imageData = $request->image_base64;
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        $imageContent = base64_decode($imageData);

        // Dummy AI search: match product containing "Camera" in name
        $products = Product::where('name', 'like', '%Camera%')->get();

        $results = $products->map(fn($item) => [
            'type'=>'product',
            'data'=>[
                'id'=>$item->id,
                'name'=>$item->name,
                'description'=>$item->description,
                'price'=>$item->price,
                'image'=>$item->image_url ?? 'https://via.placeholder.com/150'
            ]
        ]);

        return response()->json(['results'=>$results]);
    }
}
