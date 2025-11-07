<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingConsumptionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'consumption_date',
        'consumption_type',
        'requested',
        'description',
        'fulfilled',
        'fulfilled_at',
        'fulfilled_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'consumption_date' => 'date',
            'requested' => 'boolean',
            'fulfilled' => 'boolean',
            'fulfilled_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(MeetingRoomReservation::class, 'reservation_id');
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function getConsumptionTypeLabelAttribute(): string
    {
        return match ($this->consumption_type) {
            'coffee_break_morning' => 'Coffee Break Pagi',
            'coffee_break_afternoon' => 'Coffee Break Sore',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            default => $this->consumption_type,
        };
    }
}
