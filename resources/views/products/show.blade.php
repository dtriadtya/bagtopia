@extends('layouts.public')

@section('title', $product->name . ' - Bagtopia')

@section('content')
@php
    $images = collect([
        $product->image_path,
        $product->image_path_2,
        $product->image_path_3,
    ])->filter()->values();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
    <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">

        {{-- ===== PRODUCT GALLERY ===== --}}
        <div>
            {{-- Main Image --}}
            <div class="product-gallery rounded-2xl overflow-hidden bg-stone-100 relative" style="height: 480px;">
                @foreach($images as $i => $img)
                <img
                    src="{{ $img }}"
                    alt="{{ $product->name }}"
                    class="gallery-img absolute inset-0 w-full h-full object-cover object-center transition-opacity duration-300"
                    style="{{ $i === 0 ? 'opacity:1; z-index:1;' : 'opacity:0; z-index:0;' }}"
                    data-index="{{ $i }}"
                >
                @endforeach

                {{-- Arrows --}}
                @if($images->count() > 1)
                <button onclick="galleryPrev()" class="gallery-arrow absolute left-3 z-10 w-9 h-9 rounded-full flex items-center justify-center" style="top: 50%; transform: translateY(-50%); background:rgba(0,0,0,0.35); backdrop-filter:blur(4px);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="galleryNext()" class="gallery-arrow absolute right-3 z-10 w-9 h-9 rounded-full flex items-center justify-center" style="top: 50%; transform: translateY(-50%); background:rgba(0,0,0,0.35); backdrop-filter:blur(4px);">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($images->count() > 1)
            <div class="flex gap-3 mt-3">
                @foreach($images as $i => $img)
                <button
                    onclick="galleryGoto({{ $i }})"
                    class="thumb-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                    style="{{ $i === 0 ? 'border-color:#1c1917;' : 'border-color:transparent;' }}"
                    data-thumb="{{ $i }}"
                >
                    <img src="{{ $img }}" alt="Foto {{ $i + 1 }}" class="w-full h-full object-cover object-center">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ===== PRODUCT DETAILS ===== --}}
        <div class="mt-10 px-4 sm:px-0 lg:mt-0">
            <h1 class="text-3xl font-light tracking-tight text-stone-900">{{ $product->name }}</h1>
            <div class="mt-3">
                <h2 class="sr-only">Product information</h2>
                <p class="text-2xl text-stone-900 tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>

            <div class="mt-6">
                <h3 class="sr-only">Description</h3>
                <div class="text-base text-stone-600 space-y-4">
                    <p>{{ $product->description }}</p>
                </div>
            </div>

            <div class="mt-10 flex flex-col sm:flex-row gap-4">
                <a href="{{ $product->shopee_link }}" target="_blank"
                    class="flex-1 bg-[#ee4d2d] border border-transparent rounded-lg py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-[#d74226] transition-colors shadow-sm">
                    Beli di Shopee
                </a>
                <a href="{{ $product->wa_link }}" target="_blank"
                    class="flex-1 bg-[#25D366] border border-transparent rounded-lg py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-[#20bd5a] transition-colors shadow-sm">
                    Beli via WhatsApp
                </a>
            </div>

            <div class="mt-8 border-t border-stone-200 pt-8">
                <h3 class="text-sm font-medium text-stone-900">Garansi & Pengiriman</h3>
                <div class="mt-4 prose prose-sm text-stone-500">
                    <ul role="list" class="list-disc pl-5">
                        <li>Pengiriman ke seluruh Indonesia.</li>
                        <li>Garansi tukar barang jika cacat pabrik dalam 3 hari.</li>
                        <li>Bahan premium syntethic leather yang awet dan mudah dibersihkan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const totalImgs = {{ $images->count() }};
    let currentIdx = 0;

    function galleryGoto(idx) {
        // Hide all
        document.querySelectorAll('.gallery-img').forEach(el => {
            el.style.opacity = '0';
            el.style.zIndex = '0';
        });
        // Show target
        const target = document.querySelector(`.gallery-img[data-index="${idx}"]`);
        if (target) { target.style.opacity = '1'; target.style.zIndex = '1'; }

        // Update thumbs
        document.querySelectorAll('.thumb-btn').forEach(el => {
            el.style.borderColor = parseInt(el.dataset.thumb) === idx ? '#1c1917' : 'transparent';
        });

        currentIdx = idx;
    }

    function galleryNext() {
        galleryGoto((currentIdx + 1) % totalImgs);
    }

    function galleryPrev() {
        galleryGoto((currentIdx - 1 + totalImgs) % totalImgs);
    }
</script>
@endpush