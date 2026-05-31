<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Admin - Bagtopia</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-200 via-blue-100 to-indigo-200 text-slate-800 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <main class="min-h-screen grid place-items-center px-4 py-8">
            <section class="w-full max-w-4xl overflow-hidden rounded-3xl border border-white/50 bg-white/80 shadow-2xl backdrop-blur-sm">
                <div class="grid md:grid-cols-2">
                    <aside class="hidden md:flex flex-col justify-between bg-slate-950 p-8 text-slate-100">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs">
                                <span class="size-2 rounded-full bg-emerald-400"></span>
                                Admin Access Only
                            </div>
                            <h1 class="mt-4 text-3xl font-bold leading-tight">Bagtopia Monitoring</h1>
                            <p class="mt-3 text-sm text-slate-300">
                                Kelola transaksi toko Shopee & TikTok Shop, deteksi fraud otomatis, dan akses laporan penjualan dari satu dashboard.
                            </p>
                        </div>
                    </aside>

                    <div class="p-6 md:p-8">
                        <div class="mb-6 text-center md:text-left">
                            <div class="mb-3 inline-flex size-10 rounded-xl bg-blue-600 text-white grid place-items-center shadow-sm">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4zm0 5v5m0 0l2-2m-2 2l-2-2"/>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-slate-900">Login</h2>
                            <p class="mt-1 text-sm text-slate-500">Masuk ke akun admin Anda</p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form id="loginForm" method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Email</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16v12H4zM4 7l8 6 8-6"/>
                                        </svg>
                                    </span>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" class="w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200" required>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Password</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11V8a5 5 0 0110 0v3m-9 0h8a1 1 0 011 1v7H7v-7a1 1 0 011-1z"/>
                                        </svg>
                                    </span>
                                    <input id="passwordInput" type="password" name="password" class="w-full rounded-lg border border-slate-200 bg-white pl-9 pr-16 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200" required>
                                    <button id="togglePassword" type="button" class="absolute inset-y-0 right-2 px-2 text-xs font-semibold text-slate-500 hover:text-slate-700">
                                        Show
                                    </button>
                                </div>
                            </div>

                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                                Tetap login
                            </label>

                            <button id="loginButton" type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Masuk
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <script>
            (() => {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('passwordInput');
                const loginForm = document.getElementById('loginForm');
                const loginButton = document.getElementById('loginButton');

                togglePassword.addEventListener('click', () => {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    togglePassword.textContent = isPassword ? 'Hide' : 'Show';
                });

                loginForm.addEventListener('submit', () => {
                    loginButton.disabled = true;
                    loginButton.textContent = 'Masuk...';
                    loginButton.classList.add('opacity-70', 'cursor-not-allowed');
                });
            })();
        </script>
    </body>
</html>
