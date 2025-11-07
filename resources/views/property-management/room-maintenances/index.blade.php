@extends('layouts.main')

@section('title', 'Room Maintenances')

@section('title_page')
    Room Maintenances
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Room Maintenances</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Room Maintenances</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.maintenances.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Schedule Maintenance
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('pms.maintenances.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Building</label>
                                            <select name="building_id" class="form-control select2" style="width: 100%;">
                                                <option value="">All Buildings</option>
                                                @foreach ($buildings as $building)
                                                    <option value="{{ $building->id }}" {{ $filters['building_id'] == $building->id ? 'selected' : '' }}>
                                                        {{ $building->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="">All Statuses</option>
                                                <option value="scheduled" {{ $filters['status'] == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                <option value="in_progress" {{ $filters['status'] == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $filters['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Maintenance Type</label>
                                            <input type="text" name="maintenance_type" class="form-control" value="{{ $filters['maintenance_type'] }}" placeholder="Search type...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <a href="{{ route('pms.maintenances.index') }}" class="btn btn-secondary btn-block">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Form Number</th>
                                            <th>Building</th>
                                            <th>Room</th>
                                            <th>Type</th>
                                            <th>Scheduled Date</th>
                                            <th>Completed Date</th>
                                            <th>Status</th>
                                            <th>Cost</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($maintenances as $i => $maint)
                                            <tr>
                                                <td>{{ $maintenances->firstItem() + $i }}</td>
                                                <td>{{ $maint->form_number }}</td>
                                                <td>{{ $maint->room?->building?->name }}</td>
                                                <td>{{ $maint->room?->room_number }}</td>
                                                <td>{{ $maint->maintenance_type }}</td>
                                                <td>{{ $maint->scheduled_date->format('Y-m-d') }}</td>
                                                <td>{{ $maint->completed_date ? $maint->completed_date->format('Y-m-d') : '-' }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'scheduled' => 'info',
                                                            'in_progress' => 'warning',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger',
                                                        ];
                                                        $color = $statusColors[$maint->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $maint->status)) }}</span>
                                                </td>
                                                <td>{{ number_format($maint->cost, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('pms.maintenances.show', $maint) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pms.maintenances.edit', $maint) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($maint->status !== 'completed')
                                                        <form method="POST" action="{{ route('pms.maintenances.complete', $maint) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Mark Complete" onclick="return confirm('Mark this maintenance as completed?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('pms.maintenances.destroy', $maint) }}" class="d-inline" onsubmit="return confirm('Delete this maintenance?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No maintenances found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $maintenances->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4' });

        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@endpush

