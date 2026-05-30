@extends('layouts.app')

@section('content')
    <section class="mb-4">
        <h2 class="text-2xl font-bold tracking-tight">Settings (Super Admin)</h2>
        <p class="text-sm text-slate-500">Pengaturan akun user admin dan informasi detail akun.</p>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm lg:col-span-1">
            <h3 class="font-semibold text-sm mb-3">Akun Login Saat Ini</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-2"><dt class="text-slate-500">Nama</dt><dd>{{ $currentUser->name }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-500">Email</dt><dd>{{ $currentUser->email }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-500">Role</dt><dd class="uppercase">{{ str_replace('_', ' ', $currentUser->role) }}</dd></div>
            </dl>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm lg:col-span-2">
            <h3 class="font-semibold text-sm mb-3">Tambah Akun Admin</h3>
            <form method="POST" action="{{ route('settings.users.store') }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                <input type="text" name="name" placeholder="Nama admin" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <input type="email" name="email" placeholder="Email admin" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <input type="password" name="password" placeholder="Password minimal 8 karakter" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                <select name="role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
                <div class="md:col-span-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Tambah User</button>
                </div>
            </form>
        </div>
    </section>

    <section class="mt-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm overflow-x-auto">
        <h3 class="font-semibold text-sm mb-3">Daftar Akun Admin</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200">
                    <th class="py-2">Nama</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Role</th>
                    <th class="py-2">Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-slate-100">
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2">{{ $user->email }}</td>
                        <td class="py-2 uppercase">{{ str_replace('_', ' ', $user->role) }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('settings.users.update', $user) }}" class="grid gap-2 md:grid-cols-4">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $user->name }}" class="rounded border border-slate-300 px-2 py-1 text-xs">
                                <input type="email" name="email" value="{{ $user->email }}" class="rounded border border-slate-300 px-2 py-1 text-xs">
                                <select name="role" class="rounded border border-slate-300 px-2 py-1 text-xs">
                                    <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    <option value="super_admin" @selected($user->role === 'super_admin')>Super Admin</option>
                                </select>
                                <button type="submit" class="rounded bg-slate-900 px-2 py-1 text-xs text-white">Simpan</button>
                                <input type="password" name="password" placeholder="Password baru (opsional)" class="md:col-span-4 rounded border border-slate-300 px-2 py-1 text-xs">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
