@extends('layouts.main')

@section('title_page')
    User Management
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">User Management</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-1"></i>
                            User Management
                        </h3>
                        <div class="card-tools">
                            @can('create users')
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i>
                                    Add New User
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Name, email, or department..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="role" class="form-control">
                                            <option value="">All Roles</option>
                                            @foreach (['admin', 'manager', 'employee'] as $role)
                                                <option value="{{ $role }}"
                                                    {{ request('role') == $role ? 'selected' : '' }}>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department" class="form-control">
                                            <option value="">All Departments</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}"
                                                    {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Clear
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Users Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3">
                                                        {{ substr($user->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">{{ $user->name }}</div>
                                                        <small class="text-muted">{{ $user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach ($user->roles as $role)
                                                    <span class="badge badge-info">{{ ucfirst($role->name) }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $user->department?->department_name ?? 'N/A' }}</td>
                                            <td>{{ $user->project ?? 'N/A' }}</td>
                                            <td>
                                                @if ($user->is_active)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @can('view users')
                                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info"
                                                            title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('edit users')
                                                        <a href="{{ route('users.edit', $user) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('edit users')
                                                        <form action="{{ route('users.toggle-status', $user) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-secondary"
                                                                title="Toggle Status">
                                                                <i class="fas fa-toggle-on"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('delete users')
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted">
                                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }}
                                    of {{ $users->total() }} users
                                </small>
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            // Handle session notifications with Toastr
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
