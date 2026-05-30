<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    protected $fillable = [
        'store_id',
        'order_number',
        'buyer_name',
        'buyer_username',
        'buyer_phone',
        'shipping_address',
        'total_amount',
        'selling_price',
        'net_revenue',
        'cost_price',
        'marketplace_fee',
        'gross_profit',
        'margin_percent',
        'payment_method',
        'courier',
        'tracking_number',
        'brand',
        'marketplace_account',
        'platform',
        'status',
        'fraud_score',
        'fraud_status',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'net_revenue' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'marketplace_fee' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'margin_percent' => 'decimal:2',
            'fraud_score' => 'integer',
            'ordered_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<FraudLog, $this>
     */
    public function fraudLogs(): HasMany
    {
        return $this->hasMany(FraudLog::class);
    }

    /**
     * @param  Builder<Order>  $query
     */
    public function scopeApplyListFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('platform'), fn (Builder $q) => $q->where('platform', $request->string('platform')))
            ->when($request->filled('fraud_status'), fn (Builder $q) => $q->where('fraud_status', $request->string('fraud_status')))
            ->when($request->filled('from'), fn (Builder $q) => $q->whereDate('ordered_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn (Builder $q) => $q->whereDate('ordered_at', '<=', $request->date('to')))
            ->when($request->filled('q'), function (Builder $q) use ($request): void {
                $keyword = '%'.$request->string('q').'%';

                $q->where(function (Builder $inner) use ($keyword): void {
                    $inner->where('order_number', 'like', $keyword)
                        ->orWhere('buyer_name', 'like', $keyword)
                        ->orWhere('buyer_phone', 'like', $keyword);
                });
            });
    }
}
