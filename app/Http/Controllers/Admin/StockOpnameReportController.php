<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameItem;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameReportController extends Controller
{
    public function variance(Request $request)
    {
        if ($request->ajax()) {
            $items = StockOpnameItem::with(['session', 'supply'])
                ->where('variance', '!=', 0)
                ->select('stock_opname_items.*');

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('session_code', function ($item) {
                    return $item->session->session_code;
                })
                ->addColumn('supply_name', function ($item) {
                    return $item->supply->name;
                })
                ->addColumn('supply_code', function ($item) {
                    return $item->supply->code;
                })
                ->addColumn('system_stock', function ($item) {
                    return number_format($item->system_stock);
                })
                ->addColumn('actual_count', function ($item) {
                    return number_format($item->actual_count);
                })
                ->addColumn('variance', function ($item) {
                    return $item->getVarianceBadge();
                })
                ->addColumn('variance_value', function ($item) {
                    return 'Rp ' . number_format($item->variance_value, 2);
                })
                ->addColumn('reason_code_badge', function ($item) {
                    return $item->getReasonCodeBadge();
                })
                ->addColumn('reason_notes', function ($item) {
                    return $item->reason_notes ?: '-';
                })
                ->addColumn('counted_at', function ($item) {
                    return $item->counted_at ? $item->counted_at->format('M d, Y H:i') : '-';
                })
                ->rawColumns(['variance', 'reason_code_badge'])
                ->make(true);
        }

        return view('admin.stock-opname.reports.variance');
    }

    public function accuracy(Request $request)
    {
        if ($request->ajax()) {
            $sessions = StockOpnameSession::with(['creator'])
                ->where('status', 'approved')
                ->select('stock_opname_sessions.*');

            return DataTables::of($sessions)
                ->addIndexColumn()
                ->addColumn('session_code', function ($session) {
                    return $session->session_code;
                })
                ->addColumn('title', function ($session) {
                    return $session->title;
                })
                ->addColumn('total_items', function ($session) {
                    return number_format($session->total_items);
                })
                ->addColumn('items_with_variance', function ($session) {
                    return number_format($session->items_with_variance);
                })
                ->addColumn('accuracy_rate', function ($session) {
                    $rate = $session->getAccuracyRate();
                    $badgeClass = $rate >= 95 ? 'success' : ($rate >= 90 ? 'warning' : 'danger');
                    return '<span class="badge badge-' . $badgeClass . '">' . $rate . '%</span>';
                })
                ->addColumn('total_variance_value', function ($session) {
                    return 'Rp ' . number_format($session->total_variance_value, 2);
                })
                ->addColumn('creator_name', function ($session) {
                    return $session->creator->name ?? 'N/A';
                })
                ->addColumn('approved_at', function ($session) {
                    return $session->approved_at ? $session->approved_at->format('M d, Y H:i') : '-';
                })
                ->rawColumns(['accuracy_rate'])
                ->make(true);
        }

        return view('admin.stock-opname.reports.accuracy');
    }

    public function trends(Request $request)
    {
        // Get trend data for charts
        $monthlyData = StockOpnameSession::where('status', 'approved')
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as sessions, AVG(items_with_variance) as avg_variance')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $reasonData = StockOpnameItem::where('variance', '!=', 0)
            ->selectRaw('reason_code, COUNT(*) as count')
            ->groupBy('reason_code')
            ->get();

        $topVarianceItems = StockOpnameItem::with(['supply'])
            ->where('variance', '!=', 0)
            ->selectRaw('supply_id, SUM(ABS(variance_value)) as total_variance_value')
            ->groupBy('supply_id')
            ->orderBy('total_variance_value', 'desc')
            ->limit(10)
            ->get();

        return view('admin.stock-opname.reports.trends', compact('monthlyData', 'reasonData', 'topVarianceItems'));
    }

    public function history(Request $request)
    {
        if ($request->ajax()) {
            $adjustments = StockAdjustment::with(['session', 'supply', 'adjuster'])
                ->select('stock_adjustments.*');

            return DataTables::of($adjustments)
                ->addIndexColumn()
                ->addColumn('session_code', function ($adjustment) {
                    return $adjustment->session->session_code;
                })
                ->addColumn('supply_name', function ($adjustment) {
                    return $adjustment->supply->name;
                })
                ->addColumn('supply_code', function ($adjustment) {
                    return $adjustment->supply->code;
                })
                ->addColumn('adjustment_type_badge', function ($adjustment) {
                    return $adjustment->getAdjustmentTypeBadge();
                })
                ->addColumn('quantity', function ($adjustment) {
                    return number_format($adjustment->quantity);
                })
                ->addColumn('old_stock', function ($adjustment) {
                    return number_format($adjustment->old_stock);
                })
                ->addColumn('new_stock', function ($adjustment) {
                    return number_format($adjustment->new_stock);
                })
                ->addColumn('reason_code_badge', function ($adjustment) {
                    return $adjustment->getReasonCodeBadge();
                })
                ->addColumn('reason_notes', function ($adjustment) {
                    return $adjustment->reason_notes ?: '-';
                })
                ->addColumn('adjuster_name', function ($adjustment) {
                    return $adjustment->adjuster->name ?? 'N/A';
                })
                ->addColumn('adjusted_at', function ($adjustment) {
                    return $adjustment->adjusted_at->format('M d, Y H:i');
                })
                ->rawColumns(['adjustment_type_badge', 'reason_code_badge'])
                ->make(true);
        }

        return view('admin.stock-opname.reports.history');
    }
}
