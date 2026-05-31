@extends('layouts.public')

@section('title', 'Collection - Bagtopia')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR FILTERS -->
        <aside class="w-full lg:w-1/4 hidden md:block">
            <!-- Search -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-stone-500 focus:ring-1 focus:ring-stone-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Search">
                </div>
            </div>

            <div class="space-y-6">
                <!-- Categories -->
                <div class="border-b border-gray-100 pb-6">
                    <h3 class="text-sm font-medium text-gray-900 flex justify-between items-center cursor-pointer">
                        Categories
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </h3>
                    <div class="mt-4 space-y-3">
                        @foreach(['Tote Bags', 'Backpacks', 'Sling Bags', 'Clutches', 'Accessories'] as $category)
                        <div class="flex items-center">
                            <input id="cat-{{ $loop->index }}" type="checkbox" class="h-4 w-4 border-gray-300 rounded text-stone-900 focus:ring-stone-900">
                            <label for="cat-{{ $loop->index }}" class="ml-3 text-sm text-gray-600">{{ $category }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Materials -->
                <div class="border-b border-gray-100 pb-6">
                    <h3 class="text-sm font-medium text-gray-900 flex justify-between items-center cursor-pointer">
                        Materials
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </h3>
                    <div class="mt-4 space-y-3">
                        @foreach(['Genuine Leather', 'Synthetic Leather', 'Canvas', 'Nylon'] as $material)
                        <div class="flex items-center">
                            <input id="mat-{{ $loop->index }}" type="checkbox" class="h-4 w-4 border-gray-300 rounded text-stone-900 focus:ring-stone-900">
                            <label for="mat-{{ $loop->index }}" class="ml-3 text-sm text-gray-600">{{ $material }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Product Type -->
                <div class="border-b border-gray-100 pb-6">
                    <h3 class="text-sm font-medium text-gray-900 flex justify-between items-center cursor-pointer">
                        Product Type
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center">
                            <input id="type-all" name="product_type" type="radio" class="h-4 w-4 border-gray-300 text-stone-900 focus:ring-stone-900" checked>
                            <label for="type-all" class="ml-3 text-sm text-gray-600">All Products</label>
                        </div>
                        <div class="flex items-center">
                            <input id="type-featured" name="product_type" type="radio" class="h-4 w-4 border-gray-300 text-stone-900 focus:ring-stone-900">
                            <label for="type-featured" class="ml-3 text-sm text-gray-600">Featured Products</label>
                        </div>
                        <div class="flex items-center">
                            <input id="type-bundled" name="product_type" type="radio" class="h-4 w-4 border-gray-300 text-stone-900 focus:ring-stone-900">
                            <label for="type-bundled" class="ml-3 text-sm text-gray-600">Bundled Products</label>
                        </div>
                    </div>
                </div>

                <!-- Availability -->
                <div class="border-b border-gray-100 pb-6">
                    <h3 class="text-sm font-medium text-gray-900 flex justify-between items-center cursor-pointer">
                        Availability
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </h3>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center">
                            <input id="avail-all" name="availability" type="radio" class="h-4 w-4 border-gray-300 text-stone-900 focus:ring-stone-900" checked>
                            <label for="avail-all" class="ml-3 text-sm text-gray-600">All</label>
                        </div>
                        <div class="flex items-center">
                            <input id="avail-instock" name="availability" type="radio" class="h-4 w-4 border-gray-300 text-stone-900 focus:ring-stone-900">
                            <label for="avail-instock" class="ml-3 text-sm text-gray-600">In Stock</label>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT (GRID) -->
        <main class="w-full lg:w-3/4">
            <!-- Top Bar -->
            <div class="flex justify-end mb-6">
                <div class="relative inline-block text-left">
                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-200 shadow-sm px-4 py-2 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500">
                        sort : Recent
                        <svg class="-mr-1 ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="group relative flex flex-col">
                        <!-- Image Container -->
                        <div class="w-full aspect-square bg-[#f0f0f0] rounded-sm overflow-hidden relative">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                            </a>
                            
                            <!-- Badges -->
                            <div class="absolute top-0 right-0 flex flex-col gap-1">
                                @if($loop->index % 3 == 0)
                                <span class="bg-black text-white text-[10px] font-bold px-2 py-1 uppercase">10% MAX</span>
                                @elseif($loop->index % 4 == 0)
                                <span class="bg-black text-white text-[10px] font-bold px-2 py-1 uppercase">34%</span>
                                @endif
                            </div>
                            
                            <!-- Heart Icon -->
                            <button class="absolute bottom-3 right-3 text-white hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </div>
                        
                        <!-- Details -->
                        <div class="mt-4 flex flex-col items-start bg-white z-10 relative">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide hover:text-gray-500 transition-colors line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                            </a>
                            
                            <!-- Prices -->
                            <div class="mt-1 flex flex-col">
                                <span class="text-[11px] text-gray-400 line-through">Rp {{ number_format($product->price + 52900, 0, ',', '.') }}</span>
                                <span class="text-xs font-bold text-gray-900 mt-0.5">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>

                            <!-- Rating (Mock) -->
                            @if($loop->index % 2 != 0)
                            <div class="mt-1.5 flex items-center">
                                <svg class="text-yellow-400 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-[10px] text-gray-500 ml-1">5</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12 border-t border-gray-100 pt-8">
                {{ $products->links() }}
            </div>
            
        </main>
    </div>
</div>
@endsection
