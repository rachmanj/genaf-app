@extends('layouts.main')

@section('title', 'Rooms')

@section('title_page')
    Rooms
@endsection

@push('js')
<script>
    $(function(){
        const $building = $('select[name="building_id"]');
        const $status = $('select[name="status"]');

        // Initialize status with Select2
        $status.select2({ theme: 'bootstrap4', width: '100%' });

        // Initialize building with AJAX Select2
        $building.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'All',
            allowClear: true,
            ajax: {
                url: '{{ route('pms.buildings.search') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return data; // already in { results: [{id,text},...] }
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });
</script>
@endpush

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Rooms</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-bed mr-1"></i> Rooms</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.rooms.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Room
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <label>Building</label>
                                        <select name="building_id" class="form-control form-control-sm">
                                            <option value="">All</option>
                                            @foreach ($buildings as $b)
                                                <option value="{{ $b->id }}" {{ ($filters['building_id'] ?? '')==$b->id ? 'selected':'' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="">All</option>
                                            <option value="available" {{ ($filters['status'] ?? '')==='available'?'selected':'' }}>Available</option>
                                            <option value="occupied" {{ ($filters['status'] ?? '')==='occupied'?'selected':'' }}>Occupied</option>
                                            <option value="maintenance" {{ ($filters['status'] ?? '')==='maintenance'?'selected':'' }}>Maintenance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Type</label>
                                        <input type="text" name="room_type" value="{{ $filters['room_type'] ?? '' }}" class="form-control form-control-sm" placeholder="e.g., Standard, Deluxe">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Floor</label>
                                        <input type="number" name="floor" value="{{ $filters['floor'] ?? '' }}" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2 align-self-end">
                                        <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-filter"></i> Filter</button>
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('pms.rooms.index') }}">Clear</a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Building</th>
                                            <th>Number</th>
                                            <th>Type</th>
                                            <th>Floor</th>
                                            <th>Capacity</th>
                                            <th>Status</th>
                                            <th class="text-right">Rate</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rooms as $i => $r)
                                            <tr>
                                                <td>{{ $rooms->firstItem() + $i }}</td>
                                                <td>{{ $r->building?->name }}</td>
                                                <td>{{ $r->room_number }}</td>
                                                <td>{{ $r->room_type }}</td>
                                                <td>{{ $r->floor }}</td>
                                                <td>{{ $r->capacity }}</td>
                                                <td><span class="badge badge-{{ $r->status==='available' ? 'success' : ($r->status==='occupied' ? 'warning' : 'secondary') }}">{{ ucfirst($r->status) }}</span></td>
                                                <td class="text-right">{{ number_format($r->daily_rate, 2) }}</td>
                                                <td>
                                                    @if($r->is_active)
                                                        <span class="badge badge-success">Yes</span>
                                                    @else
                                                        <span class="badge badge-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-info" href="{{ route('pms.rooms.show', $r) }}"><i class="fas fa-eye"></i></a>
                                                    <a class="btn btn-sm btn-warning" href="{{ route('pms.rooms.edit', $r) }}"><i class="fas fa-edit"></i></a>
                                                    <form method="POST" action="{{ route('pms.rooms.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Delete this room?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No rooms found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $rooms->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


