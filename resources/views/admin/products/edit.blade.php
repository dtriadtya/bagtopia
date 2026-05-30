@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Edit Product</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800">Cancel</a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Product Name</label>
                <input type="text" name="name" value="{{ $product->name }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price (Rp)</label>
                <input type="number" name="price" value="{{ $product->price }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $product->description }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Produk 1 <span class="text-slate-400">(Utama)</span></label>
                @if($product->image_path)
                    <img src="{{ $product->image_path }}" class="h-20 mb-2 rounded-md border border-slate-200 object-cover">
                @endif
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Produk 2 <span class="text-slate-400">(Opsional)</span></label>
                @if($product->image_path_2)
                    <img src="{{ $product->image_path_2 }}" class="h-20 mb-2 rounded-md border border-slate-200 object-cover">
                @endif
                <input type="file" name="image_2" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Produk 3 <span class="text-slate-400">(Opsional)</span></label>
                @if($product->image_path_3)
                    <img src="{{ $product->image_path_3 }}" class="h-20 mb-2 rounded-md border border-slate-200 object-cover">
                @endif
                <input type="file" name="image_3" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Shopee Link</label>
                    <input type="url" name="shopee_link" value="{{ $product->shopee_link }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp Link</label>
                    <input type="url" name="wa_link" value="{{ $product->wa_link }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-2.5 font-semibold text-white shadow-sm hover:bg-blue-500">Update Product</button>
            </div>
        </div>
    </form>
</div>
@endsection
