<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SupplyDistribution;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::withCount(['supplyDistributions'])
                ->with(['supplyDistributions.supply'])
                ->select('departments.*');

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($department) {
                    $badgeClass = $department->status ? 'badge-success' : 'badge-danger';
                    $statusText = $department->status ? 'Active' : 'Inactive';
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('total_distributions', function ($department) {
                    return $department->supply_distributions_count;
                })
                ->addColumn('total_quantity', function ($department) {
                    return $department->supplyDistributions->sum('quantity');
                })
                ->addColumn('last_distribution', function ($department) {
                    $lastDistribution = $department->supplyDistributions->sortByDesc('distribution_date')->first();
                    return $lastDistribution ? $lastDistribution->distribution_date->format('M d, Y') : 'Never';
                })
                ->addColumn('actions', function ($department) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.department-stock.show', $department->id) . '" class="btn btn-info btn-sm">';
                    $actions .= '<i class="fas fa-eye"></i> View Details';
                    $actions .= '</a>';
                    $actions .= '<a href="' . route('supplies.department-stock.report', $department->id) . '" class="btn btn-success btn-sm" target="_blank">';
                    $actions .= '<i class="fas fa-file-pdf"></i> Report';
                    $actions .= '</a>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('office-supplies.department-stock.index');
    }

    public function show(Department $department)
    {
        $department->load(['supplyDistributions.supply', 'supplyDistributions.distributedBy']);
        
        // Get distribution summary by supply
        $distributionSummary = $department->supplyDistributions
            ->groupBy('supply_id')
            ->map(function ($distributions) {
                $supply = $distributions->first()->supply;
                return [
                    'supply' => $supply,
                    'total_quantity' => $distributions->sum('quantity'),
                    'distribution_count' => $distributions->count(),
                    'last_distribution' => $distributions->sortByDesc('distribution_date')->first()->distribution_date,
                ];
            })
            ->sortByDesc('total_quantity');

        return view('office-supplies.department-stock.show', compact('department', 'distributionSummary'));
    }

    public function report(Department $department)
    {
        $department->load(['supplyDistributions.supply', 'supplyDistributions.distributedBy']);
        
        // Get distribution summary by supply
        $distributionSummary = $department->supplyDistributions
            ->groupBy('supply_id')
            ->map(function ($distributions) {
                $supply = $distributions->first()->supply;
                return [
                    'supply' => $supply,
                    'total_quantity' => $distributions->sum('quantity'),
                    'distribution_count' => $distributions->count(),
                    'last_distribution' => $distributions->sortByDesc('distribution_date')->first()->distribution_date,
                ];
            })
            ->sortByDesc('total_quantity');

        return view('office-supplies.department-stock.report', compact('department', 'distributionSummary'));
    }
}