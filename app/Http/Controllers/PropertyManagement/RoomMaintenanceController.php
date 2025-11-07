<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Room;
use App\Models\RoomMaintenance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomMaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('view room maintenances');
        $buildings = Building::orderBy('name')->get();

        $query = RoomMaintenance::with(['room.building'])
            ->when($request->filled('building_id'), function ($q) use ($request) {
                $q->whereHas('room', fn ($roomQuery) => $roomQuery->where('building_id', (int) $request->input('building_id')));
            })
            ->when($request->filled('room_id'), fn ($q) => $q->where('room_id', (int) $request->input('room_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('maintenance_type'), fn ($q) => $q->where('maintenance_type', 'like', '%' . $request->string('maintenance_type') . '%'))
            ->latest('scheduled_date');

        $maintenances = $query->paginate(15)->withQueryString();

        return view('property-management.room-maintenances.index', [
            'maintenances' => $maintenances,
            'buildings' => $buildings,
            'filters' => [
                'building_id' => $request->input('building_id'),
                'room_id' => $request->input('room_id'),
                'status' => $request->input('status'),
                'maintenance_type' => $request->input('maintenance_type'),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create room maintenances');
        $buildings = Building::orderBy('name')->get();
        return view('property-management.room-maintenances.create', compact('buildings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create room maintenances');
        $data = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'maintenance_type' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'completed_date' => ['nullable', 'date', 'after_or_equal:scheduled_date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $room = Room::where('id', $data['room_id'])->where('building_id', $data['building_id'])->firstOrFail();

        RoomMaintenance::create([
            'room_id' => $room->id,
            'maintenance_type' => $data['maintenance_type'],
            'scheduled_date' => $data['scheduled_date'],
            'completed_date' => $data['completed_date'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('pms.maintenances.index')->with('success', 'Maintenance scheduled successfully');
    }

    public function show(RoomMaintenance $maintenance): View
    {
        $this->authorize('view room maintenances');
        $maintenance->load(['room.building']);
        return view('property-management.room-maintenances.show', compact('maintenance'));
    }

    public function edit(RoomMaintenance $maintenance): View
    {
        $this->authorize('edit room maintenances');
        $buildings = Building::orderBy('name')->get();
        $maintenance->load('room.building');
        return view('property-management.room-maintenances.edit', compact('maintenance', 'buildings'));
    }

    public function update(Request $request, RoomMaintenance $maintenance): RedirectResponse
    {
        $this->authorize('edit room maintenances');
        $data = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'maintenance_type' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'completed_date' => ['nullable', 'date', 'after_or_equal:scheduled_date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
        ]);

        $room = Room::where('id', $data['room_id'])->where('building_id', $data['building_id'])->firstOrFail();

        $maintenance->update([
            'room_id' => $room->id,
            'maintenance_type' => $data['maintenance_type'],
            'scheduled_date' => $data['scheduled_date'],
            'completed_date' => $data['completed_date'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('pms.maintenances.index')->with('success', 'Maintenance updated successfully');
    }

    public function destroy(RoomMaintenance $maintenance): RedirectResponse
    {
        $this->authorize('delete room maintenances');
        $maintenance->delete();
        return redirect()->route('pms.maintenances.index')->with('success', 'Maintenance deleted successfully');
    }

    public function markComplete(Request $request, RoomMaintenance $maintenance): RedirectResponse
    {
        $this->authorize('complete room maintenances');
        if ($maintenance->status === 'completed') {
            return back()->with('error', 'Maintenance is already completed');
        }

        $data = $request->validate([
            'completed_date' => ['nullable', 'date', 'after_or_equal:scheduled_date'],
        ]);

        $maintenance->update([
            'status' => 'completed',
            'completed_date' => $data['completed_date'] ?? now()->toDateString(),
        ]);

        return back()->with('success', 'Maintenance marked as completed');
    }
}

