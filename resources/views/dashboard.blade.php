@extends('layouts.app')

@section('content')
    <section class="mb-4">
        <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
        <p class="text-sm text-slate-500">Ringkasan penjualan, keuangan, dan fraud toko Bagtopedia.</p>
    </section>

    <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-4 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Total Orders</p>
            <p class="mt-2 text-2xl font-semibold">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Gross Sales (Harga Jual)</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($grossSales, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-emerald-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Net Revenue (Bruto)</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($netRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-indigo-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Laba Kotor</p>
            <p class="mt-2 text-2xl font-semibold text-indigo-700">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
            @if ($avgMargin !== null)
                <p class="text-[11px] text-slate-500">Margin rata-rata {{ number_format($avgMargin, 1, ',', '.') }}%</p>
            @endif
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3 mb-4">
        <div class="bg-white border border-amber-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Potongan Marketplace</p>
            <p class="mt-2 text-xl font-semibold text-amber-700">Rp {{ number_format($marketplaceFee, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Total HPP / Modal</p>
            <p class="mt-2 text-xl font-semibold">Rp {{ number_format($totalCost, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-rose-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Fraud Alerts</p>
            <p class="mt-2 text-xl font-semibold text-rose-600">{{ $suspiciousCount + $fraudCount }}</p>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-semibold text-sm flex items-center gap-2">
                    <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Recent Orders
                </h3>
                <a href="{{ route('orders.index') }}" class="text-xs text-slate-500 hover:text-slate-700">View All</a>
            </div>
            <div class="space-y-3">
                @forelse ($recentOrders->take(5) as $order)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-500">{{ $order->buyer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">{{ optional($order->ordered_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data order.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-semibold text-sm flex items-center gap-2">
                    <svg class="size-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.73 3h16.9a2 2 0 001.73-3l-8.47-14.14a2 2 0 00-3.46 0z"/></svg>
                    Fraud Alerts
                </h3>
                <a href="{{ route('fraud.index') }}" class="text-xs text-slate-500 hover:text-slate-700">View All</a>
            </div>
            <div class="space-y-2">
                @forelse ($fraudAlerts as $alert)
                    <div class="rounded-lg border px-3 py-2
                        @if ($alert->fraud_status === 'fraud') bg-rose-50 border-rose-200
                        @else bg-amber-50 border-amber-200 @endif">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium">
                                {{ $alert->fraudLogs->first()->rule_label ?? 'Fraud Rule Triggered' }}
                            </p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full
                                @if ($alert->fraud_status === 'fraud') bg-rose-200 text-rose-700
                                @else bg-amber-200 text-amber-700 @endif">
                                {{ strtoupper($alert->fraud_status) }}
                            </span>
                            <span class="text-[11px] text-slate-600">score: +{{ $alert->fraud_score }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-1">
                            {{ optional($alert->ordered_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada fraud alert.</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
                Sales by Platform
            </h3>
            <div class="grid gap-2">
                @forelse ($salesByPlatform as $item)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium">{{ strtoupper($item->platform) }}</span>
                            <span class="text-xs text-slate-500">Laba Rp {{ number_format((float) $item->gross_profit, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1 flex justify-between text-xs text-slate-600">
                            <span>Gross Rp {{ number_format((float) $item->gross_sales, 0, ',', '.') }}</span>
                            <span>Net Rp {{ number_format((float) $item->net_revenue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data penjualan platform.</p>
                @endforelse
            </div>
        </section>

        <section class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16"/></svg>
                Recent Import Logs
            </h3>
            <div class="space-y-2">
                @forelse ($recentImports as $log)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium">{{ $log->file_name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ strtoupper($log->platform) }} · {{ optional($log->imported_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-600">ok: {{ $log->imported_rows }} | dup: {{ $log->duplicate_rows }} | skip: {{ $log->skipped_rows }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px]
                                @if ($log->status === 'success') bg-emerald-100 text-emerald-700
                                @elseif ($log->status === 'partial') bg-amber-100 text-amber-700
                                @else bg-rose-100 text-rose-700 @endif">
                                {{ strtoupper($log->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada riwayat import.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
