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
    <!-- Floating Support Chat Widget -->
    <div class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
        <!-- Chat Panel Card -->
        <div id="supportChatPanel"
            class="hidden mb-4 w-80 bg-white rounded-2xl shadow-2xl border border-stone-200 overflow-hidden transform scale-95 opacity-0 origin-bottom-right transition-all duration-300 ease-out">
            <!-- Header -->
            <div class="bg-stone-900 text-white px-5 py-4 flex items-center justify-between">
                <div>
                    <h4 class="font-semibold text-sm tracking-wider uppercase">Dukungan Chat</h4>
                    <p class="text-[11px] text-stone-400 mt-0.5">Hubungi kami melalui channel berikut</p>
                </div>
                <button onclick="toggleSupportChat()" class="text-stone-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Body / Options -->
            <div class="p-5 space-y-3">
                <!-- WhatsApp Option -->
                <a href="{{ env('CONTACT_WHATSAPP', 'https://wa.me/628558537773') }}" target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 hover:bg-[#25D366]/10 hover:border-[#25D366]/40 transition-all duration-200 group">
                    <div class="p-2 bg-[#25D366] text-white rounded-lg group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.713-1.458L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.488 2.028 14.027 1 11.482 1c-5.41 0-9.813 4.359-9.816 9.754-.002 1.838.497 3.633 1.447 5.185L2.122 21.8l6.096-1.597c.05-.001.077-.002.1-.002-.03-.001-.019-.001-.019-.001zM17.15 14.7c-.287-.144-1.7-.84-1.967-.936-.268-.097-.464-.144-.66.145-.196.287-.756.955-.928 1.147-.171.191-.343.215-.63.072-.287-.144-1.21-.446-2.305-1.424-.852-.76-1.428-1.7-1.595-1.987-.168-.287-.018-.442.125-.584.129-.127.287-.335.43-.502.144-.167.191-.287.287-.478.096-.191.048-.359-.024-.502-.072-.143-.66-1.597-.904-2.186-.239-.575-.48-.496-.66-.505-.17-.008-.364-.01-.557-.01-.194 0-.51.072-.777.368-.268.297-1.023 1.004-1.023 2.45s1.05 2.834 1.196 3.025c.146.191 2.067 3.156 5.006 4.428.699.303 1.246.484 1.671.62.704.223 1.345.191 1.851.116.564-.084 1.7-.694 1.939-1.363.239-.668.239-1.24.168-1.363-.072-.12-.268-.191-.555-.335z" />
                        </svg>
                    </div>
                    <div class="text-left flex-1">
                        <p class="text-xs font-semibold text-stone-900">WhatsApp Chat</p>
                        <p class="text-[10px] text-stone-500">Tanya produk via WhatsApp</p>
                    </div>
                </a>

                <!-- Shopee Option -->
                <a href="{{ env('CONTACT_SHOPEE', 'https://shopee.co.id/be.bagtopia') }}" target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 hover:bg-[#EE4D2D]/10 hover:border-[#EE4D2D]/40 transition-all duration-200 group">
                    <div class="p-2 bg-[#EE4D2D] text-white rounded-lg group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3H9c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z" />
                        </svg>
                    </div>
                    <div class="text-left flex-1">
                        <p class="text-xs font-semibold text-stone-900">Shopee Store</p>
                        <p class="text-[10px] text-stone-500">Kunjungi toko kami di Shopee</p>
                    </div>
                </a>

                <!-- TikTok Option -->
                <a href="{{ env('CONTACT_TIKTOK', 'https://www.tiktok.com/@indrianifilantrof') }}" target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 w-full px-4 py-3 rounded-xl border border-stone-200 bg-stone-50 hover:bg-stone-900/10 hover:border-stone-900/40 transition-all duration-200 group">
                    <div class="p-2 bg-stone-900 text-white rounded-lg group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.06-2.89-.53-4.09-1.37-.76-.53-1.43-1.22-1.92-2 .01 2.87.01 5.75 0 8.62-.05 1.58-.56 3.19-1.57 4.41-1.37 1.71-3.64 2.58-5.78 2.37-2-.16-3.89-1.29-4.99-2.99-1.24-1.87-1.43-4.44-.5-6.4 1.04-2.26 3.44-3.83 5.95-3.69v4.1c-1.34-.1-2.73.54-3.41 1.68-.69 1.1-.55 2.67.33 3.61.91.99 2.5 1.21 3.63.53.86-.5 1.36-1.45 1.4-2.45.02-4.14.01-8.28.01-12.42z" />
                        </svg>
                    </div>
                    <div class="text-left flex-1">
                        <p class="text-xs font-semibold text-stone-900">TikTok Shop</p>
                        <p class="text-[10px] text-stone-500">Temukan kami di TikTok</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Floating Action Button -->
        <button onclick="toggleSupportChat()" id="supportChatBtn"
            class="flex items-center justify-center size-14 bg-stone-900 hover:bg-stone-800 text-white rounded-full shadow-2xl transition-all duration-300 hover:scale-110 active:scale-95 relative group">
            <span class="absolute inset-0 rounded-full bg-stone-900 opacity-20 group-hover:animate-ping"></span>

            <svg id="chatIconOpen" class="w-6 h-6 transition-all duration-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                </path>
            </svg>

            <svg id="chatIconClose" class="w-6 h-6 hidden transition-all duration-300" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Toggle Script -->
    <script>
        function toggleSupportChat() {
            const panel = document.getElementById('supportChatPanel');
            const openIcon = document.getElementById('chatIconOpen');
            const closeIcon = document.getElementById('chatIconClose');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('scale-95', 'opacity-0');
                    panel.classList.add('scale-100', 'opacity-100');
                }, 10);
                openIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                panel.classList.remove('scale-100', 'opacity-100');
                panel.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    panel.classList.add('hidden');
                }, 300);
                openIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>

</html>