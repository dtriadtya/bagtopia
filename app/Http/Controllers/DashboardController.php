<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalOrders = Order::count();
        $financial = Order::query()->selectRaw('
            SUM(COALESCE(selling_price, total_amount, 0)) as gross_sales,
            SUM(COALESCE(net_revenue, total_amount, 0)) as net_revenue,
            COALESCE(SUM(marketplace_fee), 0) as marketplace_fee,
            COALESCE(SUM(cost_price), 0) as total_cost,
            COALESCE(SUM(gross_profit), 0) as gross_profit,
            AVG(margin_percent) as avg_margin
        ')->first();

        $suspiciousCount = Order::where('fraud_status', 'suspicious')->count();
        $fraudCount = Order::where('fraud_status', 'fraud')->count();

        $salesByPlatform = Order::query()
            ->select(
                'platform',
                DB::raw('SUM(COALESCE(selling_price, total_amount, 0)) as gross_sales'),
                DB::raw('SUM(COALESCE(net_revenue, total_amount, 0)) as net_revenue'),
                DB::raw('COALESCE(SUM(gross_profit), 0) as gross_profit')
            )
            ->groupBy('platform')
            ->orderBy('platform')
            ->get();

        $recentOrders = Order::query()
            ->with(['store'])
            ->latest('ordered_at')
            ->limit(10)
            ->get();

        $fraudAlerts = Order::query()
            ->with(['fraudLogs'])
            ->whereIn('fraud_status', ['suspicious', 'fraud'])
            ->latest('ordered_at')
            ->limit(3)
            ->get();

        $recentImports = ImportLog::query()
            ->with(['store'])
            ->latest('imported_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalOrders' => $totalOrders,
            'grossSales' => (float) $financial->gross_sales,
            'netRevenue' => (float) $financial->net_revenue,
            'marketplaceFee' => (float) $financial->marketplace_fee,
            'totalCost' => (float) $financial->total_cost,
            'grossProfit' => (float) $financial->gross_profit,
            'avgMargin' => $financial->avg_margin !== null ? (float) $financial->avg_margin : null,
            'suspiciousCount' => $suspiciousCount,
            'fraudCount' => $fraudCount,
            'salesByPlatform' => $salesByPlatform,
            'recentOrders' => $recentOrders,
            'fraudAlerts' => $fraudAlerts,
            'recentImports' => $recentImports,
        ]);
    }
}
