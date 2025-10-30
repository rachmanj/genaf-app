<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Vehicle::query();
            return response()->json([
                'data' => $query->latest()->get()->map(function (Vehicle $v) {
                    return [
                        'id' => $v->id,
                        'plate_number' => $v->plate_number,
                        'brand' => $v->brand,
                        'model' => $v->model,
                        'type' => $v->type,
                        'year' => $v->year,
                        'status' => ucfirst($v->status),
                        'actions' => view('admin.vehicles.partials.actions', compact('v'))->render(),
                    ];
                }),
            ]);
        }
        return view('admin.vehicles.index');
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('vehicles.index')->with('success', 'Vehicle created');
    }

    public function show($id)
    {
        return view('admin.vehicles.show', ['vehicleId' => $id]);
    }

    public function edit($id)
    {
        return view('admin.vehicles.edit', ['vehicleId' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated');
    }

    public function destroy($id)
    {
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted');
    }
}


