<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class SupplyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $supplies = Supply::query();

            return DataTables::of($supplies)
                ->addColumn('stock_status', function ($supply) {
                    $status = $supply->stock_status;
                    $badgeClass = match ($status) {
                        'out_of_stock' => 'badge-danger',
                        'low_stock' => 'badge-warning',
                        'in_stock' => 'badge-success',
                        default => 'badge-secondary'
                    };

                    $statusText = match ($status) {
                        'out_of_stock' => 'Out of Stock',
                        'low_stock' => 'Low Stock',
                        'in_stock' => 'In Stock',
                        default => 'Unknown'
                    };

                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('actions', function ($supply) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.show', $supply) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    if (auth()->user()->can('edit supplies')) {
                        $actions .= '<a href="' . route('supplies.edit', $supply) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('delete supplies')) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteSupply(' . $supply->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['stock_status', 'actions'])
                ->make(true);
        }

        return view('office-supplies.supplies.index');
    }

    public function create()
    {
        $this->authorize('create supplies');

        $categories = [
            'ATK' => 'Alat Tulis Kantor',
            'Cleaning' => 'Peralatan Kebersihan',
            'Pantry' => 'Perlengkapan Dapur',
            'IT' => 'Perlengkapan IT',
            'Office' => 'Perlengkapan Kantor',
            'Other' => 'Lain-lain'
        ];

        $units = [
            'pcs' => 'Pieces',
            'box' => 'Box',
            'pack' => 'Pack',
            'roll' => 'Roll',
            'bottle' => 'Bottle',
            'kg' => 'Kilogram',
            'liter' => 'Liter',
            'meter' => 'Meter'
        ];

        return view('office-supplies.supplies.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $this->authorize('create supplies');

        $request->validate([
            'code' => 'required|string|max:50|unique:supplies,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $supply = Supply::create($request->all());

        return redirect()->route('supplies.index')
            ->with('success', 'Supply created successfully.');
    }

    public function show(Supply $supply)
    {
        $supply->load(['transactions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('office-supplies.supplies.show', compact('supply'));
    }

    public function edit(Supply $supply)
    {
        $this->authorize('edit supplies');

        $categories = [
            'ATK' => 'Alat Tulis Kantor',
            'Cleaning' => 'Peralatan Kebersihan',
            'Pantry' => 'Perlengkapan Dapur',
            'IT' => 'Perlengkapan IT',
            'Office' => 'Perlengkapan Kantor',
            'Other' => 'Lain-lain'
        ];

        $units = [
            'pcs' => 'Pieces',
            'box' => 'Box',
            'pack' => 'Pack',
            'roll' => 'Roll',
            'bottle' => 'Bottle',
            'kg' => 'Kilogram',
            'liter' => 'Liter',
            'meter' => 'Meter'
        ];

        return view('office-supplies.supplies.edit', compact('supply', 'categories', 'units'));
    }

    public function update(Request $request, Supply $supply)
    {
        $this->authorize('edit supplies');

        $request->validate([
            'code' => 'required|string|max:50|unique:supplies,code,' . $supply->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $supply->update($request->all());

        return redirect()->route('supplies.index')
            ->with('success', 'Supply updated successfully.');
    }

    public function destroy(Supply $supply): JsonResponse
    {
        $this->authorize('delete supplies');

        try {
            $supply->delete();
            return response()->json(['success' => true, 'message' => 'Supply deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cannot delete supply. It may have related transactions.']);
        }
    }
}
