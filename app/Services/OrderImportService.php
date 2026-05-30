<?php

namespace App\Services;

use App\Models\BrandCost;
use App\Models\FraudLog;
use App\Models\ImportLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Support\ImportRowParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;
use Throwable;

class OrderImportService
{
    /** @var array<string, float> */
    private array $brandCostMap = [];

    /** @var array<string, int> */
    private array $storeCache = [];

    public function __construct(
        private FraudDetectionService $fraudDetectionService,
        private OrderFinancialService $orderFinancialService,
    ) {}

    public function import(string $filePath, string $originalFilename): void
    {
        $fullPath = Storage::disk('local')->path($filePath);

        if (! file_exists($fullPath)) {
            $this->logFailure($originalFilename, 'File tidak ditemukan di storage.');

            return;
        }

        try {
            $sheetNames = SimpleExcelReader::create($fullPath)->getSheetNames();
        } catch (Throwable $e) {
            $this->logFailure($originalFilename, 'Gagal membaca file Excel/CSV: '.$e->getMessage());

            return;
        }

        $this->brandCostMap = BrandCost::costMap();
        $fraudContext = FraudImportContext::fromDatabase();

        $sheetsToProcess = count($sheetNames) > 0 ? $sheetNames : [null];
        $imported = 0;
        $duplicates = 0;
        $skipped = 0;
        $totalRows = 0;
        $firstError = null;

        foreach ($sheetsToProcess as $sheetName) {
            $reader = SimpleExcelReader::create($fullPath);

            if ($sheetName !== null) {
                $reader = $reader->fromSheetName($sheetName);
            }

            foreach ($reader->getRows() as $row) {
                $this->processRow(
                    row: $row,
                    fraudContext: $fraudContext,
                    imported: $imported,
                    duplicates: $duplicates,
                    skipped: $skipped,
                    totalRows: $totalRows,
                    firstError: $firstError,
                );
            }
        }

        Storage::disk('local')->delete($filePath);

        $finalStoreId = reset($this->storeCache) ?: Store::query()->value('id');

        ImportLog::create([
            'store_id' => $finalStoreId,
            'platform' => str_contains(implode('|', array_keys($this->storeCache)), 'tiktok') ? 'tiktok_shop' : 'shopee',
            'file_name' => $originalFilename,
            'total_rows' => $totalRows,
            'imported_rows' => $imported,
            'duplicate_rows' => $duplicates,
            'skipped_rows' => $skipped,
            'status' => $imported === 0 ? 'failed' : (($duplicates + $skipped) > 0 ? 'partial' : 'success'),
            'error_message' => $firstError,
            'imported_at' => now(),
        ]);
    }

    private function processRow(
        array $row,
        FraudImportContext $fraudContext,
        int &$imported,
        int &$duplicates,
        int &$skipped,
        int &$totalRows,
        ?string &$firstError,
    ): void {
        $totalRows++;

        $normalizedRow = ImportRowParser::normalizeRow($row);

        $orderNumber = ImportRowParser::pick($normalizedRow, ['id_pesanan', 'order_id', 'order_no', 'order_number', 'order_sn']);

        if ($orderNumber === null || $orderNumber === '') {
            $skipped++;

            return;
        }

        if ($fraudContext->isDuplicateOrder($orderNumber)) {
            $duplicates++;

            return;
        }

        $rawPlatform = ImportRowParser::pick($normalizedRow, ['shopee_tiktok', 'platform', 'sumber'], 'shopee');
        $marketplace = $this->orderFinancialService->parseMarketplaceAccount($rawPlatform);
        $platform = $marketplace['platform'];
        $storeKey = $platform.'|'.($marketplace['account_label'] ?? 'default');
        $storeId = $this->resolveStoreId($storeKey, $platform, $marketplace);

        $rawIdentitas = ImportRowParser::pick($normalizedRow, ['identitas_pembeli', 'alamat_pengiriman', 'shipping_address', 'recipient_address'], '');
        $identitas = ImportRowParser::parseIdentitasPembeli($rawIdentitas);

        $buyerUsername = ImportRowParser::pick($normalizedRow, ['akun_pembeli', 'buyer_username']);
        $buyerName = $identitas['name'] ?? $buyerUsername ?? ImportRowParser::pick($normalizedRow, ['buyer_name', 'customer_name'], 'Unknown Buyer');
        $buyerPhone = $identitas['phone'] ?? ImportRowParser::pick($normalizedRow, ['buyer_phone', 'phone', 'recipient_phone']);
        $shippingAddress = $identitas['address'] ?: '-';

        $sellingPrice = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['harga_jual', 'selling_price'], '0'));
        $netRevenue = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['bruto', 'net_revenue'], (string) $sellingPrice));
        $brand = ImportRowParser::pick($normalizedRow, ['brand', 'merek']);
        $rawCost = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['harga_modal', 'cost_price'], '0'));
        $costPrice = $rawCost > 0 ? $rawCost : BrandCost::resolveCost($brand, $this->brandCostMap);

        if ($rawCost > 0 && $brand !== null && $brand !== '') {
            BrandCost::rememberCost($brand, $rawCost);
            $this->brandCostMap[BrandCost::normalizeBrandKey($brand)] = $rawCost;
        }

        $financial = $this->orderFinancialService->buildPayload($sellingPrice, $netRevenue, $costPrice);

        $paymentMethod = ImportRowParser::pick($normalizedRow, ['metode_bayar', 'payment_method']);
        $courierData = $this->orderFinancialService->parseCourier(
            ImportRowParser::pick($normalizedRow, ['ekspedisi', 'courier', 'shipping_provider'])
        );

        $status = ImportRowParser::normalizeStatus(ImportRowParser::pick($normalizedRow, ['status', 'order_status'], 'pending'));
        $orderedAt = ImportRowParser::parseIndoDate(ImportRowParser::pick($normalizedRow, ['tanggal_pemesanan', 'order_date', 'created_at']));

        $fraudResult = $fraudContext->calculate($this->fraudDetectionService, $buyerPhone, $shippingAddress);

        try {
            DB::transaction(function () use (
                $storeId,
                $platform,
                $orderNumber,
                $buyerName,
                $buyerUsername,
                $buyerPhone,
                $shippingAddress,
                $financial,
                $paymentMethod,
                $courierData,
                $brand,
                $marketplace,
                $status,
                $orderedAt,
                $fraudResult,
                $normalizedRow
            ): void {
                $order = Order::create([
                    'store_id' => $storeId,
                    'platform' => $platform,
                    'order_number' => $orderNumber,
                    'buyer_name' => $buyerName,
                    'buyer_username' => $buyerUsername,
                    'buyer_phone' => $buyerPhone,
                    'shipping_address' => $shippingAddress,
                    ...$financial,
                    'payment_method' => $paymentMethod,
                    'courier' => $courierData['courier'],
                    'tracking_number' => $courierData['tracking_number'],
                    'brand' => $brand,
                    'marketplace_account' => $marketplace['marketplace_account'],
                    'status' => $status,
                    'ordered_at' => $orderedAt ?? now(),
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

                $productName = ImportRowParser::pick($normalizedRow, ['nama_produk', 'product_name', 'item_name', 'product'], 'Imported Product');
                $sku = ImportRowParser::pick($normalizedRow, ['sku', 'seller_sku', 'variation_sku'], $brand ?? 'NO-SKU');
                $qty = (int) ImportRowParser::pick($normalizedRow, ['quantity', 'qty', 'amount'], '1');
                $qty = $qty > 0 ? $qty : 1;
                $unitPrice = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['harga_jual', 'unit_price', 'item_price', 'price'], (string) $financial['selling_price']));
                $subtotal = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['subtotal', 'item_subtotal'], (string) ($unitPrice * $qty)));

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $productName,
                    'sku' => $sku,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal > 0 ? $subtotal : $financial['selling_price'],
                ]);
            });

            $fraudContext->register($orderNumber, $buyerPhone, $shippingAddress);
            $imported++;
        } catch (Throwable $e) {
            $skipped++;
            $firstError ??= 'Terjadi kegagalan saat insert: '.$e->getMessage();
        }
    }

    /**
     * @param  array{platform: string, account_label: ?string, marketplace_account: ?string}  $marketplace
     */
    private function resolveStoreId(string $storeKey, string $platform, array $marketplace): int
    {
        if (isset($this->storeCache[$storeKey])) {
            return $this->storeCache[$storeKey];
        }

        $storeName = $marketplace['account_label']
            ?? ucfirst(str_replace('_', ' ', $platform)).' Store';

        $store = Store::query()->firstOrCreate(
            [
                'platform' => $platform,
                'account_label' => $marketplace['account_label'],
            ],
            [
                'name' => $storeName,
                'is_active' => true,
            ]
        );

        $this->storeCache[$storeKey] = $store->id;

        return $store->id;
    }

    private function logFailure(string $originalFilename, string $errorMessage): void
    {
        ImportLog::create([
            'store_id' => Store::query()->value('id'),
            'platform' => 'shopee',
            'file_name' => $originalFilename,
            'total_rows' => 0,
            'imported_rows' => 0,
            'duplicate_rows' => 0,
            'skipped_rows' => 0,
            'status' => 'failed',
            'error_message' => $errorMessage,
            'imported_at' => now(),
        ]);
    }
}
