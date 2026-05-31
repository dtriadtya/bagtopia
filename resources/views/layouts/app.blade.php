<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bagtopia - Monitoring Toko</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800 min-h-screen" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div id="sidebarOverlay" class="fixed inset-0 bg-slate-950/40 z-30 hidden lg:hidden"></div>
        <div class="min-h-screen flex">
            <aside id="appSidebar" class="fixed inset-y-0 left-0 z-40 w-56 bg-slate-950 text-slate-200 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:shrink-0">
                <div class="px-4 py-5 border-b border-slate-800">
                    <h1 class="text-sm font-semibold tracking-wide">Bagtopia Monitor</h1>
                </div>
                <nav class="p-3 text-sm space-y-1">
                    <p class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Menu</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13h8V3H3zm10 8h8v-6h-8zM3 21h8v-6H3zm10-10h8V3h-8z"/>
                            </svg>
                        </span>
                        Dashboard
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('orders.index') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6M5 4h14l-1 16H6z"/>
                            </svg>
                        </span>
                        Orders
                    </a>
                    <a href="{{ route('fraud.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('fraud.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.73 3h16.9a2 2 0 001.73-3l-8.47-14.14a2 2 0 00-3.46 0z"/>
                            </svg>
                        </span>
                        Fraud Detection
                    </a>
                    <a href="{{ route('orders.create') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('orders.create') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        Import Data
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h10v18H7zM9 8h6M9 12h6M9 16h4"/>
                            </svg>
                        </span>
                        Reports
                    </a>
                    
                    <p class="px-3 py-2 mt-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Content (CMS)</p>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('admin.products.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                        <span>
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </span>
                        Products
                    </a>
                </nav>
                <div class="mt-auto p-3 border-t border-slate-800 text-sm text-slate-200">
                    @if (auth()->user()?->role === 'super_admin')
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8a4 4 0 100 8 4 4 0 000-8zm8.94 4a7.93 7.93 0 00-.14-1.5l2.11-1.65-2-3.46-2.49 1a8.06 8.06 0 00-2.6-1.5l-.38-2.65h-4l-.38 2.65a8.06 8.06 0 00-2.6 1.5l-2.49-1-2 3.46 2.11 1.65a8.3 8.3 0 000 3l-2.11 1.65 2 3.46 2.49-1a8.06 8.06 0 002.6 1.5l.38 2.65h4l.38-2.65a8.06 8.06 0 002.6-1.5l2.49 1 2-3.46-2.11-1.65c.09-.49.14-.99.14-1.5z"/>
                            </svg>
                            Settings
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 rounded-md px-3 py-2 hover:bg-slate-800 text-left">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 17l5-5-5-5M21 12H9m4 9H5a2 2 0 01-2-2V5a2 2 0 012-2h8"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <main class="flex-1 min-w-0 p-4 md:p-6">
                <header class="mb-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 w-full max-w-sm">
                        <button id="sidebarToggle" type="button" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 lg:hidden">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.1-5.15a6.25 6.25 0 11-12.5 0 6.25 6.25 0 0112.5 0z"/>
                                </svg>
                            </span>
                            <input type="text" placeholder="Cari..." class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                        </div>
                    </div>
                    @php
                        $displayName = auth()->user()?->name ?? 'User';
                        $nameParts = preg_split('/\s+/', trim($displayName)) ?: ['U'];
                        $initials = strtoupper(substr($nameParts[0], 0, 1).substr($nameParts[1] ?? '', 0, 1));
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500">{{ $displayName }}</span>
                        <div class="size-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white grid place-items-center text-xs font-semibold shadow-sm">
                            {{ $initials }}
                        </div>
                    </div>
                </header>

                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
        <script>
            (() => {
                const sidebar = document.getElementById('appSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const toggle = document.getElementById('sidebarToggle');
                let isOpen = false;

                const applyState = () => {
                    const isDesktop = window.innerWidth >= 1024;

                    if (isDesktop) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        overlay.classList.add('hidden');
                        return;
                    }

                    if (isOpen) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                        overlay.classList.remove('hidden');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                        overlay.classList.add('hidden');
                    }
                };

                toggle.addEventListener('click', () => {
                    if (window.innerWidth >= 1024) return;
                    isOpen = !isOpen;
                    applyState();
                });

                overlay.addEventListener('click', () => {
                    isOpen = false;
                    applyState();
                });

                window.addEventListener('resize', () => {
                    isOpen = false;
                    applyState();
                });

                applyState();
            })();
        </script>
        @stack('scripts')
    </body>
</html>
