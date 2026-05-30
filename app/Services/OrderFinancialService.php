<?php

namespace App\Services;

class OrderFinancialService
{
    /**
     * @return array{
     *     selling_price: float,
     *     net_revenue: float,
     *     total_amount: float,
     *     cost_price: float|null,
     *     marketplace_fee: float|null,
     *     gross_profit: float|null,
     *     margin_percent: float|null
     * }
     */
    public function buildPayload(float $sellingPrice, float $netRevenue, ?float $costPrice = null): array
    {
        if ($sellingPrice <= 0 && $netRevenue > 0) {
            $sellingPrice = $netRevenue;
        }

        if ($netRevenue <= 0 && $sellingPrice > 0) {
            $netRevenue = $sellingPrice;
        }

        $marketplaceFee = max($sellingPrice - $netRevenue, 0);
        $cost = $costPrice !== null && $costPrice > 0 ? $costPrice : null;
        $grossProfit = ($cost !== null && $netRevenue > 0) ? ($netRevenue - $cost) : null;
        $marginPercent = ($grossProfit !== null && $netRevenue > 0)
            ? round(($grossProfit / $netRevenue) * 100, 2)
            : null;

        return [
            'selling_price' => round($sellingPrice, 2),
            'net_revenue' => round($netRevenue, 2),
            'total_amount' => round($sellingPrice, 2),
            'cost_price' => $cost !== null ? round($cost, 2) : null,
            'marketplace_fee' => $marketplaceFee > 0 ? round($marketplaceFee, 2) : null,
            'gross_profit' => $grossProfit !== null ? round($grossProfit, 2) : null,
            'margin_percent' => $marginPercent,
        ];
    }

    /**
     * @return array{courier: string|null, tracking_number: string|null}
     */
    public function parseCourier(?string $raw): array
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return ['courier' => null, 'tracking_number' => null];
        }

        if (preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $raw, $matches)) {
            return [
                'courier' => trim($matches[1]),
                'tracking_number' => trim($matches[2]),
            ];
        }

        return ['courier' => $raw, 'tracking_number' => null];
    }

    /**
     * @return array{platform: string, account_label: string|null, marketplace_account: string|null}
     */
    public function parseMarketplaceAccount(?string $raw): array
    {
        $raw = trim((string) $raw);
        $lower = strtolower($raw);

        $platform = match (true) {
            str_contains($lower, 'tiktok') => 'tiktok_shop',
            str_contains($lower, 'shopee') => 'shopee',
            default => 'shopee',
        };

        $accountLabel = null;

        if (preg_match('/\(([^)]+)\)/', $raw, $matches)) {
            $accountLabel = trim($matches[1]);
        } elseif ($platform === 'shopee' && $raw !== '') {
            $accountLabel = 'Shopee';
        }

        return [
            'platform' => $platform,
            'account_label' => $accountLabel,
            'marketplace_account' => $raw !== '' ? $raw : null,
        ];
    }
}
