<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FraudDetectionController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['store', 'fraudLogs'])
            ->applyListFilters($request)
            ->latest('ordered_at')
            ->paginate(15)
            ->withQueryString();

        $summary = Order::query()
            ->selectRaw('fraud_status, COUNT(*) as total')
            ->groupBy('fraud_status')
            ->pluck('total', 'fraud_status');

        return view('fraud.index', [
            'orders' => $orders,
            'summary' => [
                'valid' => (int) ($summary['valid'] ?? 0),
                'suspicious' => (int) ($summary['suspicious'] ?? 0),
                'fraud' => (int) ($summary['fraud'] ?? 0),
            ],
            'platforms' => ['shopee', 'tiktok_shop'],
            'fraudStatuses' => ['valid', 'suspicious', 'fraud'],
            'filters' => $request->only(['platform', 'fraud_status', 'from', 'to', 'q']),
        ]);
    }
}
