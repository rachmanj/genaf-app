<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameItem;
use App\Models\StockAdjustment;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sessions = StockOpnameSession::with(['creator', 'approver'])
                ->select('stock_opname_sessions.*');

            return DataTables::of($sessions)
                ->addColumn('index', function ($session) {
                    return '';
                })
                ->addColumn('session_code', function ($session) {
                    return '<strong>' . $session->session_code . '</strong>';
                })
                ->addColumn('title', function ($session) {
                    return $session->title;
                })
                ->addColumn('type_badge', function ($session) {
                    $badges = [
                        'manual' => '<span class="badge badge-primary">Manual</span>',
                        'scheduled' => '<span class="badge badge-info">Scheduled</span>',
                    ];
                    return $badges[$session->type] ?? '<span class="badge badge-secondary">Unknown</span>';
                })
                ->addColumn('status_badge', function ($session) {
                    $badges = [
                        'draft' => '<span class="badge badge-secondary">Draft</span>',
                        'in_progress' => '<span class="badge badge-warning">In Progress</span>',
                        'completed' => '<span class="badge badge-info">Completed</span>',
                        'approved' => '<span class="badge badge-success">Approved</span>',
                        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                    ];
                    return $badges[$session->status] ?? '<span class="badge badge-secondary">Unknown</span>';
                })
                ->addColumn('progress', function ($session) {
                    $percentage = $session->getProgressPercentage();
                    return '<div class="progress progress-sm">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: ' . $percentage . '%" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small>' . $session->counted_items . '/' . $session->total_items . ' (' . $percentage . '%)</small>';
                })
                ->addColumn('variance_info', function ($session) {
                    if ($session->items_with_variance > 0) {
                        return '<span class="text-danger">' . $session->items_with_variance . ' items</span><br>
                                <small class="text-muted">Value: ' . number_format($session->total_variance_value, 2) . '</small>';
                    }
                    return '<span class="text-success">No variance</span>';
                })
                ->addColumn('creator_name', function ($session) {
                    return $session->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function ($session) {
                    return $session->created_at->format('M d, Y H:i');
                })
                ->addColumn('actions', function ($session) {
                    $actions = '<div class="btn-group" role="group">';

                    // View button
                    $actions .= '<a href="' . route('stock-opname.show', $session->id) . '" class="btn btn-info btn-sm">';
                    $actions .= '<i class="fas fa-eye"></i> View';
                    $actions .= '</a>';

                    // Edit button (draft only)
                    if ($session->canBeStarted()) {
                        $actions .= '<a href="' . route('stock-opname.edit', $session->id) . '" class="btn btn-warning btn-sm">';
                        $actions .= '<i class="fas fa-edit"></i> Edit';
                        $actions .= '</a>';
                    }

                    // Start button
                    if ($session->canBeStarted()) {
                        $actions .= '<button onclick="startSession(' . $session->id . ')" class="btn btn-success btn-sm">';
                        $actions .= '<i class="fas fa-play"></i> Start';
                        $actions .= '</button>';
                    }

                    // Complete button
                    if ($session->canBeCompleted()) {
                        $actions .= '<button onclick="completeSession(' . $session->id . ')" class="btn btn-primary btn-sm">';
                        $actions .= '<i class="fas fa-check"></i> Complete';
                        $actions .= '</button>';
                    }

                    // Approve button
                    if ($session->canBeApproved()) {
                        $actions .= '<button onclick="approveSession(' . $session->id . ')" class="btn btn-success btn-sm">';
                        $actions .= '<i class="fas fa-check-double"></i> Approve';
                        $actions .= '</button>';
                    }

                    // Cancel button
                    if ($session->canBeCancelled()) {
                        $actions .= '<button onclick="cancelSession(' . $session->id . ')" class="btn btn-danger btn-sm">';
                        $actions .= '<i class="fas fa-times"></i> Cancel';
                        $actions .= '</button>';
                    }

                    // Export button
                    if ($session->status === 'approved') {
                        $actions .= '<a href="' . route('stock-opname.export', $session->id) . '" class="btn btn-secondary btn-sm">';
                        $actions .= '<i class="fas fa-download"></i> Export';
                        $actions .= '</a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['session_code', 'type_badge', 'status_badge', 'progress', 'variance_info', 'actions'])
                ->make(true);
        }

        return view('office-supplies.stock-opname.index');
    }

    public function create()
    {
        $supplies = Supply::where('is_active', true)->get();
        return view('office-supplies.stock-opname.create', compact('supplies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:manual,scheduled',
            'schedule_type' => 'nullable|in:monthly,quarterly,yearly',
            'supply_ids' => 'nullable|array',
            'supply_ids.*' => 'exists:supplies,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Create session
            $session = StockOpnameSession::create([
                'session_code' => StockOpnameSession::generateSessionCode(),
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'schedule_type' => $request->schedule_type,
                'created_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Get supplies to include
            if ($request->supply_ids && count($request->supply_ids) > 0) {
                $supplies = Supply::whereIn('id', $request->supply_ids)->where('is_active', true)->get();
            } else {
                $supplies = Supply::where('is_active', true)->get();
            }

            // Create items for each supply
            foreach ($supplies as $supply) {
                StockOpnameItem::create([
                    'session_id' => $session->id,
                    'supply_id' => $supply->id,
                    'system_stock' => $supply->current_stock,
                ]);
            }

            // Update session totals
            $session->update([
                'total_items' => $supplies->count(),
            ]);

            DB::commit();

            return redirect()->route('stock-opname.show', $session->id)
                ->with('success', 'Stock opname session created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create stock opname session: ' . $e->getMessage());
        }
    }

    public function show(StockOpnameSession $session)
    {
        $session->load(['items.supply', 'creator', 'approver']);
        return view('office-supplies.stock-opname.show', compact('session'));
    }

    public function edit(StockOpnameSession $session)
    {
        if (!$session->canBeStarted()) {
            return redirect()->route('stock-opname.show', $session->id)
                ->with('error', 'Cannot edit session that has been started.');
        }

        $supplies = Supply::where('is_active', true)->get();
        $selectedSupplies = $session->items->pluck('supply_id')->toArray();

        return view('office-supplies.stock-opname.edit', compact('session', 'supplies', 'selectedSupplies'));
    }

    public function update(Request $request, StockOpnameSession $session)
    {
        if (!$session->canBeStarted()) {
            return redirect()->route('stock-opname.show', $session->id)
                ->with('error', 'Cannot edit session that has been started.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:manual,scheduled',
            'schedule_type' => 'nullable|in:monthly,quarterly,yearly',
            'supply_ids' => 'nullable|array',
            'supply_ids.*' => 'exists:supplies,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Update session
            $session->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'schedule_type' => $request->schedule_type,
                'notes' => $request->notes,
            ]);

            // Remove existing items
            $session->items()->delete();

            // Get supplies to include
            if ($request->supply_ids && count($request->supply_ids) > 0) {
                $supplies = Supply::whereIn('id', $request->supply_ids)->where('is_active', true)->get();
            } else {
                $supplies = Supply::where('is_active', true)->get();
            }

            // Create new items
            foreach ($supplies as $supply) {
                StockOpnameItem::create([
                    'session_id' => $session->id,
                    'supply_id' => $supply->id,
                    'system_stock' => $supply->current_stock,
                ]);
            }

            // Update session totals
            $session->update([
                'total_items' => $supplies->count(),
            ]);

            DB::commit();

            return redirect()->route('stock-opname.show', $session->id)
                ->with('success', 'Stock opname session updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update stock opname session: ' . $e->getMessage());
        }
    }

    public function destroy(StockOpnameSession $session)
    {
        if (!$session->canBeStarted()) {
            return redirect()->route('supplies.stock-opname.index')
                ->with('error', 'Cannot delete session that has been started.');
        }

        $session->delete();

        return redirect()->route('stock-opname.index')
            ->with('success', 'Stock opname session deleted successfully.');
    }

    public function start(StockOpnameSession $session)
    {
        if (!$session->canBeStarted()) {
            return response()->json(['error' => 'Session cannot be started.'], 422);
        }

        $session->start();

        return response()->json([
            'success' => true,
            'message' => 'Stock opname session started successfully.',
        ]);
    }

    public function complete(StockOpnameSession $session)
    {
        if (!$session->canBeCompleted()) {
            return response()->json(['error' => 'Session cannot be completed.'], 422);
        }

        $session->complete();

        return response()->json([
            'success' => true,
            'message' => 'Stock opname session completed successfully.',
        ]);
    }

    public function approve(StockOpnameSession $session)
    {
        if (!$session->canBeApproved()) {
            return response()->json(['error' => 'Session cannot be approved.'], 422);
        }

        DB::beginTransaction();

        try {
            // Load items with supplies before approving
            $session->load('items.supply');
            
            // Approve session
            $session->approve(auth()->id());
            
            // Create adjustments for items with variance
            foreach ($session->items as $item) {
                if ($item->hasVariance()) {
                    $adjustmentType = $item->variance > 0 ? 'increase' : 'decrease';
                    $quantity = abs($item->variance);
                    $newStock = $item->supply->current_stock + $item->variance;

                    $adjustment = StockAdjustment::create([
                        'session_id' => $session->id,
                        'supply_id' => $item->supply_id,
                        'adjustment_type' => $adjustmentType,
                        'quantity' => $quantity,
                        'old_stock' => $item->supply->current_stock,
                        'new_stock' => $newStock,
                        'reason_code' => $item->reason_code,
                        'reason_notes' => $item->reason_notes,
                        'adjusted_by' => auth()->id(),
                        'adjusted_at' => now(),
                    ]);

                    // Apply the adjustment
                    $adjustment->apply();
                }
            }

            // Update session totals
            $session->update([
                'items_with_variance' => $session->items()->where('variance', '!=', 0)->count(),
                'total_variance_value' => $session->getTotalVarianceValue(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock opname session approved and adjustments applied successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to approve session: ' . $e->getMessage()], 422);
        }
    }

    public function cancel(StockOpnameSession $session)
    {
        if (!$session->canBeCancelled()) {
            return response()->json(['error' => 'Session cannot be cancelled.'], 422);
        }

        $session->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Stock opname session cancelled successfully.',
        ]);
    }

    public function export(StockOpnameSession $session)
    {
        // TODO: Implement Excel/PDF export
        return response()->json(['message' => 'Export functionality will be implemented.']);
    }
}
