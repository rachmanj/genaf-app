@extends('layouts.main')

@section('title', 'Room Details')

@section('title_page')
    Room Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.rooms.index') }}">Rooms</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-bed mr-1"></i> Room {{ $room->room_number }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.rooms.edit', $room) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Building</th><td>{{ $room->building?->name }}</td></tr>
                                        <tr><th>Number</th><td>{{ $room->room_number }}</td></tr>
                                        <tr><th>Type</th><td>{{ $room->room_type }}</td></tr>
                                        <tr><th>Floor</th><td>{{ $room->floor }}</td></tr>
                                        <tr><th>Capacity</th><td>{{ $room->capacity }}</td></tr>
                                        <tr><th>Status</th><td><span class="badge badge-{{ $room->status==='available'?'success':($room->status==='occupied'?'warning':'secondary') }}">{{ ucfirst($room->status) }}</span></td></tr>
                                        <tr><th>Active</th><td>{{ $room->is_active ? 'Yes' : 'No' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Daily Rate</th><td>{{ number_format($room->daily_rate, 2) }}</td></tr>
                                        <tr><th>Description</th><td>{{ $room->description }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3">Current Reservation</h5>
                            @if($room->current_reservation)
                                <p>There is an active stay for this room.</p>
                            @else
                                <p>No active stay.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


