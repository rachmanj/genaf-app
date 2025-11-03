<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
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
        $reservations = RoomReservation::with(['room.building'])
            ->latest('check_in')
            ->paginate(15)
            ->withQueryString();

        return view('property-management.room-reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        $buildings = Building::orderBy('name')->get();
        return view('property-management.room-reservations.create', compact('buildings'));
    }

    public function store(Request $request): RedirectResponse
    {
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

    private function isRoomAvailable(int $roomId, string $checkIn, string $checkOut): bool
    {
        return !RoomReservation::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->exists();
    }
}


