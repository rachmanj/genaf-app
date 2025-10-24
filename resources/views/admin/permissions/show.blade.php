@extends('layouts.main')

@section('title_page')
    Permission Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
    <li class="breadcrumb-item active">{{ $permission->name }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-key mr-1"></i>
                            {{ ucfirst(str_replace('-', ' ', $permission->name)) }} Permission
                        </h3>
                        <div class="card-tools">
                            @can('edit permissions')
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-tool btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endcan
                            @can('delete permissions')
                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-tool btn-sm text-danger"
                                        onclick="return confirm('Are you sure you want to delete this permission?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Permission ID:</dt>
                                    <dd class="col-sm-8">{{ $permission->id }}</dd>

                                    <dt class="col-sm-4">Permission Name:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-primary">{{ $permission->name }}</span>
                                    </dd>

                                    <dt class="col-sm-4">Display Name:</dt>
                                    <dd class="col-sm-8">
                                        {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                    </dd>

                                    <dt class="col-sm-4">Roles Count:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-success">{{ $permission->roles->count() }}</span>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h5>Roles with this Permission</h5>
                                @if ($permission->roles->count() > 0)
                                    <div class="list-group">
                                        @foreach ($permission->roles as $role)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ ucfirst($role->name) }}</h6>
                                                    <small>{{ $role->users->count() }} users</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No roles assigned to this permission.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Permissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
