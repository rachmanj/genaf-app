<?php

namespace App\Models;

use App\Services\FormNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingRoomReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_number',
        'requestor_id',
        'department_id',
        'requested_room_id',
        'allocated_room_id',
        'location',
        'meeting_title',
        'meeting_date_start',
        'meeting_date_end',
        'meeting_time_start',
        'meeting_time_end',
        'participant_count',
        'required_facilities',
        'status',
        'department_head_approved_by',
        'department_head_approved_at',
        'department_head_rejection_reason',
        'ga_admin_approved_by',
        'ga_admin_approved_at',
        'ga_admin_rejection_reason',
        'room_allocated_at',
        'response_sent_at',
        'response_notes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date_start' => 'date',
            'meeting_date_end' => 'date',
            'participant_count' => 'integer',
            'department_head_approved_at' => 'datetime',
            'ga_admin_approved_at' => 'datetime',
            'room_allocated_at' => 'datetime',
            'response_sent_at' => 'datetime',
        ];
    }

    public function requestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requestor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function requestedRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'requested_room_id');
    }

    public function allocatedRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'allocated_room_id');
    }

    public function departmentHeadApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head_approved_by');
    }

    public function gaAdminApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ga_admin_approved_by');
    }

    public function consumptionRequests(): HasMany
    {
        return $this->hasMany(MeetingConsumptionRequest::class, 'reservation_id');
    }

    public function canBeDeptHeadApproved(): bool
    {
        return $this->status === 'pending_dept_head';
    }

    public function canBeGAAdminApproved(): bool
    {
        return $this->status === 'pending_ga_admin';
    }

    public function canBeDeptHeadRejected(): bool
    {
        return $this->status === 'pending_dept_head';
    }

    public function canBeGAAdminRejected(): bool
    {
        return $this->status === 'pending_ga_admin';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending_dept_head';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_dept_head', 'pending_ga_admin']);
    }

    public function isMultiDay(): bool
    {
        if (!$this->meeting_date_end) {
            return false;
        }
        
        $start = \Carbon\Carbon::parse($this->meeting_date_start);
        $end = \Carbon\Carbon::parse($this->meeting_date_end);
        
        return !$start->isSameDay($end);
    }

    public function getMeetingDates(): array
    {
        $start = \Carbon\Carbon::parse($this->meeting_date_start);
        
        if (!$this->isMultiDay()) {
            return [$start->format('Y-m-d')];
        }

        $dates = [];
        $current = $start->copy();
        $end = \Carbon\Carbon::parse($this->meeting_date_end);

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $model->form_number = FormNumberService::generateFormNumber('33', 'meeting_room_reservations');
            }
        });
    }
}
