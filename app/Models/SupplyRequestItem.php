<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'supply_id',
        'quantity',
        'fulfilled_quantity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'fulfilled_quantity' => 'integer',
        ];
    }

    /**
     * Get the request that owns the item
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class, 'request_id');
    }

    /**
     * Get the supply
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Check if item is fully fulfilled
     */
    public function isFullyFulfilled(): bool
    {
        return $this->fulfilled_quantity >= $this->quantity;
    }

    /**
     * Get remaining quantity to fulfill
     */
    public function getRemainingQuantityAttribute(): int
    {
        return $this->quantity - $this->fulfilled_quantity;
    }
}
