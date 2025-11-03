<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'room_id',
        'guest_name',
        'company',
        'phone',
        'email',
        'check_in',
        'check_out',
        'status',
        'total_cost',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'total_cost' => 'decimal:2',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('31', 'room_reservations');
            }
        });
    }
}
