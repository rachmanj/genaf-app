<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('view permissions')) {
            abort(403, 'You do not have permission to view permissions.');
        }

        if ($request->ajax()) {
            $permissions = Permission::withCount('roles');

            return DataTables::of($permissions)
                ->addColumn('roles_count', function ($permission) {
                    return '<span class="badge badge-success">' . $permission->roles_count . '</span>';
                })
                ->addColumn('actions', function ($permission) {
                    $html = '';

                    if (Auth::user()->can('edit permissions')) {
                        $html .= '<button class="btn btn-sm btn-warning mr-1 edit-permission" data-id="' . $permission->id . '" data-name="' . $permission->name . '" title="Edit">';
                        $html .= '<i class="fas fa-edit"></i></button>';
                    }

                    if (Auth::user()->can('delete permissions')) {
                        $html .= '<button class="btn btn-sm btn-danger delete-permission" data-id="' . $permission->id . '" title="Delete">';
                        $html .= '<i class="fas fa-trash"></i></button>';
                    }

                    return $html;
                })
                ->rawColumns(['roles_count', 'actions'])
                ->make(true);
        }

        return view('admin.permissions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->can('create permissions')) {
            abort(403, 'You do not have permission to create permissions.');
        }

        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create permissions')) {
            abort(403, 'You do not have permission to create permissions.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        try {
            $permission = Permission::create(['name' => $request->name]);

            Log::info('Permission created', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating permission', [
                'error' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to create permission. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        if (!Auth::user()->can('view permissions')) {
            abort(403, 'You do not have permission to view permissions.');
        }

        $permission->load('roles');

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        if (!Auth::user()->can('edit permissions')) {
            abort(403, 'You do not have permission to edit permissions.');
        }

        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        if (!Auth::user()->can('edit permissions')) {
            abort(403, 'You do not have permission to edit permissions.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id
        ]);

        try {
            $permission->update(['name' => $request->name]);

            Log::info('Permission updated', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating permission', [
                'error' => $e->getMessage(),
                'updated_by' => Auth::id(),
                'permission_id' => $permission->id,
            ]);

            return back()->with('error', 'Failed to update permission. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        if (!Auth::user()->can('delete permissions')) {
            abort(403, 'You do not have permission to delete permissions.');
        }

        try {
            $permissionName = $permission->name;
            $permission->delete();

            Log::info('Permission deleted', [
                'permission_name' => $permissionName,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting permission', [
                'error' => $e->getMessage(),
                'deleted_by' => Auth::id(),
                'permission_id' => $permission->id,
            ]);

            return back()->with('error', 'Failed to delete permission. Please try again.');
        }
    }
}
