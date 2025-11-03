<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'employee_id',
        'ticket_type',
        'destination',
        'departure_date',
        'return_date',
        'cost',
        'status',
        'approved_by',
        'approved_at',
        'booking_reference',
        'notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'cost' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the employee that made the reservation
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the user that approved the reservation
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the reservation documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ReservationDocument::class, 'reservation_id');
    }

    /**
     * Check if reservation can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if reservation can be rejected
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if reservation is round trip
     */
    public function isRoundTrip(): bool
    {
        return !is_null($this->return_date);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('21', 'ticket_reservations');
            }
        });
    }
}
