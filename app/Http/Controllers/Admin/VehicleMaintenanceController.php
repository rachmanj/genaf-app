<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;

class VehicleMaintenanceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = VehicleMaintenance::with('vehicle')->latest();
            return response()->json([
                'data' => $query->get()->map(function (VehicleMaintenance $m) {
                    return [
                        'id' => $m->id,
                        'service_date' => optional($m->service_date)->format('Y-m-d'),
                        'vehicle' => $m->vehicle?->plate_number,
                        'maintenance_type' => $m->maintenance_type,
                        'odometer' => number_format($m->odometer),
                        'cost' => number_format($m->cost, 2),
                        'vendor' => $m->vendor,
                    ];
                })
            ]);
        }
        return view('admin.vehicles.maintenance.index');
    }

    public function create()
    {
        return view('admin.vehicles.maintenance.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record created');
    }

    public function edit($id)
    {
        return view('admin.vehicles.maintenance.edit', ['maintenanceId' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record updated');
    }

    public function destroy($id)
    {
        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record deleted');
    }
}


