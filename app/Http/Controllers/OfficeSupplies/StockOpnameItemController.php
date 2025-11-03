<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameItemController extends Controller
{
    public function index(Request $request, StockOpnameSession $session)
    {
        if ($request->ajax()) {
            $items = $session->items()->with(['supply', 'counter', 'verifier'])
                ->select('stock_opname_items.*');

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('supply_name', function ($item) {
                    return $item->supply->name;
                })
                ->addColumn('supply_code', function ($item) {
                    return $item->supply->code;
                })
                ->addColumn('supply_unit', function ($item) {
                    return $item->supply->unit;
                })
                ->addColumn('system_stock', function ($item) {
                    return number_format($item->system_stock);
                })
                ->addColumn('actual_count', function ($item) {
                    if ($item->actual_count !== null) {
                        return '<input type="number" class="form-control form-control-sm actual-count-input" 
                                data-item-id="' . $item->id . '" 
                                value="' . $item->actual_count . '" 
                                min="0" style="width: 80px;">';
                    }
                    return '<input type="number" class="form-control form-control-sm actual-count-input" 
                            data-item-id="' . $item->id . '" 
                            value="" min="0" style="width: 80px;">';
                })
                ->addColumn('variance', function ($item) {
                    return $item->getVarianceBadge();
                })
                ->addColumn('variance_value', function ($item) {
                    return 'Rp ' . number_format($item->variance_value, 2);
                })
                ->addColumn('status_badge', function ($item) {
                    return $item->getStatusBadge();
                })
                ->addColumn('reason_code_badge', function ($item) {
                    return $item->getReasonCodeBadge();
                })
                ->addColumn('reason_notes', function ($item) {
                    return $item->reason_notes ?: '-';
                })
                ->addColumn('photo', function ($item) {
                    if ($item->photo_path) {
                        return '<button class="btn btn-sm btn-info" onclick="viewPhoto(' . $item->id . ')">
                                <i class="fas fa-image"></i> View
                                </button>';
                    }
                    return '<button class="btn btn-sm btn-secondary" onclick="uploadPhoto(' . $item->id . ')">
                            <i class="fas fa-upload"></i> Upload
                            </button>';
                })
                ->addColumn('counter_name', function ($item) {
                    return $item->counter->name ?? '-';
                })
                ->addColumn('counted_at', function ($item) {
                    return $item->counted_at ? $item->counted_at->format('M d, Y H:i') : '-';
                })
                ->addColumn('actions', function ($item) {
                    $actions = '<div class="btn-group" role="group">';

                    // Count button
                    if ($item->status === 'pending' || $item->status === 'counting') {
                        $actions .= '<button onclick="countItem(' . $item->id . ')" class="btn btn-primary btn-sm">';
                        $actions .= '<i class="fas fa-calculator"></i> Count';
                        $actions .= '</button>';
                    }

                    // Verify button
                    if ($item->status === 'counted') {
                        $actions .= '<button onclick="verifyItem(' . $item->id . ')" class="btn btn-success btn-sm">';
                        $actions .= '<i class="fas fa-check"></i> Verify';
                        $actions .= '</button>';
                    }

                    // Edit reason button
                    if ($item->status !== 'pending') {
                        $actions .= '<button onclick="editReason(' . $item->id . ')" class="btn btn-warning btn-sm">';
                        $actions .= '<i class="fas fa-edit"></i> Reason';
                        $actions .= '</button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['actual_count', 'variance', 'status_badge', 'reason_code_badge', 'photo', 'actions'])
                ->make(true);
        }

        return view('office-supplies.stock-opname.items', compact('session'));
    }

    public function update(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        $request->validate([
            'actual_count' => 'required|integer|min:0',
            'reason_code' => 'nullable|in:damaged,expired,lost,found,miscount,other',
            'reason_notes' => 'nullable|string|max:1000',
        ]);

        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        // Mark as counted
        $item->markAsCounted(
            auth()->id(),
            $request->actual_count,
            $request->reason_code,
            $request->reason_notes
        );

        // Update session counters
        $session->update([
            'counted_items' => $session->items()->where('status', '!=', 'pending')->count(),
            'items_with_variance' => $session->items()->where('variance', '!=', 0)->count(),
            'total_variance_value' => $session->getTotalVarianceValue(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item counted successfully.',
            'variance' => $item->variance,
            'variance_value' => $item->variance_value,
        ]);
    }

    public function bulkUpdate(Request $request, StockOpnameSession $session)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:stock_opname_items,id',
            'items.*.actual_count' => 'required|integer|min:0',
            'items.*.reason_code' => 'nullable|in:damaged,expired,lost,found,miscount,other',
            'items.*.reason_notes' => 'nullable|string|max:1000',
        ]);

        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        foreach ($request->items as $itemData) {
            $item = StockOpnameItem::find($itemData['item_id']);
            $item->markAsCounted(
                auth()->id(),
                $itemData['actual_count'],
                $itemData['reason_code'] ?? null,
                $itemData['reason_notes'] ?? null
            );
        }

        // Update session counters
        $session->update([
            'counted_items' => $session->items()->where('status', '!=', 'pending')->count(),
            'items_with_variance' => $session->items()->where('variance', '!=', 0)->count(),
            'total_variance_value' => $session->getTotalVarianceValue(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Items updated successfully.',
        ]);
    }

    public function uploadPhoto(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        // Delete old photo if exists
        if ($item->photo_path && Storage::disk('public')->exists($item->photo_path)) {
            Storage::disk('public')->delete($item->photo_path);
        }

        // Store new photo
        $path = $request->file('photo')->store(
            'stock-opname-photos/' . $session->id,
            'public'
        );

        $item->update([
            'photo_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully.',
            'photo_url' => Storage::disk('public')->url($path),
        ]);
    }

    public function verify(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        // Check if item is counted
        if ($item->status !== 'counted') {
            return response()->json(['error' => 'Item must be counted before verification.'], 422);
        }

        $item->verify(auth()->id());

        // Update session counters
        $session->update([
            'counted_items' => $session->items()->where('status', '!=', 'pending')->count(),
            'items_with_variance' => $session->items()->where('variance', '!=', 0)->count(),
            'total_variance_value' => $session->getTotalVarianceValue(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item verified successfully.',
        ]);
    }

    /**
     * Save draft progress on an item (gradual counting support)
     * Allows saving partial data before final count submission
     */
    public function saveDraft(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        $request->validate([
            'actual_count' => 'nullable|integer|min:0',
            'reason_code' => 'nullable|in:damaged,expired,lost,found,miscount,other',
            'reason_notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        // Update item with draft data
        $updateData = [];

        if ($request->has('actual_count')) {
            $updateData['actual_count'] = $request->actual_count;

            // Calculate variance if actual count is provided
            if ($request->actual_count !== null) {
                $updateData['variance'] = $request->actual_count - $item->system_stock;
                $updateData['variance_value'] = $updateData['variance'] * ($item->supply->price ?? 0);
            }
        }

        if ($request->has('reason_code')) {
            $updateData['reason_code'] = $request->reason_code;
        }

        if ($request->has('reason_notes')) {
            $updateData['reason_notes'] = $request->reason_notes;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($item->photo_path && Storage::disk('public')->exists($item->photo_path)) {
                Storage::disk('public')->delete($item->photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store(
                'stock-opname-photos/' . $session->id,
                'public'
            );
            $updateData['photo_path'] = $path;
        }

        // Update status based on what data is available
        if (isset($updateData['actual_count']) && $updateData['actual_count'] !== null) {
            // If actual count is provided, mark as counting (work in progress)
            if ($item->status === 'pending') {
                $updateData['status'] = 'counting';
                $updateData['counted_by'] = auth()->id();
                $updateData['counted_at'] = now();
            }
        }

        $item->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully.',
            'item' => [
                'id' => $item->id,
                'actual_count' => $item->actual_count,
                'variance' => $item->variance,
                'variance_value' => $item->variance_value,
                'status' => $item->status,
                'photo_url' => $item->photo_path ? Storage::disk('public')->url($item->photo_path) : null,
            ]
        ]);
    }

    /**
     * Finalize counting for an item (move from counting to counted status)
     */
    public function finalizeCount(Request $request, StockOpnameSession $session, StockOpnameItem $item)
    {
        $request->validate([
            'reason_code' => 'nullable|in:damaged,expired,lost,found,miscount,other',
            'reason_notes' => 'nullable|string|max:1000',
        ]);

        // Check if session is in progress
        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not in progress.'], 422);
        }

        // Check if item has actual count
        if ($item->actual_count === null) {
            return response()->json(['error' => 'Actual count is required before finalizing.'], 422);
        }

        // Update status to counted
        $item->update([
            'status' => 'counted',
            'reason_code' => $request->reason_code,
            'reason_notes' => $request->reason_notes,
            'counted_at' => now(),
        ]);

        // Update session counters
        $session->update([
            'counted_items' => $session->items()->whereIn('status', ['counted', 'verified'])->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item count finalized successfully.',
        ]);
    }
}
