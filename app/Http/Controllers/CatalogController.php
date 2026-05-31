<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $newLaunching = Product::oldest()->limit(6)->get();
        $newArrival   = Product::latest()->limit(6)->get();

        return view('welcome', compact('newLaunching', 'newArrival'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }

    public function collection()
    {
        $products = Product::latest()->paginate(24);
        return view('products.collection', compact('products'));
    }
}
