<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandCost extends Model
{
    protected $fillable = [
        'brand',
        'cost_price',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
        ];
    }

    public static function normalizeBrandKey(string $brand): string
    {
        return strtolower(preg_replace('/[^a-z0-9]/', '', $brand) ?? '');
    }

    /**
     * @return array<string, float>
     */
    public static function costMap(): array
    {
        $map = [];

        foreach (self::query()->get() as $record) {
            $map[self::normalizeBrandKey($record->brand)] = (float) $record->cost_price;
        }

        return $map;
    }

    public static function resolveCost(?string $brand, ?array $costMap = null): ?float
    {
        if ($brand === null || trim($brand) === '') {
            return null;
        }

        $key = self::normalizeBrandKey($brand);

        if ($costMap !== null) {
            return $costMap[$key] ?? null;
        }

        foreach (self::query()->get() as $record) {
            if (self::normalizeBrandKey($record->brand) === $key) {
                return (float) $record->cost_price;
            }
        }

        return null;
    }

    public static function rememberCost(string $brand, float $costPrice): void
    {
        if ($costPrice <= 0) {
            return;
        }

        self::query()->updateOrCreate(
            ['brand' => trim($brand)],
            ['cost_price' => $costPrice]
        );
    }
}
