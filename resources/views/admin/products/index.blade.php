@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">Manage Products</h2>
    <a href="{{ route('admin.products.create') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Add Product</a>
</div>

<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <table class="w-full text-left text-sm text-slate-600">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
            <tr>
                <th class="px-6 py-4 font-medium">Product</th>
                <th class="px-6 py-4 font-medium">Price</th>
                <th class="px-6 py-4 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @foreach($products as $product)
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($product->image_path)
                            <img src="{{ $product->image_path }}" class="h-10 w-10 rounded-md object-cover bg-slate-100">
                        @else
                            <div class="h-10 w-10 rounded-md bg-slate-100"></div>
                        @endif
                        <span class="font-medium text-slate-800">{{ $product->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline-block ml-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:underline" onclick="return confirm('Delete this product?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($products->isEmpty())
            <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400">No products found.</td></tr>
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection
