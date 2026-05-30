<?php

namespace App\Console\Commands;

use App\Models\BrandCost;
use App\Models\Order;
use App\Services\OrderFinancialService;
use App\Support\ImportRowParser;
use Illuminate\Console\Command;
use Spatie\SimpleExcel\SimpleExcelReader;

class RepairOrdersFromExcel extends Command
{
    protected $signature = 'orders:repair-from-excel
                            {file? : Path ke file Excel/CSV (default: resources/Database Penjualan.xlsx)}';

    protected $description = 'Perbaiki tanggal, status, dan angka keuangan order dari file Excel master';

    public function handle(OrderFinancialService $orderFinancialService): int
    {
        $file = $this->argument('file') ?? resource_path('Database Penjualan.xlsx');

        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $brandCostMap = BrandCost::costMap();
        $updated = 0;
        $notFound = 0;
        $skipped = 0;

        foreach (SimpleExcelReader::create($file)->getSheetNames() as $sheetName) {
            $this->info("Memproses sheet: {$sheetName}");

            foreach (SimpleExcelReader::create($file)->fromSheetName($sheetName)->getRows() as $row) {
                $normalizedRow = ImportRowParser::normalizeRow($row);
                $orderNumber = ImportRowParser::pick($normalizedRow, ['id_pesanan', 'order_id', 'order_no', 'order_number', 'order_sn']);

                if ($orderNumber === null || $orderNumber === '') {
                    $skipped++;

                    continue;
                }

                $order = Order::query()->where('order_number', $orderNumber)->first();

                if ($order === null) {
                    $notFound++;

                    continue;
                }

                $sellingPrice = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['harga_jual', 'selling_price'], '0'));
                $netRevenue = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['bruto', 'net_revenue'], (string) $sellingPrice));
                $brand = ImportRowParser::pick($normalizedRow, ['brand', 'merek']);
                $rawCost = ImportRowParser::parseMoney(ImportRowParser::pick($normalizedRow, ['harga_modal', 'cost_price'], '0'));
                $costPrice = $rawCost > 0 ? $rawCost : BrandCost::resolveCost($brand, $brandCostMap);

                if ($rawCost > 0 && $brand !== null && $brand !== '') {
                    BrandCost::rememberCost($brand, $rawCost);
                    $brandCostMap[BrandCost::normalizeBrandKey($brand)] = $rawCost;
                }

                $orderedAt = ImportRowParser::parseIndoDate(
                    ImportRowParser::pick($normalizedRow, ['tanggal_pemesanan', 'order_date', 'created_at'])
                );

                if ($orderedAt === null) {
                    $this->warn("Tanggal tidak terbaca untuk order {$orderNumber}, dilewati.");

                    continue;
                }

                $status = ImportRowParser::normalizeStatus(
                    ImportRowParser::pick($normalizedRow, ['status', 'order_status'], 'pending')
                );

                $order->update([
                    ...$orderFinancialService->buildPayload($sellingPrice, $netRevenue, $costPrice),
                    'brand' => $brand,
                    'status' => $status,
                    'ordered_at' => $orderedAt,
                ]);

                $updated++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Diperbarui', $updated],
                ['Tidak ada di database', $notFound],
                ['Baris tanpa ID / tanggal', $skipped],
            ]
        );

        return self::SUCCESS;
    }
}
