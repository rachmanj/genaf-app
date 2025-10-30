<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelRecordController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = FuelRecord::with('vehicle')->latest();
            return response()->json([
                'data' => $query->get()->map(function (FuelRecord $r) {
                    return [
                        'id' => $r->id,
                        'date' => optional($r->date)->format('Y-m-d'),
                        'vehicle' => $r->vehicle?->plate_number,
                        'odometer' => number_format($r->odometer),
                        'liters' => number_format($r->liters, 2),
                        'cost' => number_format($r->cost, 2),
                        'gas_station' => $r->gas_station,
                    ];
                })
            ]);
        }
        return view('admin.vehicles.fuel-records.index');
    }

    public function create()
    {
        return view('admin.vehicles.fuel-records.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('fuel-records.index')->with('success', 'Fuel record created');
    }

    public function edit($id)
    {
        return view('admin.vehicles.fuel-records.edit', ['fuelRecordId' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('fuel-records.index')->with('success', 'Fuel record updated');
    }

    public function destroy($id)
    {
        return redirect()->route('fuel-records.index')->with('success', 'Fuel record deleted');
    }
}


