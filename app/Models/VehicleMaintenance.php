<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'service_date',
        'odometer',
        'cost',
        'vendor',
        'next_service_date',
        'next_service_odometer',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'next_service_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->where(function ($q) use ($days) {
            $q->whereNotNull('next_service_date')
                ->where('next_service_date', '<=', now()->addDays($days));
        });
    }
}


