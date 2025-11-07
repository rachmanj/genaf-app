<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\SupplyDistribution;
use App\Models\SupplyTransaction;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SupplyFulfillmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = SupplyRequest::with(['department', 'employee', 'items.supply'])
                ->whereIn('status', ['approved', 'partially_fulfilled'])
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
                    if ($request->status === 'partially_fulfilled') {
                        return '<span class="badge badge-warning">Partially Fulfilled</span>';
                    }
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

        return view('office-supplies.supply-fulfillment.index');
    }

    public function show(SupplyRequest $request)
    {
        $request->load(['department', 'employee', 'items.supply']);

        return view('office-supplies.supply-fulfillment.show', compact('request'));
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

                // Create distribution record with pending verification
                $distribution = SupplyDistribution::create([
                    'supply_id' => $item->supply_id,
                    'department_id' => $request->department_id,
                    'request_item_id' => $item->id,
                    'quantity' => $fulfillQuantity,
                    'distribution_date' => $httpRequest->distribution_date,
                    'distributed_by' => auth()->id(),
                    'notes' => $httpRequest->notes,
                    'verification_status' => 'pending', // Awaiting requestor verification
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

            // Update request status to pending_verification (awaiting requestor verification)
            $request->refresh();
            $request->update([
                'status' => 'pending_verification',
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

        return view('office-supplies.supply-fulfillment.history');
    }

    /**
     * Show distributions pending verification by requestor
     */
    public function pendingVerification(Request $request)
    {
        if ($request->ajax()) {
            $distributions = SupplyDistribution::with(['supply', 'department', 'distributedBy', 'requestItem.request'])
                ->whereHas('requestItem.request', function ($query) {
                    $query->where('employee_id', auth()->id());
                })
                ->where('verification_status', 'pending')
                ->select('supply_distributions.*');

            return DataTables::of($distributions)
                ->addIndexColumn()
                ->addColumn('form_number', function ($distribution) {
                    return $distribution->form_number ?? 'N/A';
                })
                ->addColumn('supply_name', function ($distribution) {
                    return $distribution->supply->name ?? 'N/A';
                })
                ->addColumn('supply_code', function ($distribution) {
                    return $distribution->supply->code ?? 'N/A';
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
                    return $distribution->requestItem && $distribution->requestItem->request
                        ? "#{$distribution->requestItem->request->form_number}"
                        : 'N/A';
                })
                ->addColumn('days_pending', function ($distribution) {
                    return $distribution->created_at->diffInDays(now());
                })
                ->addColumn('actions', function ($distribution) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.fulfillment.verify-show', $distribution->id) . '" class="btn btn-success btn-sm" title="Verify">';
                    $actions .= '<i class="fas fa-check"></i> Verify';
                    $actions .= '</a>';
                    $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectDistribution(' . $distribution->id . ')" title="Reject">';
                    $actions .= '<i class="fas fa-times"></i> Reject';
                    $actions .= '</button>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('office-supplies.supply-fulfillment.pending-verification');
    }

    /**
     * Show verification form for a distribution
     */
    public function verifyShow(SupplyDistribution $distribution)
    {
        // Security check: Only requestor can verify their own distributions
        if (!$distribution->canBeVerifiedBy(auth()->user())) {
            abort(403, 'You can only verify distributions from your own requests.');
        }

        $distribution->load(['supply', 'department', 'distributedBy', 'requestItem.request.employee']);

        return view('office-supplies.supply-fulfillment.verify', compact('distribution'));
    }

    /**
     * Verify distribution (requestor confirms receipt)
     */
    public function verify(Request $httpRequest, SupplyDistribution $distribution)
    {
        // Security check
        if (!$distribution->canBeVerifiedBy(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You can only verify distributions from your own requests.',
            ], 403);
        }

        $httpRequest->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Mark distribution as verified
            $distribution->markAsVerified(auth()->user(), $httpRequest->verification_notes);

            // Check if all distributions for this request are verified
            $request = $distribution->requestItem->request;
            $request->load(['items' => function ($query) {
                $query->with('distributions');
            }]);

            $allVerified = true;
            $hasRejected = false;

            foreach ($request->items as $item) {
                $itemDistributions = $item->distributions;
                $nonVerifiedDistributions = $itemDistributions->where('verification_status', '!=', 'verified');
                if ($nonVerifiedDistributions->isNotEmpty()) {
                    $allVerified = false;
                }

                $rejectedDistributions = $itemDistributions->where('verification_status', 'rejected');
                if ($rejectedDistributions->isNotEmpty()) {
                    $hasRejected = true;
                }
            }

            // Update request status based on verification
            if ($hasRejected) {
                // If any distribution is rejected, status remains pending_verification (GA Admin will review)
                // Request status should allow GA Admin to see rejected items
            } elseif ($allVerified) {
                // All distributions verified, check if all items are completed
                $allItemsCompleted = $request->items->every(function ($item) {
                    return $item->fulfillment_status === 'completed';
                });

                $request->update([
                    'status' => $allItemsCompleted ? 'fulfilled' : 'partially_fulfilled',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Distribution verified successfully.',
                'redirect' => route('supplies.fulfillment.pending-verification'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify distribution: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject distribution (requestor reports issue)
     */
    public function rejectVerification(Request $httpRequest, SupplyDistribution $distribution)
    {
        // Security check
        if (!$distribution->canBeVerifiedBy(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You can only reject distributions from your own requests.',
            ], 403);
        }

        $httpRequest->validate([
            'rejection_reason' => 'required|string|max:1000',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Mark distribution as rejected
            $distribution->markAsRejected(
                auth()->user(),
                $httpRequest->rejection_reason,
                $httpRequest->verification_notes
            );

            // Request status remains pending_verification
            // GA Admin will see rejected distributions and can re-fulfill if needed

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Distribution rejected. GA Admin will review.',
                'redirect' => route('supplies.fulfillment.pending-verification'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject distribution: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show rejected distributions for GA Admin review
     */
    public function rejectedDistributions(Request $request)
    {
        // Only GA Admin can view rejected distributions
        if (!auth()->user()->hasPermissionTo('view supply fulfillment')) {
            abort(403, 'You do not have permission to view rejected distributions.');
        }

        if ($request->ajax()) {
            $distributions = SupplyDistribution::with([
                'supply',
                'department',
                'distributedBy',
                'requestItem.request.employee',
                'verifiedBy'
            ])
                ->where('verification_status', 'rejected')
                ->select('supply_distributions.*');

            return DataTables::of($distributions)
                ->addIndexColumn()
                ->addColumn('form_number', function ($distribution) {
                    return $distribution->form_number ?? 'N/A';
                })
                ->addColumn('supply_name', function ($distribution) {
                    return $distribution->supply->name ?? 'N/A';
                })
                ->addColumn('supply_code', function ($distribution) {
                    return $distribution->supply->code ?? 'N/A';
                })
                ->addColumn('department_name', function ($distribution) {
                    return $distribution->department->department_name ?? 'N/A';
                })
                ->addColumn('requestor_name', function ($distribution) {
                    return $distribution->requestItem && $distribution->requestItem->request && $distribution->requestItem->request->employee
                        ? $distribution->requestItem->request->employee->name
                        : 'N/A';
                })
                ->addColumn('distributed_by_name', function ($distribution) {
                    return $distribution->distributedBy->name ?? 'N/A';
                })
                ->addColumn('distribution_date', function ($distribution) {
                    return $distribution->distribution_date->format('M d, Y');
                })
                ->addColumn('rejected_at', function ($distribution) {
                    return $distribution->verified_at ? $distribution->verified_at->format('M d, Y H:i') : 'N/A';
                })
                ->addColumn('rejected_by_name', function ($distribution) {
                    return $distribution->verifiedBy->name ?? 'N/A';
                })
                ->addColumn('request_reference', function ($distribution) {
                    return $distribution->requestItem && $distribution->requestItem->request
                        ? "#{$distribution->requestItem->request->form_number}"
                        : 'N/A';
                })
                ->addColumn('rejection_reason', function ($distribution) {
                    return Str::limit($distribution->rejection_reason ?? 'N/A', 50);
                })
                ->addColumn('actions', function ($distribution) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.fulfillment.rejected-show', $distribution->id) . '" class="btn btn-info btn-sm" title="View Details">';
                    $actions .= '<i class="fas fa-eye"></i> View';
                    $actions .= '</a>';
                    // Link to re-fulfill the request
                    if ($distribution->requestItem && $distribution->requestItem->request) {
                        $requestId = $distribution->requestItem->request->id;
                        $actions .= '<a href="' . route('supplies.fulfillment.show', $requestId) . '" class="btn btn-primary btn-sm" title="Re-fulfill Request">';
                        $actions .= '<i class="fas fa-redo"></i> Re-fulfill';
                        $actions .= '</a>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('office-supplies.supply-fulfillment.rejected-distributions');
    }

    /**
     * Show details of a rejected distribution
     */
    public function rejectedShow(SupplyDistribution $distribution)
    {
        // Only GA Admin can view rejected distributions
        if (!auth()->user()->hasPermissionTo('view supply fulfillment')) {
            abort(403, 'You do not have permission to view rejected distributions.');
        }

        if ($distribution->verification_status !== 'rejected') {
            abort(404, 'Distribution is not rejected.');
        }

        $distribution->load([
            'supply',
            'department',
            'distributedBy',
            'requestItem.request.employee',
            'verifiedBy'
        ]);

        return view('office-supplies.supply-fulfillment.rejected-show', compact('distribution'));
    }
}
