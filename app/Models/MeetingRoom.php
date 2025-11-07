<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'facilities',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(MeetingRoomReservation::class, 'allocated_room_id');
    }

    public function requestedReservations(): HasMany
    {
        return $this->hasMany(MeetingRoomReservation::class, 'requested_room_id');
    }

    public function isAvailableForMeeting(string $dateStart, string $dateEnd, string $timeStart, string $timeEnd, ?int $excludeReservationId = null): bool
    {
        $query = $this->reservations()
            ->whereIn('status', ['pending_dept_head', 'pending_ga_admin', 'approved', 'confirmed'])
            ->where(function ($q) use ($dateStart, $dateEnd, $timeStart, $timeEnd) {
                $q->where(function ($dateQuery) use ($dateStart, $dateEnd) {
                    $dateQuery->whereBetween('meeting_date_start', [$dateStart, $dateEnd])
                        ->orWhereBetween('meeting_date_end', [$dateStart, $dateEnd])
                        ->orWhere(function ($overlapQuery) use ($dateStart, $dateEnd) {
                            $overlapQuery->where('meeting_date_start', '<=', $dateStart)
                                ->where(function ($endQuery) use ($dateEnd) {
                                    $endQuery->whereNull('meeting_date_end')
                                        ->orWhere('meeting_date_end', '>=', $dateEnd);
                                });
                        });
                });
            })
            ->where(function ($timeQuery) use ($timeStart, $timeEnd) {
                $timeQuery->where(function ($t) use ($timeStart, $timeEnd) {
                    $t->where('meeting_time_start', '<', $timeEnd)
                        ->where('meeting_time_end', '>', $timeStart);
                });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return !$query->exists();
    }
}
