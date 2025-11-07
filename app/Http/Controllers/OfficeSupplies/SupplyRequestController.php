<?php

namespace App\Http\Controllers\OfficeSupplies;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\SupplyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplyRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SupplyRequest::with(['employee', 'department', 'departmentHeadApprover', 'gaAdminApprover', 'items.supply'])
                ->select('supply_requests.*');

            // Filter by department for non-admin/ga-admin users
            if (!auth()->user()->canViewAllDepartments()) {
                $query->where('department_id', auth()->user()->department_id);
            }

            return DataTables::of($query)
                ->addColumn('index', function ($request) {
                    return '';
                })
                ->addColumn('form_number', function ($request) {
                    return $request->form_number ?? 'N/A';
                })
                ->addColumn('employee_name', function ($request) {
                    return $request->employee ? $request->employee->name : 'N/A';
                })
                ->addColumn('department_name', function ($request) {
                    return $request->department ? $request->department->department_name : 'N/A';
                })
                ->addColumn('request_date_formatted', function ($request) {
                    return $request->request_date ? $request->request_date->format('d/m/Y') : 'N/A';
                })
                ->addColumn('status_badge', function ($request) {
                    $badgeClass = match ($request->status) {
                        'pending_dept_head' => 'badge-warning',
                        'pending_ga_admin' => 'badge-info',
                        'approved' => 'badge-success',
                        'rejected' => 'badge-danger',
                        'pending_verification' => 'badge-warning',
                        'partially_fulfilled' => 'badge-primary',
                        'fulfilled' => 'badge-success',
                        default => 'badge-secondary'
                    };
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $request->status)) . '</span>';
                })
                ->addColumn('dept_head_approver', function ($request) {
                    return $request->departmentHeadApprover ? $request->departmentHeadApprover->name : 'N/A';
                })
                ->addColumn('dept_head_approved_at', function ($request) {
                    return $request->department_head_approved_at ? $request->department_head_approved_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('ga_admin_approver', function ($request) {
                    return $request->gaAdminApprover ? $request->gaAdminApprover->name : 'N/A';
                })
                ->addColumn('ga_admin_approved_at', function ($request) {
                    return $request->ga_admin_approved_at ? $request->ga_admin_approved_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('items_count', function ($request) {
                    return $request->items->count();
                })
                ->addColumn('total_quantity', function ($request) {
                    return $request->items->sum('quantity');
                })
                ->addColumn('actions', function ($request) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('supplies.requests.show', $request) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-eye"></i></a>';

                    if (auth()->user()->can('edit supply requests') && $request->status === 'pending_dept_head') {
                        $actions .= '<a href="' . route('supplies.requests.edit', $request) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    }

                    if (auth()->user()->can('approve dept head supply requests') && $request->canBeDeptHeadApproved()) {
                        $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="approveDeptHead(' . $request->id . ')" title="Approve"><i class="fas fa-check"></i></button>';
                    }

                    if (auth()->user()->can('approve ga admin supply requests') && $request->canBeGAAdminApproved()) {
                        $actions .= '<button type="button" class="btn btn-primary btn-sm" onclick="approveGAAdmin(' . $request->id . ')" title="GA Approve"><i class="fas fa-check-double"></i></button>';
                    }

                    if (auth()->user()->can('reject dept head supply requests') && $request->canBeDeptHeadRejected()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectDeptHead(' . $request->id . ')" title="Reject"><i class="fas fa-times"></i></button>';
                    }

                    if (auth()->user()->can('reject ga admin supply requests') && $request->canBeGAAdminRejected()) {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="rejectGAAdmin(' . $request->id . ')" title="GA Reject"><i class="fas fa-times"></i></button>';
                    }

                    if (auth()->user()->can('delete supply requests') && $request->status === 'pending_dept_head') {
                        $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="deleteRequest(' . $request->id . ')" title="Delete"><i class="fas fa-trash"></i></button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('office-supplies.supply-requests.index');
    }

    public function create()
    {
        return view('office-supplies.supply-requests.create');
    }

    public function suppliesData(Request $request)
    {
        if ($request->ajax()) {
            $supplies = Supply::select('supplies.*');

            return DataTables::of($supplies)
                ->editColumn('category', function ($supply) {
                    return $supply->category ?? 'N/A';
                })
                ->rawColumns(['category'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $supplyRequest = SupplyRequest::create([
                'employee_id' => auth()->id(),
                'department_id' => auth()->user()->department_id,
                'request_date' => $request->request_date,
                'status' => 'pending_dept_head',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                SupplyRequestItem::create([
                    'request_id' => $supplyRequest->id,
                    'supply_id' => $item['supply_id'],
                    'quantity' => $item['quantity'],
                    'approved_quantity' => 0, // Will be set during GA admin approval
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supply request created successfully.',
                    'redirect' => route('supplies.requests.index')
                ]);
            }

            return redirect()->route('supplies.requests.index')
                ->with('success', 'Supply request created successfully.');
        } catch (\Exception $e) {
            DB::rollback();

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create supply request: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create supply request: ' . $e->getMessage());
        }
    }

    public function show(SupplyRequest $request)
    {
        $request->load(['employee', 'department', 'departmentHeadApprover', 'gaAdminApprover', 'items.supply']);

        if (request()->ajax()) {
            return response()->json([
                'id' => $request->id,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'request_date' => $request->request_date,
                'status' => $request->status,
                'notes' => $request->notes,
                'items' => $request->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'supply_id' => $item->supply_id,
                        'quantity' => $item->quantity,
                        'approved_quantity' => $item->approved_quantity,
                        'supply' => [
                            'id' => $item->supply->id,
                            'name' => $item->supply->name,
                            'code' => $item->supply->code,
                            'current_stock' => $item->supply->current_stock,
                            'unit' => $item->supply->unit,
                        ]
                    ];
                })
            ]);
        }

        return view('office-supplies.supply-requests.show', ['supplyRequest' => $request]);
    }

    public function edit(SupplyRequest $supplyRequest)
    {
        if ($supplyRequest->status !== 'pending_dept_head') {
            return redirect()->route('supplies.requests.index')
                ->with('error', 'Only pending requests can be edited.');
        }

        $supplies = Supply::orderBy('name')->get();
        $supplyRequest->load('items');
        return view('office-supplies.supply-requests.edit', compact('supplyRequest', 'supplies'));
    }

    public function update(Request $request, SupplyRequest $supplyRequest)
    {
        if ($supplyRequest->status !== 'pending_dept_head') {
            return redirect()->route('supplies.requests.index')
                ->with('error', 'Only pending requests can be updated.');
        }

        $request->validate([
            'request_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $supplyRequest->update([
                'request_date' => $request->request_date,
                'notes' => $request->notes,
            ]);

            // Delete existing items and create new ones
            $supplyRequest->items()->delete();
            foreach ($request->items as $item) {
                SupplyRequestItem::create([
                    'request_id' => $supplyRequest->id,
                    'supply_id' => $item['supply_id'],
                    'quantity' => $item['quantity'],
                    'approved_quantity' => 0, // Will be set during GA admin approval
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('supplies.requests.index')
                ->with('success', 'Supply request updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update supply request: ' . $e->getMessage());
        }
    }

    public function destroy(SupplyRequest $supplyRequest)
    {
        if ($supplyRequest->status !== 'pending_dept_head') {
            return response()->json(['error' => 'Only pending requests can be deleted.'], 400);
        }

        $supplyRequest->items()->delete();
        $supplyRequest->delete();

        return response()->json(['message' => 'Supply request deleted successfully.']);
    }

    public function approveDeptHead(Request $request, SupplyRequest $supplyRequest)
    {
        if (!$supplyRequest->canBeDeptHeadApproved()) {
            return response()->json(['error' => 'Request cannot be approved by department head.'], 400);
        }

        // Security check: Department heads can only approve requests from their own department
        if (
            !auth()->user()->canViewAllDepartments() &&
            $supplyRequest->department_id !== auth()->user()->department_id
        ) {
            return response()->json(['error' => 'You can only approve requests from your own department.'], 403);
        }

        $supplyRequest->update([
            'status' => 'pending_ga_admin',
            'department_head_approved_by' => auth()->id(),
            'department_head_approved_at' => now(),
        ]);

        return response()->json(['message' => 'Supply request approved by department head successfully.']);
    }

    public function rejectDeptHead(Request $request, SupplyRequest $supplyRequest)
    {
        if (!$supplyRequest->canBeDeptHeadRejected()) {
            return response()->json(['error' => 'Request cannot be rejected by department head.'], 400);
        }

        // Security check: Department heads can only reject requests from their own department
        if (
            !auth()->user()->canViewAllDepartments() &&
            $supplyRequest->department_id !== auth()->user()->department_id
        ) {
            return response()->json(['error' => 'You can only reject requests from your own department.'], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $supplyRequest->update([
            'status' => 'rejected',
            'department_head_approved_by' => auth()->id(),
            'department_head_approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'Supply request rejected by department head successfully.']);
    }

    public function approveGAAdmin(Request $request, SupplyRequest $supplyRequest)
    {
        if (!$supplyRequest->canBeGAAdminApproved()) {
            return response()->json(['error' => 'Request cannot be approved by GA admin.'], 400);
        }

        $request->validate([
            'approved_quantities' => 'required|array',
            'approved_quantities.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update approved quantities for each item
            foreach ($supplyRequest->items as $index => $item) {
                $approvedQuantity = $request->approved_quantities[$index] ?? 0;

                if ($approvedQuantity > $item->quantity) {
                    throw new \Exception("Approved quantity ({$approvedQuantity}) cannot exceed requested quantity ({$item->quantity}) for item: {$item->supply->name}");
                }

                $item->update(['approved_quantity' => $approvedQuantity]);
            }

            $supplyRequest->update([
                'status' => 'approved',
                'ga_admin_approved_by' => auth()->id(),
                'ga_admin_approved_at' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Supply request approved by GA admin successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to approve request: ' . $e->getMessage()], 500);
        }
    }

    public function rejectGAAdmin(Request $request, SupplyRequest $supplyRequest)
    {
        if (!$supplyRequest->canBeGAAdminRejected()) {
            return response()->json(['error' => 'Request cannot be rejected by GA admin.'], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $supplyRequest->update([
            'status' => 'rejected',
            'ga_admin_approved_by' => auth()->id(),
            'ga_admin_approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json(['message' => 'Supply request rejected by GA admin successfully.']);
    }
}
