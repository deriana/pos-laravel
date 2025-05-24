<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $cart = $request->input('cart');

        session(['checkout_cart' => $cart]);

        return redirect()->route('sales.create');
    }
}
