<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use App\Models\SupplyRequest;
use App\Models\SupplyTransaction;
use App\Models\SupplyDistribution;
use App\Models\StockOpnameSession;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_supplies' => Supply::count(),
            'active_supplies' => Supply::where('is_active', true)->count(),
            'low_stock_count' => Supply::whereRaw('current_stock <= min_stock')->count(),
            'out_of_stock_count' => Supply::where('current_stock', 0)->count(),
            
            'total_requests' => SupplyRequest::count(),
            'pending_requests' => SupplyRequest::whereIn('status', ['pending_dept_head', 'pending_ga_admin'])->count(),
            'approved_requests' => SupplyRequest::where('status', 'approved')->count(),
            'fulfilled_requests' => SupplyRequest::whereIn('status', ['fulfilled', 'partially_fulfilled'])->count(),
            
            'total_transactions' => SupplyTransaction::count(),
            'incoming_transactions' => SupplyTransaction::where('type', 'in')->count(),
            'outgoing_transactions' => SupplyTransaction::where('type', 'out')->count(),
            
            'total_distributions' => SupplyDistribution::count(),
            'distributions_this_month' => SupplyDistribution::whereMonth('distribution_date', now()->month)
                ->whereYear('distribution_date', now()->year)
                ->count(),
            
            'stock_opname_sessions' => StockOpnameSession::count(),
            'active_opname_sessions' => StockOpnameSession::where('status', 'in_progress')->count(),
            'pending_approval_opname' => StockOpnameSession::where('status', 'completed')->count(),
            
            'total_departments' => Department::where('status', true)->count(),
            'active_departments_with_stock' => Department::whereHas('supplyDistributions')->distinct()->count(),
        ];

        $category_distribution = Supply::select('category', DB::raw('count(*) as count'))
            ->where('is_active', true)
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        $recent_requests = SupplyRequest::with(['employee', 'department'])
            ->latest()
            ->take(10)
            ->get();

        $low_stock_items = Supply::whereRaw('current_stock <= min_stock')
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->take(10)
            ->get();

        $recent_transactions = SupplyTransaction::with(['supply', 'department', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $top_supplies_by_usage = SupplyDistribution::select('supplies.name', 'supplies.code', DB::raw('SUM(supply_distributions.quantity) as total_distributed'))
            ->join('supplies', 'supply_distributions.supply_id', '=', 'supplies.id')
            ->groupBy('supplies.id', 'supplies.name', 'supplies.code')
            ->orderBy('total_distributed', 'desc')
            ->take(10)
            ->get();

        $monthly_transactions = SupplyTransaction::select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as incoming'),
                DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as outgoing')
            )
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare data for charts
        $category_chart_labels = $category_distribution->pluck('category')->toArray();
        $category_chart_data = $category_distribution->pluck('count')->toArray();
        $category_chart_colors = $this->generateColors(count($category_chart_labels));

        $monthly_chart_labels = $monthly_transactions->pluck('month')->toArray();
        $monthly_chart_incoming = $monthly_transactions->pluck('incoming')->toArray();
        $monthly_chart_outgoing = $monthly_transactions->pluck('outgoing')->toArray();

        $top_supplies_labels = $top_supplies_by_usage->take(10)->pluck('code')->toArray();
        $top_supplies_data = $top_supplies_by_usage->take(10)->pluck('total_distributed')->toArray();

        // Stock status distribution
        $stock_status_counts = [
            'in_stock' => Supply::whereRaw('current_stock > min_stock')->where('is_active', true)->count(),
            'low_stock' => Supply::whereRaw('current_stock > 0 AND current_stock <= min_stock')->where('is_active', true)->count(),
            'out_of_stock' => Supply::where('current_stock', 0)->where('is_active', true)->count(),
        ];

        return view('office-supplies.supplies.dashboard', compact(
            'stats',
            'category_distribution',
            'recent_requests',
            'low_stock_items',
            'recent_transactions',
            'top_supplies_by_usage',
            'monthly_transactions',
            'category_chart_labels',
            'category_chart_data',
            'category_chart_colors',
            'monthly_chart_labels',
            'monthly_chart_incoming',
            'monthly_chart_outgoing',
            'top_supplies_labels',
            'top_supplies_data',
            'stock_status_counts'
        ));
    }

    private function generateColors($count)
    {
        $colors = [
            '#3c8dbc', '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3d9970',
            '#39cccc', '#ff851b', '#0073b7', '#01ff70', '#b10dc9', '#ff4136',
            '#85144b', '#ffdc00', '#aaaaaa', '#111111', '#001f3f', '#0074d9',
            '#7fdbff', '#2ecc40', '#ffd700', '#ff69b4', '#00ced1', '#ff6347'
        ];

        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $colors[$i % count($colors)];
        }
        return $result;
    }
}
