<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
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
        if (!Auth::user()->can('view roles')) {
            abort(403, 'You do not have permission to view roles.');
        }

        if ($request->ajax()) {
            $roles = Role::withCount('users')->with('permissions');

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('permissions', function ($role) {
                    $permissions = $role->permissions->pluck('name')->toArray();
                    $permissionCount = count($permissions);

                    if ($permissionCount <= 3) {
                        return collect($permissions)->map(function ($permission) {
                            return '<span class="badge badge-info mr-1 mb-1">' . ucfirst(str_replace('-', ' ', $permission)) . '</span>';
                        })->join(' ');
                    }

                    $displayPermissions = array_slice($permissions, 0, 3);
                    $remaining = $permissionCount - 3;

                    $html = collect($displayPermissions)->map(function ($permission) {
                        return '<span class="badge badge-info mr-1 mb-1">' . ucfirst(str_replace('-', ' ', $permission)) . '</span>';
                    })->join(' ');

                    $html .= '<span class="badge badge-secondary">+' . $remaining . ' more</span>';

                    return $html;
                })
                ->addColumn('users_count', function ($role) {
                    return '<span class="badge badge-success">' . $role->users_count . '</span>';
                })
                ->addColumn('actions', function ($role) {
                    $html = '';

                    if (Auth::user()->can('view roles')) {
                        $html .= '<a href="' . route('admin.roles.show', $role->id) . '" class="btn btn-sm btn-info mr-1" title="View">';
                        $html .= '<i class="fas fa-eye"></i></a>';
                    }

                    if (Auth::user()->can('edit roles')) {
                        $html .= '<a href="' . route('admin.roles.edit', $role->id) . '" class="btn btn-sm btn-warning mr-1" title="Edit">';
                        $html .= '<i class="fas fa-edit"></i></a>';
                    }

                    if (Auth::user()->can('delete roles') && $role->name !== 'superadmin') {
                        $html .= '<button class="btn btn-sm btn-danger delete-role" data-id="' . $role->id . '" title="Delete">';
                        $html .= '<i class="fas fa-trash"></i></button>';
                    }

                    return $html;
                })
                ->rawColumns(['permissions', 'users_count', 'actions'])
                ->make(true);
        }

        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->can('create roles')) {
            abort(403, 'You do not have permission to create roles.');
        }

        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return $this->getPermissionModule($permission->name);
        });

        // Create icon mapping for modules
        $moduleIcons = [
            'User Management' => 'fa-users',
            'Role Management' => 'fa-user-shield',
            'Permission Management' => 'fa-key',
            'Office Supplies' => 'fa-boxes',
            'Ticket Reservations' => 'fa-ticket-alt',
            'Property Management' => 'fa-bed',
            'Vehicle Administration' => 'fa-car',
            'Asset Inventory' => 'fa-laptop',
            'Reports & Analytics' => 'fa-chart-bar',
            'System Settings' => 'fa-cog',
            'Other' => 'fa-folder',
        ];

        return view('admin.roles.create', compact('groupedPermissions', 'moduleIcons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create roles')) {
            abort(403, 'You do not have permission to create roles.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            Log::info('Role created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'created_by' => Auth::id(),
                'permissions_count' => count($request->permissions ?? [])
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating role', [
                'error' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to create role. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if (!Auth::user()->can('view roles')) {
            abort(403, 'You do not have permission to view roles.');
        }

        $role->load(['permissions', 'users']);

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!Auth::user()->can('edit roles')) {
            abort(403, 'You do not have permission to edit roles.');
        }

        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return $this->getPermissionModule($permission->name);
        });

        $role->load('permissions');

        // Create icon mapping for modules
        $moduleIcons = [
            'User Management' => 'fa-users',
            'Role Management' => 'fa-user-shield',
            'Permission Management' => 'fa-key',
            'Office Supplies' => 'fa-boxes',
            'Ticket Reservations' => 'fa-ticket-alt',
            'Property Management' => 'fa-bed',
            'Vehicle Administration' => 'fa-car',
            'Asset Inventory' => 'fa-laptop',
            'Reports & Analytics' => 'fa-chart-bar',
            'System Settings' => 'fa-cog',
            'Other' => 'fa-folder',
        ];

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'moduleIcons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if (!Auth::user()->can('edit roles')) {
            abort(403, 'You do not have permission to edit roles.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role->update(['name' => $request->name]);

            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            Log::info('Role updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'updated_by' => Auth::id(),
                'permissions_count' => count($request->permissions ?? [])
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating role', [
                'error' => $e->getMessage(),
                'updated_by' => Auth::id(),
                'role_id' => $role->id,
            ]);

            return back()->with('error', 'Failed to update role. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!Auth::user()->can('delete roles')) {
            abort(403, 'You do not have permission to delete roles.');
        }

        if ($role->name === 'superadmin') {
            return back()->with('error', 'Cannot delete superadmin role.');
        }

        try {
            $roleName = $role->name;
            $role->delete();

            Log::info('Role deleted', [
                'role_name' => $roleName,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting role', [
                'error' => $e->getMessage(),
                'deleted_by' => Auth::id(),
                'role_id' => $role->id,
            ]);

            return back()->with('error', 'Failed to delete role. Please try again.');
        }
    }

    /**
     * Get the module name for a permission based on its name
     */
    private function getPermissionModule($permissionName)
    {
        $moduleMap = [
            'view users' => 'User Management',
            'create users' => 'User Management',
            'edit users' => 'User Management',
            'delete users' => 'User Management',
            'toggle user status' => 'User Management',

            'view roles' => 'Role Management',
            'create roles' => 'Role Management',
            'edit roles' => 'Role Management',
            'delete roles' => 'Role Management',

            'view permissions' => 'Permission Management',
            'create permissions' => 'Permission Management',
            'edit permissions' => 'Permission Management',
            'delete permissions' => 'Permission Management',

            'view supplies' => 'Office Supplies',
            'create supplies' => 'Office Supplies',
            'edit supplies' => 'Office Supplies',
            'delete supplies' => 'Office Supplies',
            'view supply requests' => 'Office Supplies',
            'create supply requests' => 'Office Supplies',
            'edit supply requests' => 'Office Supplies',
            'approve supply requests' => 'Office Supplies',

            'view ticket reservations' => 'Ticket Reservations',
            'create ticket reservations' => 'Ticket Reservations',
            'edit ticket reservations' => 'Ticket Reservations',
            'delete ticket reservations' => 'Ticket Reservations',
            'approve ticket reservations' => 'Ticket Reservations',

            'view rooms' => 'Property Management',
            'create rooms' => 'Property Management',
            'edit rooms' => 'Property Management',
            'delete rooms' => 'Property Management',
            'view room reservations' => 'Property Management',
            'create room reservations' => 'Property Management',
            'edit room reservations' => 'Property Management',
            'approve room reservations' => 'Property Management',

            'view vehicles' => 'Vehicle Administration',
            'create vehicles' => 'Vehicle Administration',
            'edit vehicles' => 'Vehicle Administration',
            'delete vehicles' => 'Vehicle Administration',
            'view fuel records' => 'Vehicle Administration',
            'create fuel records' => 'Vehicle Administration',
            'edit fuel records' => 'Vehicle Administration',
            'view vehicle maintenance' => 'Vehicle Administration',
            'create vehicle maintenance' => 'Vehicle Administration',
            'edit vehicle maintenance' => 'Vehicle Administration',

            'view assets' => 'Asset Inventory',
            'create assets' => 'Asset Inventory',
            'edit assets' => 'Asset Inventory',
            'delete assets' => 'Asset Inventory',
            'view asset maintenance' => 'Asset Inventory',
            'create asset maintenance' => 'Asset Inventory',
            'edit asset maintenance' => 'Asset Inventory',
            'view asset transfers' => 'Asset Inventory',
            'create asset transfers' => 'Asset Inventory',
            'approve asset transfers' => 'Asset Inventory',

            'view reports' => 'Reports & Analytics',
            'export reports' => 'Reports & Analytics',

            'view system settings' => 'System Settings',
            'edit system settings' => 'System Settings',
        ];

        return $moduleMap[$permissionName] ?? 'Other';
    }

    /**
     * Get the icon for a module
     */
    public function getModuleIcon($moduleName)
    {
        $iconMap = [
            'User Management' => 'fa-users',
            'Role Management' => 'fa-user-shield',
            'Permission Management' => 'fa-key',
            'Office Supplies' => 'fa-boxes',
            'Ticket Reservations' => 'fa-ticket-alt',
            'Property Management' => 'fa-bed',
            'Vehicle Administration' => 'fa-car',
            'Asset Inventory' => 'fa-laptop',
            'Reports & Analytics' => 'fa-chart-bar',
            'System Settings' => 'fa-cog',
            'Other' => 'fa-folder',
        ];

        return $iconMap[$moduleName] ?? 'fa-folder';
    }
}
