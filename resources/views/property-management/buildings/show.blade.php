@extends('layouts.main')

@section('title', 'Building Details')

@section('title_page')
    Building Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-city mr-1"></i> {{ $building->name }} ({{ $building->code }})</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.buildings.edit', $building) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Code</th><td>{{ $building->code }}</td></tr>
                                        <tr><th>Name</th><td>{{ $building->name }}</td></tr>
                                        <tr><th>Status</th><td><span class="badge badge-{{ $building->status==='active'?'success':'secondary' }}">{{ ucfirst($building->status) }}</span></td></tr>
                                        <tr><th>City</th><td>{{ $building->city }}</td></tr>
                                        <tr><th>Province</th><td>{{ $building->province }}</td></tr>
                                        <tr><th>Country</th><td>{{ $building->country }}</td></tr>
                                        <tr><th>Postal Code</th><td>{{ $building->postal_code }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Address</th><td>{{ $building->address_line1 }} {{ $building->address_line2 }}</td></tr>
                                        <tr><th>Timezone</th><td>{{ $building->timezone }}</td></tr>
                                        <tr><th>Latitude</th><td>{{ $building->latitude }}</td></tr>
                                        <tr><th>Longitude</th><td>{{ $building->longitude }}</td></tr>
                                        <tr><th>Contact</th><td>{{ $building->contact_name }} ({{ $building->contact_phone }})</td></tr>
                                    </table>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Rooms</h5>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-percent"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Occupancy</span>
                                            @php
                                                $totalRooms = $building->rooms->count();
                                                $occupied = $building->rooms->where('status','occupied')->count();
                                                $occupancy = $totalRooms ? round(($occupied/$totalRooms)*100) : 0;
                                            @endphp
                                            <span class="info-box-number">{{ $occupancy }}%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-wrench"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Maint. Cost (30d)</span>
                                            @php
                                                $maintCost = 0;
                                                foreach($building->rooms as $r){
                                                    foreach(($r->maintenances ?? []) as $m){
                                                        if($m->completed_date && \Illuminate\Support\Carbon::parse($m->completed_date) >= now()->subDays(30)){
                                                            $maintCost += (float)$m->cost;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <span class="info-box-number">IDR {{ number_format($maintCost, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Number</th>
                                            <th>Type</th>
                                            <th>Floor</th>
                                            <th>Capacity</th>
                                            <th>Status</th>
                                            <th class="text-right">Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($building->rooms as $i => $r)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $r->room_number }}</td>
                                                <td>{{ $r->room_type }}</td>
                                                <td>{{ $r->floor }}</td>
                                                <td>{{ $r->capacity }}</td>
                                                <td><span class="badge badge-{{ $r->status==='available' ? 'success' : ($r->status==='occupied' ? 'warning' : 'secondary') }}">{{ ucfirst($r->status) }}</span></td>
                                                <td class="text-right">{{ number_format($r->daily_rate, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No rooms available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


