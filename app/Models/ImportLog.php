<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'store_id',
        'platform',
        'file_name',
        'total_rows',
        'imported_rows',
        'duplicate_rows',
        'skipped_rows',
        'status',
        'error_message',
        'imported_at',
    ];

    protected function casts(): array
    {
        return [
            'total_rows' => 'integer',
            'imported_rows' => 'integer',
            'duplicate_rows' => 'integer',
            'skipped_rows' => 'integer',
            'imported_at' => 'datetime',
        ];
    }



    /**
     * @return BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
