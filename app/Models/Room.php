<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type',
        'floor',
        'capacity',
        'status',
        'daily_rate',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'capacity' => 'integer',
            'daily_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the room reservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(RoomReservation::class);
    }

    /**
     * Get the room maintenances
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(RoomMaintenance::class);
    }

    /**
     * Check if room is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Get current reservation
     */
    public function getCurrentReservationAttribute()
    {
        return $this->reservations()
            ->where('status', 'checked_in')
            ->first();
    }
}
