@extends('layouts.main')

@section('title', 'Buildings')

@section('title_page')
    Buildings
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Buildings</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-city mr-1"></i> Buildings</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.buildings.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Building
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <label>Status</label>
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="">All</option>
                                            <option value="active" {{ ($filters['status'] ?? '')==='active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ ($filters['status'] ?? '')==='inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>City</label>
                                        <input type="text" name="city" class="form-control form-control-sm" value="{{ $filters['city'] ?? '' }}" placeholder="Search city...">
                                    </div>
                                    <div class="col-md-3 align-self-end">
                                        <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-filter"></i> Filter</button>
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('pms.buildings.index') }}">Clear</a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>City</th>
                                            <th>Status</th>
                                            <th class="text-right">Rooms</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($buildings as $i => $b)
                                            <tr>
                                                <td>{{ $buildings->firstItem() + $i }}</td>
                                                <td>{{ $b->code }}</td>
                                                <td>{{ $b->name }}</td>
                                                <td>{{ $b->city }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $b->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($b->status) }}</span>
                                                </td>
                                                <td class="text-right">{{ $b->rooms_count }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-info" href="{{ route('pms.buildings.show', $b) }}"><i class="fas fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('pms.buildings.edit', $b) }}"><i class="fas fa-edit"></i></a>
                                                    <form method="POST" action="{{ route('pms.buildings.destroy', $b) }}" class="d-inline" onsubmit="return confirm('Delete this building?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No buildings found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $buildings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


