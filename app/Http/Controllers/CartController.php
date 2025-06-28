<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
        public function index()
    {
        $cartItems = []; 
        return view('cart.index', compact('cartItems'));
    }
}
