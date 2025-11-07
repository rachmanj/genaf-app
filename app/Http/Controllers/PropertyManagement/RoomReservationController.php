<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class RoomReservationController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('view room reservations');
        $reservations = RoomReservation::with(['room.building'])
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        return view('property-management.room-reservations.index', compact('reservations'));
    }

    public function calendar(Request $request): View
    {
        $this->authorize('view room reservations');
        $buildings = Building::orderBy('name')->get();
        $selectedBuildingId = $request->integer('building_id') ?? optional($buildings->first())->id;

        $statusOptions = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
        ];

        return view('property-management.calendar.index', [
            'buildings' => $buildings,
            'selectedBuildingId' => $selectedBuildingId,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create room reservations');
        $buildings = Building::orderBy('name')->get();
        return view('property-management.room-reservations.create', compact('buildings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create room reservations');
        $data = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'notes' => ['nullable', 'string'],
        ]);

        // Ensure room belongs to building
        $room = Room::where('id', $data['room_id'])->where('building_id', $data['building_id'])->firstOrFail();

        if (!$this->isRoomAvailable($room->id, $data['check_in'], $data['check_out'])) {
            return back()->withInput()->with('error', 'Room is not available for the selected dates');
        }

        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => $data['guest_name'],
            'company' => $data['company'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'status' => 'pending',
            'total_cost' => 0,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('pms.reservations.index')->with('success', 'Reservation created');
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $available = $this->isRoomAvailable((int) $data['room_id'], $data['check_in'], $data['check_out']);
        return response()->json(['available' => $available]);
    }

    public function getUnavailableDates(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
        ]);

        $roomId = (int) $data['room_id'];
        $unavailableDates = [];

        // Get reservation dates
        $reservations = RoomReservation::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->get();

        foreach ($reservations as $reservation) {
            $start = Carbon::parse($reservation->check_in);
            $end = Carbon::parse($reservation->check_out);
            while ($start->lt($end)) {
                $unavailableDates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        // Get maintenance dates
        $maintenances = RoomMaintenance::where('room_id', $roomId)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->get();

        foreach ($maintenances as $maintenance) {
            $start = Carbon::parse($maintenance->scheduled_date);
            $end = $maintenance->completed_date 
                ? Carbon::parse($maintenance->completed_date) 
                : $start->copy()->addDays(30); // If not completed, assume 30 days

            while ($start->lte($end)) {
                $dateStr = $start->format('Y-m-d');
                if (!in_array($dateStr, $unavailableDates)) {
                    $unavailableDates[] = $dateStr;
                }
                $start->addDay();
            }
        }

        return response()->json(['unavailable_dates' => array_unique($unavailableDates)]);
    }

    public function events(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'status' => ['nullable', 'array'],
            'status.*' => ['in:pending,confirmed,checked_in,checked_out,cancelled'],
        ]);

        $start = Carbon::parse($validated['start'])->startOfDay();
        $end = Carbon::parse($validated['end'])->subDay()->endOfDay();
        if ($end->lt($start)) {
            $end = Carbon::parse($validated['end'])->endOfDay();
        }

        $building = isset($validated['building_id']) ? Building::find($validated['building_id']) : null;
        $statuses = array_filter($validated['status'] ?? []);

        $statusColors = [
            'pending' => ['#6c757d', '#6c757d'],
            'confirmed' => ['#007bff', '#007bff'],
            'checked_in' => ['#28a745', '#28a745'],
            'checked_out' => ['#17a2b8', '#17a2b8'],
            'cancelled' => ['#dc3545', '#dc3545'],
        ];

        $events = [];

        $reservations = RoomReservation::with(['room.building'])
            ->when($building, function ($query) use ($building) {
                $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('building_id', $building->id));
            })
            ->when($statuses, fn ($query) => $query->whereIn('status', $statuses))
            ->where(function ($query) use ($start, $end) {
                $query->where('check_in', '<=', $end->toDateString())
                    ->where('check_out', '>=', $start->toDateString());
            })
            ->get();

        foreach ($reservations as $reservation) {
            $colorPair = $statusColors[$reservation->status] ?? ['#343a40', '#343a40'];
            $checkIn = Carbon::parse($reservation->check_in);
            $checkOut = Carbon::parse($reservation->check_out)->addDay(); // make end exclusive

            $events[] = [
                'id' => 'reservation-' . $reservation->id,
                'title' => $reservation->room?->room_number . ' • ' . $reservation->guest_name,
                'start' => $checkIn->toDateString(),
                'end' => $checkOut->toDateString(),
                'allDay' => true,
                'backgroundColor' => $colorPair[0],
                'borderColor' => $colorPair[1],
                'textColor' => '#ffffff',
                'classNames' => ['pms-event-reservation', 'status-' . $reservation->status],
                'extendedProps' => [
                    'type' => 'reservation',
                    'reservationId' => $reservation->id,
                    'roomNumber' => $reservation->room?->room_number,
                    'roomType' => $reservation->room?->room_type,
                    'guestName' => $reservation->guest_name,
                    'status' => $reservation->status,
                    'checkIn' => $checkIn->toDateString(),
                    'checkOut' => $reservation->check_out,
                    'building' => $reservation->room?->building?->name,
                ],
            ];
        }

        $maintenanceEvents = RoomMaintenance::with(['room.building'])
            ->when($building, function ($query) use ($building) {
                $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('building_id', $building->id));
            })
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('scheduled_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('scheduled_date', '<=', $end->toDateString())
                            ->where(function ($inner) use ($start) {
                                $inner->whereNull('completed_date')
                                    ->orWhere('completed_date', '>=', $start->toDateString());
                            });
                    });
            })
            ->get();

        foreach ($maintenanceEvents as $maintenance) {
            $scheduled = Carbon::parse($maintenance->scheduled_date);
            $completed = $maintenance->completed_date ? Carbon::parse($maintenance->completed_date)->addDay() : $scheduled->copy()->addDay();

            $events[] = [
                'id' => 'maintenance-' . $maintenance->id,
                'title' => 'Maintenance • ' . ($maintenance->room?->room_number ?? 'Room') . ' (' . $maintenance->maintenance_type . ')',
                'start' => $scheduled->toDateString(),
                'end' => $completed->toDateString(),
                'allDay' => true,
                'display' => 'background',
                'backgroundColor' => 'rgba(255,193,7,0.35)',
                'borderColor' => '#ffc107',
                'classNames' => ['pms-event-maintenance'],
                'extendedProps' => [
                    'type' => 'maintenance',
                    'maintenanceId' => $maintenance->id,
                    'roomNumber' => $maintenance->room?->room_number,
                    'maintenanceType' => $maintenance->maintenance_type,
                    'scheduledDate' => $scheduled->toDateString(),
                    'completedDate' => $maintenance->completed_date,
                ],
            ];
        }

        return response()->json($events);
    }

    public function show(RoomReservation $reservation): View
    {
        $this->authorize('view room reservations');
        $reservation->load(['room.building', 'creator', 'approver', 'canceller']);
        return view('property-management.room-reservations.show', compact('reservation'));
    }

    public function approve(Request $request, RoomReservation $reservation): RedirectResponse
    {
        $this->authorize('approve room reservations');
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Only pending reservations can be approved');
        }

        $reservation->update([
            'status' => 'confirmed',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Reservation approved successfully');
    }

    public function checkIn(Request $request, RoomReservation $reservation): RedirectResponse
    {
        $this->authorize('check in room reservations');
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed reservations can be checked in');
        }

        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
            'approved_by' => $reservation->approved_by ?? $request->user()->id,
            'approved_at' => $reservation->approved_at ?? now(),
        ]);

        $reservation->room->update(['status' => 'occupied']);

        return back()->with('success', 'Guest checked in successfully');
    }

    public function checkOut(Request $request, RoomReservation $reservation): RedirectResponse
    {
        $this->authorize('check out room reservations');
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out');
        }

        $reservation->update([
            'status' => 'checked_out',
            'checked_out_at' => now(),
        ]);

        $reservation->room->update(['status' => 'available']);

        return back()->with('success', 'Guest checked out successfully');
    }

    public function cancel(Request $request, RoomReservation $reservation): RedirectResponse
    {
        $this->authorize('cancel room reservations');
        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel a reservation that is already checked out or cancelled');
        }

        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $wasCheckedIn = $reservation->status === 'checked_in';

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_by' => $request->user()->id,
            'cancelled_at' => now(),
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ]);

        if ($wasCheckedIn) {
            $reservation->room->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation cancelled successfully');
    }

    public function getGuestInfo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guest_name' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
        ]);

        $lastReservation = RoomReservation::where('guest_name', $data['guest_name'])
            ->when($data['phone'] ?? null, fn ($q, $phone) => $q->where('phone', $phone))
            ->latest('created_at')
            ->first(['guest_name', 'company', 'phone', 'email']);

        if ($lastReservation) {
            return response()->json([
                'company' => $lastReservation->company,
                'phone' => $lastReservation->phone,
                'email' => $lastReservation->email,
            ]);
        }

        return response()->json(['company' => null, 'phone' => null, 'email' => null]);
    }

    private function isRoomAvailable(int $roomId, string $checkIn, string $checkOut): bool
    {
        // Check for conflicting reservations
        $hasReservation = RoomReservation::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->exists();

        if ($hasReservation) {
            return false;
        }

        // Check for maintenance windows that block reservations
        $hasMaintenance = RoomMaintenance::where('room_id', $roomId)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where(function ($query) use ($checkIn, $checkOut) {
                    // Maintenance starts before or during the requested period
                    $query->where('scheduled_date', '<=', $checkOut)
                        ->where(function ($inner) use ($checkIn) {
                            // Maintenance ends after the requested period starts, or hasn't been completed
                            $inner->whereNull('completed_date')
                                ->orWhere('completed_date', '>=', $checkIn);
                        });
                });
            })
            ->exists();

        return !$hasMaintenance;
    }
}


