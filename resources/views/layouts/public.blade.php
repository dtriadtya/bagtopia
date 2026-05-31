<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Katalog Tas Minimalis')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600|playfair-display:400,500,600&display=swap"
        rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-serif-brand {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>

<body class="bg-[#faf9f6] text-stone-800 antialiased selection:bg-stone-200">
    <!-- Top Black Bar -->
    <div class="bg-black text-white text-xs text-center py-2 tracking-widest">
        FREE SHIPPING NATIONWIDE
    </div>

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white border-b border-stone-200">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center w-1/3 md:w-1/4">
                    <a href="{{ route('home') }}"
                        class="text-xl md:text-2xl font-serif-brand tracking-widest uppercase text-stone-900">
                        Bagtopia
                    </a>
                </div>

                <!-- Center Navigation -->
                <nav class="hidden md:flex space-x-8 w-2/4 justify-center">
                    <a href="{{ route('home') }}"
                        class="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-stone-800 hover:text-stone-500 transition-colors">Home</a>
                    <div class="relative group">
                        <button
                            class="flex items-center space-x-1 text-[10px] md:text-xs font-semibold tracking-widest uppercase text-stone-800 hover:text-stone-500 transition-colors">
                            <span>Pre-Order</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="relative group">
                        <button
                            class="flex items-center space-x-1 text-[10px] md:text-xs font-semibold tracking-widest uppercase text-stone-800 hover:text-stone-500 transition-colors">
                            <span>Sale</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                    <a href="{{ route('collection') }}"
                        class="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-stone-800 hover:text-stone-500 transition-colors">Collection</a>
                </nav>

                <!-- Right Icons -->
                <div class="flex items-center justify-end space-x-3 sm:space-x-4 w-1/3 md:w-1/4">
                    <div class="hidden sm:flex items-center space-x-1">
                        <img src="https://flagcdn.com/w20/id.png" alt="ID" class="w-4 h-3 object-cover">
                        <span class="text-[10px] font-semibold text-stone-800">IDR</span>
                    </div>
                    <button class="text-stone-800 hover:text-stone-500 transition-colors">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button class="text-stone-800 hover:text-stone-500 transition-colors hidden sm:block">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>

                    <!-- Hamburger (Mobile Only) -->
                    <button id="mobile-menu-button"
                        class="md:hidden text-stone-800 hover:text-stone-500 transition-colors ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Full Screen Overlay) -->
        <div id="mobile-menu" class="fixed inset-0 bg-[#faf9f6] z-[100] hidden flex-col justify-center items-center">
            <button id="close-menu-button" class="absolute top-6 right-6 text-stone-800 hover:text-stone-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <div class="flex flex-col space-y-10 text-center w-full px-6">
                <a href="{{ route('home') }}"
                    class="text-3xl font-serif-brand tracking-widest uppercase text-stone-800 hover:text-stone-500">Home</a>
                <a href="#"
                    class="text-3xl font-serif-brand tracking-widest uppercase text-stone-800 hover:text-stone-500">Pre-Order</a>
                <a href="#"
                    class="text-3xl font-serif-brand tracking-widest uppercase text-stone-800 hover:text-stone-500">Sale</a>
                <a href="{{ route('collection') }}"
                    class="text-3xl font-serif-brand tracking-widest uppercase text-stone-800 hover:text-stone-500">Collection</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-stone-100 border-t border-stone-200 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-stone-500 text-sm">
            <p>&copy; {{ date('Y') }} Bagtopia. All rights reserved.</p>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-button');
            const closeBtn = document.getElementById('close-menu-button');
            const menu = document.getElementById('mobile-menu');

            if (btn && closeBtn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.remove('hidden');
                    menu.classList.add('flex');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                });

                closeBtn.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    menu.classList.remove('flex');
                    document.body.style.overflow = '';
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>