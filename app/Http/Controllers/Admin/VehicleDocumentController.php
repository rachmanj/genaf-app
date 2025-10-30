<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleDocument\VehicleDocumentStoreRequest;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class VehicleDocumentController extends Controller
{
    public function store(VehicleDocumentStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('vehicle-documents/' . $data['vehicle_id'], 'public');
            $data['file_path'] = $path;
        }

        VehicleDocument::create($data);

        return back()->with('success', 'Document uploaded');
    }

    public function destroy($id)
    {
        $document = VehicleDocument::findOrFail($id);
        Gate::authorize('delete vehicle documents');
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Document deleted');
    }

    public function download($documentId)
    {
        $document = VehicleDocument::findOrFail($documentId);
        Gate::authorize('download vehicle documents');
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($document->file_path);
    }
}


