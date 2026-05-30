<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'shopee_link' => 'nullable|url|max:255',
            'wa_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'image_2' => 'nullable|image|max:2048',
            'image_3' => 'nullable|image|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name) . '-' . uniqid();
        $product->description = $request->description;
        $product->price = $request->price;
        $product->shopee_link = $request->shopee_link;
        $product->wa_link = $request->wa_link;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image_path = '/storage/' . $path;
        }
        if ($request->hasFile('image_2')) {
            $path = $request->file('image_2')->store('products', 'public');
            $product->image_path_2 = '/storage/' . $path;
        }
        if ($request->hasFile('image_3')) {
            $path = $request->file('image_3')->store('products', 'public');
            $product->image_path_3 = '/storage/' . $path;
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'shopee_link' => 'nullable|url|max:255',
            'wa_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
            'image_2' => 'nullable|image|max:2048',
            'image_3' => 'nullable|image|max:2048',
        ]);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->shopee_link = $request->shopee_link;
        $product->wa_link = $request->wa_link;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image_path = '/storage/' . $path;
        }
        if ($request->hasFile('image_2')) {
            $path = $request->file('image_2')->store('products', 'public');
            $product->image_path_2 = '/storage/' . $path;
        }
        if ($request->hasFile('image_3')) {
            $path = $request->file('image_3')->store('products', 'public');
            $product->image_path_3 = '/storage/' . $path;
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}
