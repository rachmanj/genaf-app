<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleDocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                        'unit_no' => $v->unit_no,
                        'nomor_polisi' => $v->nomor_polisi,
                        'brand' => $v->brand,
                        'model' => $v->model,
                        'plant_group' => $v->plant_group,
                        'current_project_code' => $v->current_project_code,
                        'year' => $v->year,
                        'status' => ucfirst($v->status),
                        'arkfleet_sync_status' => $v->arkfleet_sync_status,
                        'arkfleet_sync_status_label' => Str::headline($v->arkfleet_sync_status ?? 'never'),
                        'arkfleet_sync_message' => $v->arkfleet_sync_message,
                        'arkfleet_synced_at' => optional($v->arkfleet_synced_at)?->toIso8601String(),
                        'arkfleet_synced_at_human' => optional($v->arkfleet_synced_at)?->diffForHumans(),
                        'actions' => view('vehicle.vehicles.partials.actions', compact('v'))->render(),
                    ];
                }),
            ]);
        }
        return view('vehicle.vehicles.index');
    }

    public function create()
    {
        return view('vehicle.vehicles.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('vehicles.index')->with('success', 'Vehicle created');
    }

    public function show($id)
    {
        $vehicle = Vehicle::query()
            ->with([
                'documents' => function ($query) {
                    $query->orderByRaw('due_date IS NULL')->orderBy('due_date')->orderByDesc('created_at');
                },
                'documents.type',
                'documents.revisions' => function ($query) {
                    $query->orderByDesc('created_at');
                },
                'documents.revisions.changedBy',
            ])
            ->findOrFail($id);

        $documentTypes = VehicleDocumentType::query()->orderBy('name')->get();

        $documentsForView = $vehicle->documents->mapWithKeys(function ($document) {
            $dueDate = $document->due_date;
            $documentDate = $document->document_date;
            $now = now()->startOfDay();
            $daysRemaining = null;
            $statusLabel = 'No due date';
            $statusClass = 'badge-secondary';

            if ($dueDate !== null) {
                $daysRemaining = $now->diffInDays($dueDate, false);

                if ($daysRemaining < 0) {
                    $statusLabel = 'Expired ' . abs($daysRemaining) . ' day' . (abs($daysRemaining) === 1 ? '' : 's') . ' ago';
                    $statusClass = 'badge-danger';
                } elseif ($daysRemaining <= 30) {
                    $statusLabel = 'Due in ' . $daysRemaining . ' day' . ($daysRemaining === 1 ? '' : 's');
                    $statusClass = 'badge-warning';
                } else {
                    $statusLabel = 'Due in ' . $daysRemaining . ' day' . ($daysRemaining === 1 ? '' : 's');
                    $statusClass = 'badge-success';
                }
            }

            $revisions = $document->revisions->map(function ($revision) {
                return [
                    'document_number' => $revision->document_number,
                    'document_date' => optional($revision->document_date)?->toDateString(),
                    'due_date' => optional($revision->due_date)?->toDateString(),
                    'supplier' => $revision->supplier,
                    'amount' => $revision->amount,
                    'amount_formatted' => $revision->amount !== null ? number_format($revision->amount, 0, '.', ',') : null,
                    'notes' => $revision->notes,
                    'file_url' => $revision->file_path ? Storage::disk('public')->url($revision->file_path) : null,
                    'changed_by' => optional($revision->changedBy)->name,
                    'changed_at' => optional($revision->created_at)?->format('d M Y H:i'),
                ];
            })->toArray();

            return [
                $document->id => [
                    'id' => $document->id,
                    'vehicle_document_type_id' => $document->vehicle_document_type_id,
                    'type_name' => optional($document->type)->name,
                    'document_number' => $document->document_number,
                    'document_date' => optional($documentDate)?->toDateString(),
                    'due_date' => optional($dueDate)?->toDateString(),
                    'supplier' => $document->supplier,
                    'amount' => $document->amount,
                    'amount_formatted' => $document->amount !== null ? number_format($document->amount, 0, '.', ',') : null,
                    'notes' => $document->notes,
                    'file_path' => $document->file_path,
                    'file_url' => $document->file_path ? Storage::disk('public')->url($document->file_path) : null,
                    'status_label' => $statusLabel,
                    'status_class' => $statusClass,
                    'days_remaining' => $daysRemaining,
                    'revisions' => $revisions,
                ],
            ];
        })->toArray();

        return view('vehicle.vehicles.show', [
            'vehicle' => $vehicle,
            'documentTypes' => $documentTypes,
            'documentsForView' => $documentsForView,
        ]);
    }

    public function edit($id)
    {
        return view('vehicle.vehicles.edit', ['vehicleId' => $id]);
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


