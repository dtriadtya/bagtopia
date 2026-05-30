@extends('layouts.app')

@section('content')
    <section class="mb-4">
        <h2 class="text-2xl font-bold tracking-tight">Fraud Detection</h2>
        <p class="text-sm text-slate-500">Monitoring hasil pengecekan fraud per order beserta detail rule yang terpicu.</p>
    </section>

    <section class="grid gap-3 md:grid-cols-3 mb-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Valid</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ $summary['valid'] }}</p>
            <p class="text-[11px] text-slate-500">Skor 0–1, tidak ada indikasi</p>
        </div>
        <div class="bg-white border border-amber-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Suspicious</p>
            <p class="mt-1 text-2xl font-semibold text-amber-700">{{ $summary['suspicious'] }}</p>
            <p class="text-[11px] text-slate-500">Skor 2–3, perlu ditinjau</p>
        </div>
        <div class="bg-white border border-rose-200 rounded-xl p-4 shadow-sm">
            <p class="text-[11px] text-slate-500">Fraud</p>
            <p class="mt-1 text-2xl font-semibold text-rose-700">{{ $summary['fraud'] }}</p>
            <p class="text-[11px] text-slate-500">Skor &gt; 3, risiko tinggi</p>
        </div>
    </section>

    <section class="bg-white border border-slate-200 rounded-2xl p-5">
        <form method="GET" class="grid gap-3 md:grid-cols-5 mb-5">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Fraud Status</label>
                <select name="fraud_status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua Status</option>
                    @foreach ($fraudStatuses as $status)
                        <option value="{{ $status }}" @selected(($filters['fraud_status'] ?? '') === $status)>{{ strtoupper($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Platform</label>
                <select name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua Platform</option>
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform }}" @selected(($filters['platform'] ?? '') === $platform)>{{ strtoupper($platform) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Dari tanggal</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Sampai tanggal</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Cari order</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order / Buyer / Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Filter</button>
                <a href="{{ route('fraud.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left">
                        <th class="py-2 pr-3">Order</th>
                        <th class="py-2 pr-3">Tanggal</th>
                        <th class="py-2 pr-3">Platform</th>
                        <th class="py-2 pr-3">Skor</th>
                        <th class="py-2 pr-3">Status</th>
                        <th class="py-2">Detail Pengecekan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="py-3 pr-3">
                                <p class="font-medium">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ $order->buyer_name }}</p>
                                <p class="text-xs text-slate-400">{{ $order->buyer_phone ?: '-' }}</p>
                                @if ($order->store)
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $order->store->name }}</p>
                                @endif
                            </td>
                            <td class="py-3 pr-3 whitespace-nowrap">{{ optional($order->ordered_at)->format('d M Y H:i') }}</td>
                            <td class="py-3 pr-3 uppercase">{{ $order->platform }}</td>
                            <td class="py-3 pr-3 font-semibold">{{ $order->fraud_score }}</td>
                            <td class="py-3 pr-3">
                                <span class="inline-flex text-[11px] font-medium px-2 py-0.5 rounded-full
                                    @if ($order->fraud_status === 'fraud') bg-rose-100 text-rose-700
                                    @elseif ($order->fraud_status === 'suspicious') bg-amber-100 text-amber-700
                                    @else bg-emerald-100 text-emerald-700 @endif">
                                    {{ strtoupper($order->fraud_status) }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($order->fraudLogs->isNotEmpty())
                                    <ul class="space-y-2">
                                        @foreach ($order->fraudLogs as $log)
                                            <li class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-xs font-medium text-slate-800">{{ $log->rule_label }}</span>
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">+{{ $log->points }} poin</span>
                                                </div>
                                                @if ($log->notes)
                                                    <p class="text-[11px] text-slate-600 mt-1">{{ $log->notes }}</p>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-xs text-slate-500 italic">Tidak ada rule terpicu — order lolos pengecekan.</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">Belum ada order untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    </section>
@endsection
