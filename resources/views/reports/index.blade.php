@extends('layouts.app')

@section('content')
    <section class="mb-4 flex flex-wrap gap-4 items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Reports</h2>
            <p class="text-sm text-slate-500">Ringkasan performa penjualan dan fraud berdasarkan periode.</p>
        </div>
        
        <form action="{{ route('reports.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
            @csrf
            <input type="file" name="master_file" accept=".csv,.txt,.xlsx,.xls" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm bg-white" required>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm whitespace-nowrap hover:bg-slate-800">
                Import Data Penjualan
            </button>
        </form>
    </section>

    <section class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm mb-4">
        <form method="GET" class="grid gap-3 md:grid-cols-5">
            <div>
                <label class="block text-sm mb-1">Dari tanggal</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Sampai tanggal</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Platform</label>
                <select name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Semua</option>
                    <option value="shopee" @selected(($filters['platform'] ?? '') === 'shopee')>Shopee</option>
                    <option value="tiktok_shop" @selected(($filters['platform'] ?? '') === 'tiktok_shop')>TikTok Shop</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Fraud Status</label>
                <select name="fraud_status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    <option value="">Semua</option>
                    <option value="valid" @selected(($filters['fraud_status'] ?? '') === 'valid')>Valid</option>
                    <option value="suspicious" @selected(($filters['fraud_status'] ?? '') === 'suspicious')>Suspicious</option>
                    <option value="fraud" @selected(($filters['fraud_status'] ?? '') === 'fraud')>Fraud</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Cari transaksi</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order / Buyer / Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div class="flex items-end gap-2 md:col-span-5">
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Terapkan</button>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-lg border border-slate-300">Reset</a>
                <button type="submit" formaction="{{ route('reports.export') }}" class="px-4 py-2 rounded-lg border border-emerald-300 text-emerald-700 hover:bg-emerald-50">
                    Export CSV
                </button>
            </div>
        </form>
    </section>

    <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-4 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Gross Sales (Harga Jual)</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-emerald-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Net Revenue (Bruto)</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-indigo-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Laba Kotor</p>
            <p class="mt-2 text-2xl font-semibold text-indigo-700">Rp {{ number_format($totalGrossProfit, 0, ',', '.') }}</p>
            @if ($avgMarginPercent !== null)
                <p class="text-[11px] text-slate-500">Margin {{ number_format($avgMarginPercent, 1, ',', '.') }}%</p>
            @endif
        </div>
        <div class="bg-white border border-amber-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Potongan Marketplace</p>
            <p class="mt-2 text-2xl font-semibold text-amber-700">Rp {{ number_format($totalMarketplaceFee, 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-500">HPP: Rp {{ number_format($totalCostPrice, 0, ',', '.') }}</p>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Orders</p>
            <p class="mt-2 text-2xl font-semibold">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Suspicious</p>
            <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $suspiciousCount }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500">Fraud</p>
            <p class="mt-2 text-2xl font-semibold text-rose-600">{{ $fraudCount }}</p>
        </div>
    </section>

    <section class="grid gap-4 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-sm">Tren Penjualan Harian</h3>
                    <p class="text-xs text-slate-500">Gross, net (bruto), dan laba kotor per hari · {{ $dailySalesTrend['period_label'] }}</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h3 class="font-semibold text-sm mb-1">Penjualan per Platform</h3>
                <p class="text-xs text-slate-500 mb-4">Distribusi revenue Shopee vs TikTok Shop</p>
                <div class="h-56">
                    <canvas id="platformSalesChart"></canvas>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h3 class="font-semibold text-sm mb-1">Status Fraud</h3>
                <p class="text-xs text-slate-500 mb-4">Valid, suspicious, dan fraud</p>
                <div class="h-56">
                    <canvas id="fraudStatusChart"></canvas>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <h3 class="font-semibold text-sm mb-1">Status Order</h3>
                <p class="text-xs text-slate-500 mb-4">Pending, paid, shipped, completed, cancelled</p>
                <div class="h-56">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <h3 class="font-semibold text-sm mb-3">Ringkasan per Platform</h3>
            <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($salesByPlatform as $item)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="text-sm font-medium">{{ strtoupper($item->platform) }} · {{ $item->total_orders }} order</p>
                        <p class="text-xs text-slate-600 mt-1">Gross Rp {{ number_format((float) $item->total_sales, 0, ',', '.') }}</p>
                        <p class="text-xs text-emerald-700">Net Rp {{ number_format((float) $item->net_revenue, 0, ',', '.') }}</p>
                        <p class="text-xs text-indigo-700">Laba Rp {{ number_format((float) $item->gross_profit, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data platform.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <h3 class="font-semibold text-sm mb-2">Fraud Breakdown</h3>
        <p class="text-sm text-slate-600">
            Valid: <span class="font-semibold text-emerald-700">{{ $validCount }}</span> ·
            Suspicious: <span class="font-semibold text-amber-700">{{ $suspiciousCount }}</span> ·
            Fraud: <span class="font-semibold text-rose-700">{{ $fraudCount }}</span>
        </p>
    </section>

    <section class="mt-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm overflow-x-auto">
        <h3 class="font-semibold text-sm mb-3">Laporan Transaksi</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200">
                    <th class="py-2">Order ID</th>
                    <th class="py-2">Tanggal</th>
                    <th class="py-2">Customer</th>
                    <th class="py-2">Platform</th>
                    <th class="py-2">Bruto</th>
                    <th class="py-2">Laba</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Fraud</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 font-medium">{{ $trx->order_number }}</td>
                        <td class="py-2">{{ optional($trx->ordered_at)->format('d/m/Y H:i') }}</td>
                        <td class="py-2">
                            <p>{{ $trx->buyer_name }}</p>
                            <p class="text-xs text-slate-500">{{ $trx->buyer_phone ?: '-' }}</p>
                        </td>
                        <td class="py-2 uppercase text-xs">{{ $trx->platform }}</td>
                        <td class="py-2">Rp {{ number_format((float) ($trx->net_revenue ?? $trx->total_amount), 0, ',', '.') }}</td>
                        <td class="py-2">
                            @if ($trx->gross_profit !== null)
                                Rp {{ number_format((float) $trx->gross_profit, 0, ',', '.') }}
                                @if ($trx->margin_percent)
                                    <span class="text-xs text-slate-500">({{ number_format((float) $trx->margin_percent, 1, ',', '.') }}%)</span>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700">{{ strtoupper($trx->status) }}</span>
                        </td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                @if ($trx->fraud_status === 'fraud') bg-rose-100 text-rose-700
                                @elseif ($trx->fraud_status === 'suspicious') bg-amber-100 text-amber-700
                                @else bg-emerald-100 text-emerald-700 @endif">
                                {{ strtoupper($trx->fraud_status) }} ({{ $trx->fraud_score }})
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-slate-500">Belum ada data transaksi di periode/filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 12,
                        font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" },
                    },
                },
            },
        };

        const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

        new Chart(document.getElementById('dailySalesChart'), {
            type: 'line',
            data: {
                labels: @json($dailySalesTrend['labels']),
                datasets: [
                    {
                        label: 'Gross Sales',
                        data: @json($dailySalesTrend['sales']),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Net Revenue (Bruto)',
                        data: @json($dailySalesTrend['net_revenue']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Laba Kotor',
                        data: @json($dailySalesTrend['profits']),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.08)',
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Jumlah Order',
                        data: @json($dailySalesTrend['orders']),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                ...chartDefaults,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        position: 'left',
                        ticks: {
                            callback: (value) => formatRupiah(value),
                            font: { size: 10 },
                        },
                        grid: { color: '#e2e8f0' },
                    },
                    y1: {
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1, font: { size: 10 } },
                    },
                    x: {
                        ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } },
                        grid: { display: false },
                    },
                },
                plugins: {
                    ...chartDefaults.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                if (context.dataset.label === 'Jumlah Order') {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }

                                return `${context.dataset.label}: ${formatRupiah(context.parsed.y)}`;
                            },
                        },
                    },
                },
            },
        });

        const platformLabels = @json($salesByPlatform->map(fn ($item) => strtoupper($item->platform))->values());
        const platformSales = @json($salesByPlatform->map(fn ($item) => (float) $item->net_revenue)->values());

        new Chart(document.getElementById('platformSalesChart'), {
            type: 'doughnut',
            data: {
                labels: platformLabels.length ? platformLabels : ['Tidak ada data'],
                datasets: [{
                    data: platformSales.length ? platformSales : [1],
                    backgroundColor: ['#ee4d2d', '#111827', '#64748b'],
                    borderWidth: 0,
                }],
            },
            options: {
                ...chartDefaults,
                cutout: '62%',
                plugins: {
                    ...chartDefaults.plugins,
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.label}: ${formatRupiah(context.parsed)}`,
                        },
                    },
                },
            },
        });

        new Chart(document.getElementById('fraudStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Valid', 'Suspicious', 'Fraud'],
                datasets: [{
                    data: [{{ $validCount }}, {{ $suspiciousCount }}, {{ $fraudCount }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                    borderWidth: 0,
                }],
            },
            options: {
                ...chartDefaults,
                cutout: '62%',
            },
        });

        const orderStatusLabels = @json($ordersByStatus->map(fn ($item) => strtoupper($item->status))->values());
        const orderStatusTotals = @json($ordersByStatus->map(fn ($item) => (int) $item->total)->values());

        new Chart(document.getElementById('orderStatusChart'), {
            type: 'bar',
            data: {
                labels: orderStatusLabels.length ? orderStatusLabels : ['Tidak ada data'],
                datasets: [{
                    label: 'Jumlah Order',
                    data: orderStatusTotals.length ? orderStatusTotals : [0],
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                }],
            },
            options: {
                ...chartDefaults,
                plugins: {
                    ...chartDefaults.plugins,
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#e2e8f0' },
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false },
                    },
                },
            },
        });
    </script>
@endpush
