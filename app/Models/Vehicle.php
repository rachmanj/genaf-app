<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'year',
        'type',
        'current_odometer',
        'status',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'current_odometer' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the fuel records
     */
    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }

    /**
     * Get the vehicle documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    /**
     * Get the vehicle maintenances
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    /**
     * Check if vehicle is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->is_active;
    }

    /**
     * Get expiring documents (within 3 months)
     */
    public function getExpiringDocumentsAttribute()
    {
        return $this->documents()
            ->where('expiry_date', '<=', now()->addMonths(3))
            ->where('expiry_date', '>', now())
            ->get();
    }

    public function lastOdometer(): int
    {
        $fromVehicle = (int) $this->current_odometer;
        $fromFuel = (int) optional($this->fuelRecords()->orderByDesc('date')->orderByDesc('id')->first())->odometer;
        $fromMaintenance = (int) optional($this->maintenances()->orderByDesc('service_date')->orderByDesc('id')->first())->odometer;
        return max($fromVehicle, $fromFuel, $fromMaintenance);
    }
}
