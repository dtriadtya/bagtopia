@extends('layouts.app')

@section('content')
    <section class="bg-white border border-slate-200 rounded-2xl p-5">
        <div class="flex flex-wrap gap-2 items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold">Monitoring Order</h2>
                <p class="text-xs text-slate-500">Update status manual jika belum terhubung API Shopee/TikTok Shop.</p>
            </div>
            <a href="{{ route('orders.create') }}" class="px-3 py-2 text-sm rounded-lg bg-slate-900 text-white">Import / Tambah Order</a>
        </div>

        <form method="GET" class="grid gap-3 md:grid-cols-4 mb-5">
            <select name="platform" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Semua Platform</option>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform }}" @selected(($filters['platform'] ?? '') === $platform)>{{ strtoupper($platform) }}</option>
                @endforeach
            </select>
            <select name="fraud_status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Semua Fraud Status</option>
                @foreach ($fraudStatuses as $status)
                    <option value="{{ $status }}" @selected(($filters['fraud_status'] ?? '') === $status)>{{ strtoupper($status) }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <div class="md:col-span-4">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Filter</button>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left">
                        <th class="py-2 pr-3">Order</th>
                        <th class="py-2 pr-3">Tanggal</th>
                        <th class="py-2 pr-3">Platform</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2 pr-3">Bruto</th>
                        <th class="py-2">Fraud</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="py-3 pr-3">
                                <p class="font-medium">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ $order->buyer_name }} · {{ $order->buyer_phone ?: '-' }}</p>
                                @if ($order->courier)
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $order->courier }}@if ($order->tracking_number) · {{ $order->tracking_number }}@endif</p>
                                @endif
                            </td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ optional($order->ordered_at)->format('d M Y H:i') }}</td>
                            <td class="py-3 pr-3 uppercase text-xs">{{ $order->platform }}</td>
                            <td class="py-3 pr-3">
                                <form method="POST" action="{{ route('orders.update-status', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="rounded-lg border border-slate-300 px-2 py-1.5 text-xs font-medium min-w-[120px]
                                            @if ($order->status === 'completed') bg-emerald-50 text-emerald-700 border-emerald-200
                                            @elseif ($order->status === 'shipped') bg-blue-50 text-blue-700 border-blue-200
                                            @elseif ($order->status === 'paid') bg-indigo-50 text-indigo-700 border-indigo-200
                                            @elseif ($order->status === 'cancelled') bg-rose-50 text-rose-700 border-rose-200
                                            @else bg-amber-50 text-amber-700 border-amber-200 @endif"
                                    >
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ strtoupper($status) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="py-3 pr-3">Rp {{ number_format((float) ($order->net_revenue ?? $order->total_amount), 0, ',', '.') }}</td>
                            <td class="py-3">
                                <span class="inline-flex text-[11px] px-2 py-0.5 rounded-full
                                    @if ($order->fraud_status === 'fraud') bg-rose-100 text-rose-700
                                    @elseif ($order->fraud_status === 'suspicious') bg-amber-100 text-amber-700
                                    @else bg-emerald-100 text-emerald-700 @endif">
                                    {{ strtoupper($order->fraud_status) }} ({{ $order->fraud_score }})
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-500">Belum ada order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    </section>
@endsection
