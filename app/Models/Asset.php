<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'purchase_date',
        'purchase_cost',
        'depreciation_rate',
        'current_value',
        'condition',
        'location',
        'assigned_to',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'depreciation_rate' => 'decimal:2',
            'current_value' => 'decimal:2',
        ];
    }

    /**
     * Get the assigned user
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the asset maintenances
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    /**
     * Get the asset transfers
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class);
    }

    /**
     * Check if asset is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Calculate current value based on depreciation
     */
    public function calculateCurrentValue(): float
    {
        $years = $this->purchase_date->diffInYears(now());
        $depreciation = $this->purchase_cost * ($this->depreciation_rate / 100) * $years;
        return max(0, $this->purchase_cost - $depreciation);
    }
}
