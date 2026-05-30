<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSpreadsheetRequest;
use App\Jobs\ProcessMasterImportCsv;
use App\Models\FraudLog;
use App\Models\Order;
use App\Models\Store;
use App\Services\FraudDetectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['store'])
            ->applyListFilters($request)
            ->latest('ordered_at')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', [
            'orders' => $orders,
            'platforms' => ['shopee', 'tiktok_shop'],
            'fraudStatuses' => ['valid', 'suspicious', 'fraud'],
            'statuses' => ['pending', 'paid', 'shipped', 'completed', 'cancelled'],
            'filters' => $request->only(['platform', 'fraud_status', 'from', 'to']),
        ]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'stores' => Store::query()->where('is_active', true)->orderBy('name')->get(),
            'platforms' => ['shopee', 'tiktok_shop'],
            'statuses' => ['pending', 'paid', 'shipped', 'completed', 'cancelled'],
        ]);
    }

    public function store(Request $request, FraudDetectionService $fraudDetectionService): RedirectResponse
    {
        $validated = $request->validate([
            'store_id' => ['required', Rule::exists('stores', 'id')],
            'order_number' => ['required', 'string', 'max:120', 'unique:orders,order_number'],
            'buyer_name' => ['required', 'string', 'max:120'],
            'buyer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['required', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'platform' => ['required', Rule::in(['shopee', 'tiktok_shop'])],
            'status' => ['required', Rule::in(['pending', 'paid', 'shipped', 'completed', 'cancelled'])],
            'ordered_at' => ['required', 'date'],
        ]);

        $fraudResult = $fraudDetectionService->calculate(
            buyerPhone: $validated['buyer_phone'] ?? null,
            shippingAddress: $validated['shipping_address']
        );

        $order = Order::create([
            ...$validated,
            'fraud_score' => $fraudResult['score'],
            'fraud_status' => $fraudResult['status'],
        ]);

        foreach ($fraudResult['logs'] as $log) {
            FraudLog::create([
                'order_id' => $order->id,
                'rule_key' => $log['rule_key'],
                'rule_label' => $log['rule_label'],
                'points' => $log['points'],
                'notes' => $log['notes'],
            ]);
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order berhasil disimpan dan fraud scoring sudah dijalankan.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'paid', 'shipped', 'completed', 'cancelled'])],
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', "Status order {$order->order_number} diperbarui ke ".strtoupper($validated['status']).'.');
    }

    public function importCsv(ImportSpreadsheetRequest $request): RedirectResponse
    {
        $file = $request->spreadsheetFile();
        $originalFilename = $file->getClientOriginalName();
        $path = $file->store('imports', 'local');

        if (! $path) {
            return back()->withErrors(['csv_file' => 'Gagal menyimpan file Excel/CSV untuk diproses.']);
        }

        ProcessMasterImportCsv::dispatch($path, $originalFilename);

        return redirect()
            ->route('orders.index')
            ->with('success', 'File Excel/CSV berhasil diupload. Proses import sedang berjalan di latar belakang.');
    }

    public function downloadTemplate(Request $request): Response
    {
        $platform = $request->string('platform')->value() === 'tiktok_shop' ? 'tiktok_shop' : 'shopee';

        $headers = [
            'order_number',
            'buyer_name',
            'buyer_phone',
            'shipping_address',
            'total_amount',
            'status',
            'order_date',
            'product_name',
            'sku',
            'quantity',
            'unit_price',
            'subtotal',
        ];

        $sampleRows = $platform === 'shopee'
            ? [
                ['SHP-ORDER-001', 'Andi Saputra', '081234567890', 'Jl. Mawar No 12 Bandung', '275000', 'paid', '2026-04-25 10:15:00', 'Sling Bag Mini', 'SLING-001', '1', '275000', '275000'],
                ['SHP-ORDER-002', 'Rina Putri', '081300000111', 'Jl. Kenanga No 2 Surabaya', '560000', 'shipped', '2026-04-25 11:45:00', 'Tote Bag Premium', 'TOTE-002', '2', '280000', '560000'],
            ]
            : [
                ['TTS-ORDER-001', 'Budi Santoso', '081200001111', 'Jl. Melati No 18 Jakarta', '450000', 'pending', '2026-04-25 09:00:00', 'Backpack Urban', 'BPACK-003', '1', '450000', '450000'],
                ['TTS-ORDER-002', 'Nia Ramadhani', '081344445555', 'Jl. Dahlia No 7 Medan', '720000', 'completed', '2026-04-25 14:05:00', 'Laptop Sleeve', 'LSLV-004', '3', '240000', '720000'],
            ];

        $content = implode(',', $headers)."\n";

        foreach ($sampleRows as $row) {
            $content .= implode(',', $row)."\n";
        }

        $filename = 'template-import-'.$platform.'.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
