<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSpreadsheetRequest;
use App\Jobs\ProcessMasterImportCsv;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $platform = $request->string('platform')->value();
        $fraudStatus = $request->string('fraud_status')->value();
        $keyword = trim($request->string('q')->value() ?? '');

        $ordersQuery = Order::query()
            ->with(['store'])
            ->applyListFilters($request);

        $aggregates = (clone $ordersQuery)->selectRaw("
            SUM(COALESCE(selling_price, total_amount, 0)) as total_sales,
            SUM(COALESCE(net_revenue, total_amount, 0)) as total_net_revenue,
            COALESCE(SUM(marketplace_fee), 0) as total_marketplace_fee,
            COALESCE(SUM(cost_price), 0) as total_cost_price,
            COALESCE(SUM(gross_profit), 0) as total_gross_profit,
            AVG(margin_percent) as avg_margin_percent,
            COUNT(id) as total_orders,
            SUM(CASE WHEN fraud_status = 'valid' THEN 1 ELSE 0 END) as valid_count,
            SUM(CASE WHEN fraud_status = 'suspicious' THEN 1 ELSE 0 END) as suspicious_count,
            SUM(CASE WHEN fraud_status = 'fraud' THEN 1 ELSE 0 END) as fraud_count
        ")->first();

        $totalSales = (float) $aggregates->total_sales;
        $totalNetRevenue = (float) $aggregates->total_net_revenue;
        $totalMarketplaceFee = (float) $aggregates->total_marketplace_fee;
        $totalCostPrice = (float) $aggregates->total_cost_price;
        $totalGrossProfit = (float) $aggregates->total_gross_profit;
        $avgMarginPercent = $aggregates->avg_margin_percent !== null ? (float) $aggregates->avg_margin_percent : null;
        $totalOrders = (int) $aggregates->total_orders;
        $validCount = (int) $aggregates->valid_count;
        $suspiciousCount = (int) $aggregates->suspicious_count;
        $fraudCount = (int) $aggregates->fraud_count;

        $salesByPlatform = (clone $ordersQuery)
            ->select(
                'platform',
                DB::raw('SUM(COALESCE(selling_price, total_amount, 0)) as total_sales'),
                DB::raw('SUM(COALESCE(net_revenue, total_amount, 0)) as net_revenue'),
                DB::raw('COALESCE(SUM(gross_profit), 0) as gross_profit'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('platform')
            ->orderBy('platform')
            ->get();

        $ordersByStatus = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $dailySalesTrend = $this->buildDailySalesTrend($ordersQuery, $from, $to);

        $transactions = (clone $ordersQuery)
            ->orderByDesc('ordered_at')
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', [
            'totalSales' => $totalSales,
            'totalNetRevenue' => $totalNetRevenue,
            'totalMarketplaceFee' => $totalMarketplaceFee,
            'totalCostPrice' => $totalCostPrice,
            'totalGrossProfit' => $totalGrossProfit,
            'avgMarginPercent' => $avgMarginPercent,
            'totalOrders' => $totalOrders,
            'validCount' => $validCount,
            'suspiciousCount' => $suspiciousCount,
            'fraudCount' => $fraudCount,
            'salesByPlatform' => $salesByPlatform,
            'ordersByStatus' => $ordersByStatus,
            'dailySalesTrend' => $dailySalesTrend,
            'transactions' => $transactions,
            'filters' => $request->only(['from', 'to', 'platform', 'fraud_status', 'q']),
        ]);
    }

    public function export(Request $request): Response
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $platform = $request->string('platform')->value();
        $fraudStatus = $request->string('fraud_status')->value();
        $keyword = trim($request->string('q')->value() ?? '');

        $orders = Order::query()
            ->with(['store', 'items'])
            ->when($from !== null, fn ($query) => $query->whereDate('ordered_at', '>=', $from))
            ->when($to !== null, fn ($query) => $query->whereDate('ordered_at', '<=', $to))
            ->when(in_array($platform, ['shopee', 'tiktok_shop'], true), fn ($query) => $query->where('platform', $platform))
            ->when(in_array($fraudStatus, ['valid', 'suspicious', 'fraud'], true), fn ($query) => $query->where('fraud_status', $fraudStatus))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('order_number', 'like', "%{$keyword}%")
                        ->orWhere('buyer_name', 'like', "%{$keyword}%")
                        ->orWhere('buyer_phone', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('ordered_at')
            ->get();

        $headers = [
            'No',
            'Nama Produk',
            'Brand',
            'ID Pesanan',
            'Tanggal Pemesanan',
            'Shopee/Tiktok',
            'Metode Bayar',
            'Akun Pembeli',
            'Identitas Pembeli',
            'Ekspedisi',
            'Status',
            'Harga Jual',
            'Bruto',
            'Harga Modal',
            'Margin',
        ];

        $csvContent = implode(',', $headers)."\n";
        $no = 1;

        foreach ($orders as $order) {
            $product = $order->items->first();
            $namaProduk = $product ? $product->product_name : 'Produk';
            $brand = $product ? $product->sku : '-';
            
            $tanggal = optional($order->ordered_at)->format('j F Y H.i') ?? '-';
            
            $platformName = $order->platform === 'tiktok_shop' ? 'TikTok' : 'Shopee';
            $identitas = sprintf("%s (%s) %s", $order->buyer_name, $order->buyer_phone ?: '-', preg_replace('/[\r\n]+/', ' ', $order->shipping_address));

            $row = [
                $no++,
                $namaProduk,
                $order->brand ?: ($product ? $product->sku : '-'),
                $order->order_number,
                $tanggal,
                $order->marketplace_account ?: $platformName,
                $order->payment_method ?: '-',
                $order->buyer_username ?: $order->buyer_name,
                $identitas,
                trim(($order->courier ?: '-').($order->tracking_number ? ' ('.$order->tracking_number.')' : '')),
                ucfirst($order->status),
                (string) (int) ($order->selling_price ?? $order->total_amount),
                (string) (int) ($order->net_revenue ?? $order->total_amount),
                $order->cost_price ? (string) (int) $order->cost_price : '',
                $order->gross_profit ? (string) (int) $order->gross_profit : '',
            ];

            $csvContent .= $this->toCsvRow($row)."\n";
        }

        $filename = 'Database Penjualan '.now()->format('F Y').'.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Order> $ordersQuery
     * @return array{
     *     labels: array<int, string>,
     *     sales: array<int, float>,
     *     net_revenue: array<int, float>,
     *     profits: array<int, float>,
     *     orders: array<int, int>,
     *     period_label: string
     * }
     */
    private function buildDailySalesTrend($ordersQuery, ?Carbon $from, ?Carbon $to): array
    {
        $boundsQuery = clone $ordersQuery;

        if ($from !== null) {
            $chartStart = $from->copy()->startOfDay();
        } else {
            $minDate = $boundsQuery->min('ordered_at');
            $chartStart = $minDate !== null
                ? Carbon::parse($minDate)->startOfDay()
                : now()->startOfDay();
        }

        if ($to !== null) {
            $chartEnd = $to->copy()->startOfDay();
        } else {
            $maxDate = $boundsQuery->max('ordered_at');
            $chartEnd = $maxDate !== null
                ? Carbon::parse($maxDate)->startOfDay()
                : now()->startOfDay();
        }

        if ($chartStart->gt($chartEnd)) {
            $chartEnd = $chartStart->copy();
        }

        $raw = (clone $ordersQuery)
            ->whereBetween('ordered_at', [$chartStart, $chartEnd->copy()->endOfDay()])
            ->selectRaw('
                DATE(ordered_at) as date,
                SUM(COALESCE(selling_price, total_amount, 0)) as total_sales,
                SUM(COALESCE(net_revenue, total_amount, 0)) as net_revenue,
                SUM(COALESCE(gross_profit, 0)) as gross_profit,
                COUNT(*) as total_orders
            ')
            ->groupBy(DB::raw('DATE(ordered_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'));

        $labels = [];
        $sales = [];
        $netRevenue = [];
        $profits = [];
        $orders = [];

        for ($date = $chartStart->copy(); $date->lte($chartEnd); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $row = $raw->get($key);

            $labels[] = $date->format('d M');
            $sales[] = (float) ($row->total_sales ?? 0);
            $netRevenue[] = (float) ($row->net_revenue ?? 0);
            $profits[] = (float) ($row->gross_profit ?? 0);
            $orders[] = (int) ($row->total_orders ?? 0);
        }

        return [
            'labels' => $labels,
            'sales' => $sales,
            'net_revenue' => $netRevenue,
            'profits' => $profits,
            'orders' => $orders,
            'period_label' => $chartStart->format('d M Y').' – '.$chartEnd->format('d M Y'),
        ];
    }

    /**
     * @param array<int, mixed> $values
     */
    private function toCsvRow(array $values): string
    {
        return implode(',', array_map(function ($value): string {
            $escaped = str_replace('"', '""', (string) $value);

            return "\"{$escaped}\"";
        }, $values));
    }

    public function importMaster(ImportSpreadsheetRequest $request): RedirectResponse
    {
        $file = $request->spreadsheetFile();
        $originalFilename = $file->getClientOriginalName();
        $path = $file->store('imports', 'local');

        if (! $path) {
            return back()->withErrors(['master_file' => 'Gagal menyimpan file untuk diproses.']);
        }

        ProcessMasterImportCsv::dispatch($path, $originalFilename);

        return redirect()
            ->route('reports.index')
            ->with('success', 'File Master Excel/CSV berhasil diupload. Proses import sedang berjalan di latar belakang.');
    }
}
