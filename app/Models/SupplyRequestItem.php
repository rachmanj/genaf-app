<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplyRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'supply_id',
        'quantity',
        'approved_quantity',
        'fulfilled_quantity',
        'fulfillment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'approved_quantity' => 'integer',
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
     * Get the distributions for this request item
     */
    public function distributions(): HasMany
    {
        return $this->hasMany(SupplyDistribution::class, 'request_item_id');
    }

    /**
     * Get remaining quantity to fulfill (based on approved quantity)
     */
    public function getRemainingQuantity(): int
    {
        return $this->approved_quantity - $this->fulfilled_quantity;
    }

    /**
     * Check if item can be fulfilled
     */
    public function canBeFulfilled(): bool
    {
        return $this->getRemainingQuantity() > 0;
    }

    /**
     * Update fulfillment status based on quantities
     */
    public function updateFulfillmentStatus(): void
    {
        if ($this->fulfilled_quantity == 0) {
            $this->fulfillment_status = 'pending';
        } elseif ($this->fulfilled_quantity >= $this->approved_quantity) {
            $this->fulfillment_status = 'completed';
        } else {
            $this->fulfillment_status = 'partial';
        }
        $this->save();
    }
}
