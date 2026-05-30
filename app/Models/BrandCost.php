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

    /**
     * @return array<string, float>
     */
    public static function costMap(): array
    {
        return self::query()
            ->get()
            ->mapWithKeys(fn (self $record) => [strtolower(trim($record->brand)) => (float) $record->cost_price])
            ->all();
    }

    public static function resolveCost(?string $brand, ?array $costMap = null): ?float
    {
        if ($brand === null || trim($brand) === '') {
            return null;
        }

        $key = strtolower(trim($brand));

        if ($costMap !== null) {
            return $costMap[$key] ?? null;
        }

        $cost = self::query()
            ->whereRaw('LOWER(brand) = ?', [$key])
            ->value('cost_price');

        return $cost !== null ? (float) $cost : null;
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
