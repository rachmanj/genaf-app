<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('view rooms');
        $buildings = Building::orderBy('name')->get();

        $query = Room::query()
            ->with('building')
            ->when($request->filled('building_id'), fn ($q) => $q->where('building_id', (int) $request->input('building_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('room_type'), fn ($q) => $q->where('room_type', $request->string('room_type')))
            ->when($request->filled('floor'), fn ($q) => $q->where('floor', (int) $request->input('floor')))
            ->orderBy('building_id')
            ->orderBy('floor')
            ->orderBy('room_number');

        $rooms = $query->paginate(15)->withQueryString();

        return view('property-management.rooms.index', [
            'rooms' => $rooms,
            'buildings' => $buildings,
            'filters' => [
                'building_id' => $request->input('building_id'),
                'status' => $request->input('status'),
                'room_type' => $request->input('room_type'),
                'floor' => $request->input('floor'),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create rooms');
        $buildings = Building::orderBy('name')->get();
        return view('property-management.rooms.create', compact('buildings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create rooms');
        $data = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'room_type' => ['required', 'string', 'max:100'],
            'floor' => ['required', 'integer'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = Room::where('building_id', $data['building_id'])
            ->where('room_number', $data['room_number'])
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Room number already exists in this building');
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        Room::create($data);

        return redirect()->route('pms.rooms.index')->with('success', 'Room created successfully');
    }

    public function show(Room $room): View
    {
        $this->authorize('view rooms');
        $room->load(['building', 'reservations', 'maintenances']);
        return view('property-management.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $this->authorize('edit rooms');
        $buildings = Building::orderBy('name')->get();
        return view('property-management.rooms.edit', compact('room', 'buildings'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $this->authorize('edit rooms');
        $data = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_number' => ['required', 'string', 'max:50'],
            'room_type' => ['required', 'string', 'max:100'],
            'floor' => ['required', 'integer'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $exists = Room::where('building_id', $data['building_id'])
            ->where('room_number', $data['room_number'])
            ->where('id', '!=', $room->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', 'Room number already exists in this building');
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $room->update($data);

        return redirect()->route('pms.rooms.index')->with('success', 'Room updated successfully');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete rooms');
        $room->delete();
        return redirect()->route('pms.rooms.index')->with('success', 'Room deleted');
    }

    public function byBuilding(Building $building): JsonResponse
    {
        $this->authorize('view rooms');
        $rooms = Room::where('building_id', $building->id)
            ->where('is_active', true)
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'room_type', 'floor', 'status', 'daily_rate']);
        return response()->json($rooms);
    }
}


