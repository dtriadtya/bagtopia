@extends('layouts.app')

@section('content')
    <section class="bg-white border border-slate-200 rounded-2xl p-5 mb-4">
        <h2 class="text-lg font-semibold mb-1">Import Data Order (Excel/CSV)</h2>
        <p class="text-sm text-slate-500 mb-4">Upload Excel/CSV rekap order, sistem akan memecah data, simpan order + item, lalu hitung fraud otomatis.</p>
        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('orders.template-csv', ['platform' => 'shopee']) }}" class="text-xs px-3 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Download Template Shopee</a>
            <a href="{{ route('orders.template-csv', ['platform' => 'tiktok_shop']) }}" class="text-xs px-3 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Download Template TikTok Shop</a>
        </div>

        <form action="{{ route('orders.import-csv') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">File Excel/CSV</label>
                <input type="file" name="csv_file" accept=".csv,.txt,.xlsx,.xls" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">Max 10MB. Format sama dengan Database Penjualan — kolom ID Pesanan, Brand, Harga Jual, Bruto, Harga Modal, dsb.</p>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Upload & Import</button>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-300">Lihat Data Order</a>
            </div>
        </form>
    </section>

    <section class="bg-white border border-slate-200 rounded-2xl p-5">
        <h3 class="text-base font-semibold mb-3">Input Manual (Backup)</h3>
        <form action="{{ route('orders.store') }}" method="POST" class="grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <label class="block text-sm mb-1">Store</label>
                <select name="store_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Pilih store marketplace</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}" @selected((string) old('store_id') === (string) $store->id)>
                            {{ $store->name }} ({{ strtoupper($store->platform) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Nomor Order</label>
                <input type="text" name="order_number" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Nama Pembeli</label>
                <input type="text" name="buyer_name" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Nomor HP Pembeli</label>
                <input type="text" name="buyer_phone" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Platform</label>
                <select name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform }}">{{ strtoupper($platform) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Alamat Pengiriman</label>
                <textarea name="shipping_address" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Total Transaksi</label>
                <input type="number" step="0.01" min="0" name="total_amount" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Status Order</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Waktu Order</label>
                <input type="datetime-local" name="ordered_at" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white">Simpan Manual</button>
            </div>
        </form>
    </section>
@endsection
