@extends('layouts.main')

@section('title_page')
    Dashboard
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        @can('view supplies')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ \App\Models\Supply::count() }}</h3>
                        <p>Office Supplies</p>
                    </div>
                    <div class="icon"><i class="fas fa-box"></i></div>
                    <a href="{{ route('supplies.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endcan

        @can('view users')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ \App\Models\User::count() }}</h3>
                        <p>Users</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endcan

        @can('view departments')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ \App\Models\Department::count() }}</h3>
                        <p>Departments</p>
                    </div>
                    <div class="icon"><i class="fas fa-building"></i></div>
                    <a href="{{ route('departments.index') }}" class="small-box-footer">More info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endcan

        @can('view vehicles')
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ \App\Models\Vehicle::count() }}</h3>
                        <p>Vehicles</p>
                    </div>
                    <div class="icon"><i class="fas fa-car"></i></div>
                    <a href="{{ route('vehicles.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endcan
    </div>

    @can('view vehicles')
        <div class="row">
            <div class="col-lg-4 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ \App\Models\VehicleDocument::expiringWithin(90)->count() }}</h3>
                        <p>Documents expiring in 90 days</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-alt"></i></div>
                    <a href="{{ route('vehicles.index') }}" class="small-box-footer">Review <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ \App\Models\VehicleMaintenance::upcoming(30)->count() }}</h3>
                        <p>Upcoming services (30 days)</p>
                    </div>
                    <div class="icon"><i class="fas fa-tools"></i></div>
                    <a href="{{ route('vehicle-maintenance.index') }}" class="small-box-footer">Review <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    @endcan

    <!-- Recent Supply Requests -->
    @can('view supply requests')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Recent Supply Requests
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Department</th>
                                    <th>Requestor</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\SupplyRequest::with(['employee', 'department'])->latest()->take(10)->get() as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->department->name ?? 'N/A' }}</td>
                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                        <td>{{ $request->items->count() }} items</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('supplies.requests.show', $request) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No supply requests yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    @if (session('status'))
        <script>
            toastr.success(@json(session('status')));
        </script>
    @endif
@endpush
