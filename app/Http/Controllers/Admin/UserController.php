<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware will be applied in routes instead
    }

    /**
     * Get the authenticated user
     */
    private function getAuthUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check permission
        if (!$this->getAuthUser()->can('view users')) {
            abort(403, 'You do not have permission to view users.');
        }

        $query = User::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('department_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $role = $request->get('role');
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->get('department'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $users = $query->with('department')->orderBy('name')->paginate(15);
        $users->appends($request->query());

        // Get departments for filter dropdown
        $departments = Department::active()->orderBy('department_name')->get();

        return view('admin.users.index', compact('users', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->getAuthUser()->can('create users')) {
            abort(403, 'You do not have permission to create users.');
        }

        $departments = Department::active()->orderBy('department_name')->get();
        $roles = \Spatie\Permission\Models\Role::all()->pluck('name', 'name');

        return view('admin.users.create', compact('departments', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if (!$this->getAuthUser()->can('create users')) {
            abort(403, 'You do not have permission to create users.');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'nik' => $request->nik,
                'department_id' => $request->department_id,
                'phone' => $request->phone,
                'project' => $request->project,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Log the activity
            Log::info('User created', [
                'user_id' => $user->id,
                'created_by' => Auth::id(),
                'user_email' => $user->email,
                'role' => $request->role,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'created_by' => Auth::id(),
            ]);

            return back()->with('error', 'Failed to create user. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (!$this->getAuthUser()->can('view users')) {
            abort(403, 'You do not have permission to view users.');
        }

        // Log the activity
        Log::info('User profile viewed', [
            'viewed_user_id' => $user->id,
            'viewed_by' => Auth::id(),
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!$this->getAuthUser()->can('edit users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        $departments = Department::active()->orderBy('department_name')->get();
        $roles = \Spatie\Permission\Models\Role::all()->pluck('name', 'name');

        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if (!$this->getAuthUser()->can('edit users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'nik' => $request->nik,
                'department_id' => $request->department_id,
                'phone' => $request->phone,
                'project' => $request->project,
                'is_active' => $request->boolean('is_active', true),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Update role
            $user->syncRoles([$request->role]);

            // Log the activity
            Log::info('User updated', [
                'user_id' => $user->id,
                'updated_by' => Auth::id(),
                'changes' => $updateData,
                'role' => $request->role,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'error' => $e->getMessage(),
                'updated_by' => Auth::id(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!$this->getAuthUser()->can('delete users')) {
            abort(403, 'You do not have permission to delete users.');
        }

        try {
            // Prevent self-deletion
            if ($user->id === Auth::id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $userEmail = $user->email;
            $user->delete();

            // Log the activity
            Log::info('User deleted', [
                'deleted_user_email' => $userEmail,
                'deleted_by' => Auth::id(),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'error' => $e->getMessage(),
                'deleted_by' => Auth::id(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        if (!$this->getAuthUser()->can('edit users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        try {
            // Prevent self-deactivation
            if ($user->id === Auth::id()) {
                return back()->with('error', 'You cannot deactivate your own account.');
            }

            $user->update(['is_active' => !$user->is_active]);

            // Log the activity
            Log::info('User status toggled', [
                'user_id' => $user->id,
                'new_status' => $user->is_active ? 'active' : 'inactive',
                'toggled_by' => Auth::id(),
            ]);

            $status = $user->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "User {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Error toggling user status', [
                'error' => $e->getMessage(),
                'toggled_by' => Auth::id(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Failed to update user status. Please try again.');
        }
    }
}
