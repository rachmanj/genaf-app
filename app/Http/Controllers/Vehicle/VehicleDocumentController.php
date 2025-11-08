<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleDocument\VehicleDocumentStoreRequest;
use App\Http\Requests\VehicleDocument\VehicleDocumentUpdateRequest;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentController extends Controller
{
    public function store(VehicleDocumentStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('vehicle-documents/' . $data['vehicle_id'], 'public');
            $data['file_path'] = $path;
        }

        unset($data['file']);

        VehicleDocument::create($data);

        return back()->with('success', 'Document uploaded');
    }

    public function update(VehicleDocumentUpdateRequest $request, VehicleDocument $vehicleDocument)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($vehicleDocument->file_path) {
                Storage::disk('public')->delete($vehicleDocument->file_path);
            }

            $data['file_path'] = $request->file('file')->store(
                'vehicle-documents/' . $vehicleDocument->vehicle_id,
                'public'
            );
        }

        unset($data['file'], $data['vehicle_id']);

        $vehicleDocument->fill($data);
        $vehicleDocument->save();

        return back()->with('success', 'Document updated');
    }

    public function destroy(VehicleDocument $vehicleDocument)
    {
        Gate::authorize('delete vehicle documents');
        if ($vehicleDocument->file_path) {
            Storage::disk('public')->delete($vehicleDocument->file_path);
        }
        $vehicleDocument->delete();
        return back()->with('success', 'Document deleted');
    }

    public function download(VehicleDocument $document)
    {
        Gate::authorize('download vehicle documents');
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($document->file_path);
    }
}


