<?php

namespace App\Http\Controllers\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuildingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Building::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', 'like', '%' . $request->string('city') . '%'))
            ->withCount('rooms')
            ->orderBy('name');

        $buildings = $query->paginate(15)->withQueryString();

        return view('property-management.buildings.index', [
            'buildings' => $buildings,
            'filters' => [
                'status' => $request->string('status')->toString(),
                'city' => $request->string('city')->toString(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('property-management.buildings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:buildings,code'],
            'name' => ['required', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Building::create($data);

        return redirect()->route('pms.buildings.index')->with('success', 'Building created successfully');
    }

    public function show(Building $building): View
    {
        $building->load(['rooms' => function ($q) {
            $q->orderBy('floor')->orderBy('room_number');
        }]);
        return view('property-management.buildings.show', compact('building'));
    }

    public function edit(Building $building): View
    {
        return view('property-management.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:buildings,code,' . $building->id],
            'name' => ['required', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $building->update($data);

        return redirect()->route('pms.buildings.index')->with('success', 'Building updated successfully');
    }

    public function destroy(Building $building): RedirectResponse
    {
        $building->delete();
        return redirect()->route('pms.buildings.index')->with('success', 'Building deleted');
    }

    public function search(Request $request): JsonResponse
    {
        $term = (string) $request->input('q', '');
        $results = Building::query()
            ->when($term !== '', function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                  ->orWhere('code', 'like', "%$term%")
                  ->orWhere('city', 'like', "%$term%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'text' => $b->name . ' (' . $b->code . ')',
            ]);

        return response()->json([ 'results' => $results ]);
    }
}


