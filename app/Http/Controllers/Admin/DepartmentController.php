<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::withCount(['users', 'supplyRequests'])
                ->select('departments.*');

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($department) {
                    $badgeClass = $department->status ? 'badge-success' : 'badge-danger';
                    $statusText = $department->status ? 'Active' : 'Inactive';
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('users_count', function ($department) {
                    return $department->users_count;
                })
                ->addColumn('requests_count', function ($department) {
                    return $department->supply_requests_count;
                })
                ->addColumn('actions', function ($department) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('departments.show', $department->id) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                    $actions .= '<a href="' . route('departments.edit', $department->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                    $actions .= '<button type="button" class="btn btn-' . ($department->status ? 'danger' : 'success') . ' btn-sm toggle-status" data-id="' . $department->id . '" data-status="' . $department->status . '">';
                    $actions .= '<i class="fas fa-' . ($department->status ? 'times' : 'check') . '"></i>';
                    $actions .= '</button>';
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.departments.index');
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments',
        ]);

        $department = Department::create([
            'department_name' => $request->department_name,
            'slug' => Department::generateSlug($request->department_name),
            'status' => true,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['users', 'supplyRequests.employee', 'supplyDistributions.supply']);
        
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $department->id,
        ]);

        $department->update([
            'department_name' => $request->department_name,
            'slug' => Department::generateSlug($request->department_name),
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function toggleStatus(Request $request, Department $department)
    {
        $department->update([
            'status' => !$department->status,
        ]);

        $status = $department->status ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Department {$status} successfully.",
            'status' => $department->status,
        ]);
    }
}