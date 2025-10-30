<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\SupplyDistribution;
use App\Models\SupplyTransaction;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplyFulfillmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = SupplyRequest::with(['department', 'employee', 'items.supply'])
                ->where('status', 'approved')
                ->select('supply_requests.*');

            return DataTables::of($requests)
                ->addColumn('index', function ($request) {
                    return '';
                })
                ->addColumn('request_id', function ($request) {
                    return $request->id;
                })
                ->addColumn('items_count', function ($request) {
                    return $request->items->count();
                })
                ->addColumn('status_badge', function ($request) {
                    return '<span class="badge badge-success">Approved</span>';
                })
                ->addColumn('department_name', function ($request) {
                    return $request->department->department_name ?? 'N/A';
                })
                ->addColumn('employee_name', function ($request) {
                    return $request->employee->name ?? 'N/A';
                })
                ->addColumn('request_date', function ($request) {
                    return $request->request_date->format('M d, Y');
                })
                ->addColumn('items_summary', function ($request) {
                    $totalItems = $request->items->count();
                    $totalQuantity = $request->items->sum('approved_quantity');
                    return "{$totalItems} items ({$totalQuantity} total)";
                })
                ->addColumn('actions', function ($request) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.fulfillment.show', $request->id) . '" class="btn btn-primary btn-sm">';
                    $actions .= '<i class="fas fa-box"></i> Fulfill';
                    $actions .= '</a>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['actions', 'status_badge'])
                ->make(true);
        }

        return view('admin.supply-fulfillment.index');
    }

    public function show(SupplyRequest $request)
    {
        $request->load(['department', 'employee', 'items.supply']);

        return view('admin.supply-fulfillment.show', compact('request'));
    }

    public function fulfill(Request $httpRequest, SupplyRequest $request)
    {
        $httpRequest->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:supply_request_items,id',
            'items.*.fulfill_quantity' => 'required|integer|min:1',
            'distribution_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $totalDistributed = 0;
            $distributions = [];

            foreach ($httpRequest->items as $itemData) {
                $item = SupplyRequestItem::findOrFail($itemData['item_id']);
                $fulfillQuantity = $itemData['fulfill_quantity'];

                // Validate quantity
                if ($fulfillQuantity > $item->getRemainingQuantity()) {
                    throw new \Exception("Fulfill quantity ({$fulfillQuantity}) cannot exceed remaining quantity ({$item->getRemainingQuantity()}) for item: {$item->supply->name}");
                }

                // Check stock availability
                if ($fulfillQuantity > $item->supply->current_stock) {
                    throw new \Exception("Insufficient stock for {$item->supply->name}. Available: {$item->supply->current_stock}, Requested: {$fulfillQuantity}");
                }

                // Create distribution record
                $distribution = SupplyDistribution::create([
                    'supply_id' => $item->supply_id,
                    'department_id' => $request->department_id,
                    'request_item_id' => $item->id,
                    'quantity' => $fulfillQuantity,
                    'distribution_date' => $httpRequest->distribution_date,
                    'distributed_by' => auth()->id(),
                    'notes' => $httpRequest->notes,
                ]);

                // Create outgoing transaction
                SupplyTransaction::create([
                    'supply_id' => $item->supply_id,
                    'type' => 'out',
                    'source' => 'manual',
                    'department_id' => $request->department_id,
                    'quantity' => $fulfillQuantity,
                    'reference_no' => 'DIST-' . $distribution->id,
                    'transaction_date' => $httpRequest->distribution_date,
                    'notes' => "Distribution for request #{$request->id}",
                    'user_id' => auth()->id(),
                ]);

                // Update stock
                $item->supply->decrement('current_stock', $fulfillQuantity);

                // Update fulfillment quantity
                $item->increment('fulfilled_quantity', $fulfillQuantity);
                $item->updateFulfillmentStatus();

                $totalDistributed += $fulfillQuantity;
                $distributions[] = $distribution;
            }

            // Update request status
            $allItemsCompleted = $request->items->every(function ($item) {
                return $item->fulfillment_status === 'completed';
            });

            $request->update([
                'status' => $allItemsCompleted ? 'fulfilled' : 'partially_fulfilled',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully distributed {$totalDistributed} items to {$request->department->department_name}",
                'redirect' => route('supplies.fulfillment.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(Request $request)
    {
        if ($request->ajax()) {
            $distributions = SupplyDistribution::with(['supply', 'department', 'distributedBy', 'requestItem.request'])
                ->select('supply_distributions.*');

            return DataTables::of($distributions)
                ->addIndexColumn()
                ->addColumn('supply_name', function ($distribution) {
                    return $distribution->supply->name ?? 'N/A';
                })
                ->addColumn('department_name', function ($distribution) {
                    return $distribution->department->department_name ?? 'N/A';
                })
                ->addColumn('distributed_by_name', function ($distribution) {
                    return $distribution->distributedBy->name ?? 'N/A';
                })
                ->addColumn('distribution_date', function ($distribution) {
                    return $distribution->distribution_date->format('M d, Y');
                })
                ->addColumn('request_reference', function ($distribution) {
                    return $distribution->requestItem ? "#{$distribution->requestItem->request_id}" : 'N/A';
                })
                ->rawColumns([])
                ->make(true);
        }

        return view('admin.supply-fulfillment.history');
    }
}
