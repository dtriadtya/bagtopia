@extends('layouts.public')

@section('content')

@php
/**
 * Hero Banner — baca otomatis dari: public/images/banners/hero/
 * Tambah/kurangi foto langsung di folder, slider menyesuaikan sendiri.
 */
$heroFiles = collect(glob(public_path('images/banners/hero/*.{jpg,jpeg,png,webp}'), GLOB_BRACE))
    ->sortBy(fn($f) => basename($f))
    ->values();

$heroImages = $heroFiles->map(fn($f) => asset('images/banners/hero/' . basename($f)))->all();

// Fallback kalau folder masih kosong
if (empty($heroImages)) {
    $heroImages = [null];
}

// Caption per slide (opsional, berulang kalau slide lebih banyak dari caption)
$heroCaptions = [
    ['title' => 'Elegance Collection',  'sub' => 'Discover Your Style',    'btn' => 'Shop Now'],
    ['title' => 'Minimalist Series',    'sub' => 'Simplicity at its best', 'btn' => 'View Details'],
    ['title' => 'Premium Collection',   'sub' => 'Crafted for You',        'btn' => 'Explore'],
    ['title' => 'New Arrival',          'sub' => 'Fresh from the Studio',  'btn' => 'See More'],
];

/**
 * Product / Bag Images — pakai Unsplash (placeholder produk)
 */
$bagImages = [
    'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1594223274512-ad4803739b7c?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1598532163915-539d04dc9f7d?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1600857062241-98e547eae858?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1575037614876-c3858d44f64b?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1566150908141-8b32115162a0?q=80&w=500&h=500&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=500&h=500&auto=format&fit=crop',
];

/**
 * Bottom Banner — baca otomatis dari: public/images/banners/bottom/
 */
$bottomFiles = collect(glob(public_path('images/banners/bottom/*.{jpg,jpeg,png,webp}'), GLOB_BRACE))
    ->sortBy(fn($f) => basename($f))
    ->values();

$bottomImages = $bottomFiles->map(fn($f) => asset('images/banners/bottom/' . basename($f)))->all();

$bottomCaptions = [
    'New Collection',
    'Premium Quality',
    'Stay Elegant',
    'Limited Edition',
];

if (empty($bottomImages)) {
    $bottomImages = [null];
}
@endphp

<!-- Full Width Hero Slider -->
<div class="swiper hero-slider w-full h-[60vh] md:h-[85vh]">
    <div class="swiper-wrapper">
        @foreach($heroImages as $i => $heroImg)
        @php $caption = $heroCaptions[$i % count($heroCaptions)]; @endphp
        <div class="swiper-slide relative w-full h-full bg-stone-800 overflow-hidden">
            @if($heroImg)
                <img src="{{ $heroImg }}" alt="Banner {{ $i + 1 }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-stone-700"></div>
            @endif
            {{-- Gradient overlay: gelap di bawah, transparan di atas --}}
            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.72) 0%, rgba(0,0,0,0.35) 45%, rgba(0,0,0,0.08) 100%);"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center px-4 max-w-3xl">
                    <h2 class="text-3xl md:text-5xl font-serif-brand text-white mb-4 tracking-wide drop-shadow-lg">{{ $caption['title'] }}</h2>
                    <p class="text-white/85 text-sm md:text-base font-light tracking-widest uppercase mb-8">{{ $caption['sub'] }}</p>
                    <a href="#katalog" class="inline-block border border-white text-white text-xs md:text-sm tracking-widest uppercase font-medium px-8 py-3 hover:bg-white hover:text-black transition-all duration-300">
                        {{ $caption['btn'] }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Navigation Buttons -->
    <div class="swiper-button-next !text-white after:!text-xl md:after:!text-2xl"></div>
    <div class="swiper-button-prev !text-white after:!text-xl md:after:!text-2xl"></div>
    <!-- Pagination -->
    <div class="swiper-pagination !bottom-6"></div>
</div>

<!-- Catalog Section -->
<div id="katalog" class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    
    <!-- NEW LAUNCHING SECTION -->
    <div class="mb-16">
        <div class="mb-10 text-center">
            <h2 class="text-xl md:text-2xl font-serif-brand text-[#1e3a5f] uppercase font-bold tracking-wide">NEW LAUNCHING</h2>
        </div>

        @if($newLaunching->isEmpty())
            <p class="text-center text-sm text-gray-400">Belum ada produk.</p>
        @else
        <div class="swiper product-slider-launching !pb-12 px-2">
            <div class="swiper-wrapper">
            @foreach($newLaunching as $product)
                <div class="swiper-slide h-auto">
                    <div class="bg-white h-full rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col relative group">
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative w-full aspect-square">
                            @if($product->image_path)
                                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-in-out">
                            @else
                                <div class="w-full h-full bg-stone-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 left-2">
                                <span class="bg-[#1e3a5f]/80 text-white text-[10px] font-bold px-2 py-1 tracking-wider uppercase">LAUNCHING</span>
                            </div>
                        </a>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 hover:text-[#c2a278] transition-colors">{{ $product->name }}</h3>
                                </a>
                                <div class="flex flex-col mt-2">
                                    <span class="text-sm font-bold text-[#5586b5]">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="mt-4 text-center border-t border-gray-100 pt-3 flex justify-center gap-3">
                                @if($product->shopee_link)
                                    <a href="{{ $product->shopee_link }}" target="_blank" class="text-xs font-bold text-[#EE4D2D] hover:text-[#c23a1a] uppercase tracking-wider transition-colors">Shopee</a>
                                @endif
                                @if($product->wa_link)
                                    <a href="{{ $product->wa_link }}" target="_blank" class="text-xs font-bold text-[#25D366] hover:text-[#1a9e4a] uppercase tracking-wider transition-colors">WA</a>
                                @endif
                                @if(!$product->shopee_link && !$product->wa_link)
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-bold text-[#5586b5] hover:text-[#1e3a5f] uppercase tracking-wider transition-colors">Detail</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            <div class="swiper-pagination launching-pagination"></div>
        </div>
        @endif
    </div>

    <!-- NEW ARRIVAL SECTION -->
    <div class="mb-12">
        <div class="mb-10 text-center">
            <h2 class="text-xl md:text-2xl font-serif-brand text-[#1e3a5f] uppercase font-bold tracking-wide">NEW ARRIVAL</h2>
        </div>

        @if($newArrival->isEmpty())
            <p class="text-center text-sm text-gray-400">Belum ada produk.</p>
        @else
        <div class="swiper product-slider-arrival !pb-12 px-2">
            <div class="swiper-wrapper">
            @foreach($newArrival as $product)
                <div class="swiper-slide h-auto">
                    <div class="bg-white h-full rounded-md border border-gray-100 shadow-sm overflow-hidden flex flex-col relative group">
                        <a href="{{ route('products.show', $product->slug) }}" class="block relative w-full aspect-square">
                            @if($product->image_path)
                                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-in-out">
                            @else
                                <div class="w-full h-full bg-stone-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 left-2">
                                <span class="bg-black/60 text-white text-[10px] font-bold px-2 py-1 tracking-wider uppercase">NEW</span>
                            </div>
                        </a>
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 hover:text-[#c2a278] transition-colors">{{ $product->name }}</h3>
                                </a>
                                <div class="flex flex-col mt-2">
                                    <span class="text-sm font-bold text-[#5586b5]">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="mt-4 text-center border-t border-gray-100 pt-3 flex justify-center gap-3">
                                @if($product->shopee_link)
                                    <a href="{{ $product->shopee_link }}" target="_blank" class="text-xs font-bold text-[#EE4D2D] hover:text-[#c23a1a] uppercase tracking-wider transition-colors">Shopee</a>
                                @endif
                                @if($product->wa_link)
                                    <a href="{{ $product->wa_link }}" target="_blank" class="text-xs font-bold text-[#25D366] hover:text-[#1a9e4a] uppercase tracking-wider transition-colors">WA</a>
                                @endif
                                @if(!$product->shopee_link && !$product->wa_link)
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-bold text-[#5586b5] hover:text-[#1e3a5f] uppercase tracking-wider transition-colors">Detail</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            <div class="swiper-pagination arrival-pagination"></div>
        </div>
        @endif
    </div>
</div>

<!-- Bottom Slider Section -->
<div class="w-full mt-12 mb-16 px-4 sm:px-6 lg:px-8 max-w-[1400px] mx-auto">
    <div class="swiper bottom-slider w-full h-[40vh] md:h-[60vh] rounded-2xl overflow-hidden">
        <div class="swiper-wrapper">
            @foreach($bottomImages as $j => $bottomImg)
            @php $btCaption = $bottomCaptions[$j % count($bottomCaptions)]; @endphp
            <div class="swiper-slide relative w-full h-full bg-stone-200">
                @if($bottomImg)
                    <img src="{{ $bottomImg }}" alt="Bottom Banner {{ $j + 1 }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 bg-stone-400"></div>
                @endif
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                    <h2 class="text-white text-2xl md:text-4xl font-serif-brand tracking-widest uppercase">{{ $btCaption }}</h2>
                </div>
            </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination bottom-pagination !bottom-4"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hero Slider
        const heroSwiper = new Swiper('.hero-slider', {
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.hero-slider .swiper-pagination',
                clickable: true,
            },
        });

        // Bottom Slider
        const bottomSwiper = new Swiper('.bottom-slider', {
            loop: true,
            slidesPerView: 1,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.bottom-pagination',
                clickable: true,
            },
        });
        
        // Product Sliders
        const sliderConfig = {
            slidesPerView: 1.5,
            spaceBetween: 16,
            pagination: { clickable: true, dynamicBullets: true },
            breakpoints: {
                640: { slidesPerView: 2.5, spaceBetween: 20 },
                1024: { slidesPerView: 4, spaceBetween: 24 },
            }
        };

        if (document.querySelector('.product-slider-launching')) {
            new Swiper('.product-slider-launching', {
                ...sliderConfig,
                pagination: { el: '.launching-pagination', clickable: true, dynamicBullets: true },
            });
        }

        if (document.querySelector('.product-slider-arrival')) {
            new Swiper('.product-slider-arrival', {
                ...sliderConfig,
                pagination: { el: '.arrival-pagination', clickable: true, dynamicBullets: true },
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
</script>
<style>
    /* Custom Swiper Pagination Color */
    .swiper-pagination-bullet {
        background: white !important;
        opacity: 0.5;
    }
    .swiper-pagination-bullet-active {
        opacity: 1;
    }
</style>
@endpush
