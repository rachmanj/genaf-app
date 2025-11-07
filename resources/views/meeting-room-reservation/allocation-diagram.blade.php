@extends('layouts.main')

@section('title_page', 'Room Allocation Diagram')
@section('breadcrumb_title', 'Room Allocation Diagram')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Meeting Room Allocation Diagram</h3>
                            <div class="card-tools">
                                <form method="GET" action="{{ route('meeting-rooms.allocation-diagram') }}" class="d-inline">
                                    <input type="date" name="date" id="diagram-date" value="{{ $date }}" class="form-control form-control-sm d-inline-block" style="width: auto;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search"></i> View
                                    </button>
                                </form>
                                <a href="{{ route('meeting-rooms.reservations.index') }}" class="btn btn-secondary btn-sm ml-2">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 150px;">Time</th>
                                            @foreach($meetingRooms as $room)
                                                <th>{{ $room->name }}<br><small class="text-muted">({{ $room->location }}, Capacity: {{ $room->capacity }})</small></th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Generate time slots (8:00 to 18:00)
                                            $timeSlots = [];
                                            for ($hour = 8; $hour <= 18; $hour++) {
                                                $timeSlots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                                            }
                                        @endphp
                                        @foreach($timeSlots as $timeSlot)
                                            <tr>
                                                <td><strong>{{ $timeSlot }}</strong></td>
                                                @foreach($meetingRooms as $room)
                                                    @php
                                                        $timeSlotDate = \Carbon\Carbon::parse($date . ' ' . $timeSlot);
                                                        $roomReservations = $reservations->filter(function($res) use ($room, $date, $timeSlotDate) {
                                                            if ($res->allocated_room_id != $room->id) return false;
                                                            if ($res->status == 'cancelled' || $res->status == 'rejected') return false;
                                                            
                                                            $resStart = \Carbon\Carbon::parse($res->meeting_date_start . ' ' . $res->meeting_time_start);
                                                            $resEnd = \Carbon\Carbon::parse($res->meeting_date_start . ' ' . $res->meeting_time_end);
                                                            if ($res->meeting_date_end) {
                                                                $resEnd = \Carbon\Carbon::parse($res->meeting_date_end . ' ' . $res->meeting_time_end);
                                                            }
                                                            
                                                            // Check if current time slot overlaps with reservation
                                                            $slotStart = $timeSlotDate->copy();
                                                            $slotEnd = $timeSlotDate->copy()->addHour();
                                                            
                                                            return $slotStart < $resEnd && $slotEnd > $resStart;
                                                        });
                                                    @endphp
                                                    <td class="text-center align-middle" style="height: 60px;">
                                                        @if($roomReservations->count() > 0)
                                                            @foreach($roomReservations as $res)
                                                                @php
                                                                    $badgeClass = match ($res->status) {
                                                                        'pending_dept_head' => 'warning',
                                                                        'pending_ga_admin' => 'info',
                                                                        'approved' => 'success',
                                                                        'confirmed' => 'primary',
                                                                        default => 'secondary'
                                                                    };
                                                                @endphp
                                                                <div class="badge badge-{{ $badgeClass }} mb-1" style="font-size: 0.75rem; display: block;">
                                                                    {{ $res->meeting_title }}<br>
                                                                    <small>{{ \Carbon\Carbon::parse($res->meeting_time_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($res->meeting_time_end)->format('H:i') }}</small><br>
                                                                    <small>{{ $res->requestor->name ?? 'N/A' }}</small>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Available</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h5>Legend:</h5>
                                    <span class="badge badge-warning">Pending Dept Head</span>
                                    <span class="badge badge-info">Pending GA Admin</span>
                                    <span class="badge badge-success">Approved</span>
                                    <span class="badge badge-primary">Confirmed</span>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <h5>Reservations for {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</h5>
                                    @if($reservations->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Room</th>
                                                        <th>Meeting Title</th>
                                                        <th>Time</th>
                                                        <th>Requestor</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reservations as $res)
                                                        <tr>
                                                            <td>{{ $res->allocatedRoom->name ?? 'N/A' }}</td>
                                                            <td>{{ $res->meeting_title }}</td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($res->meeting_time_start)->format('H:i') }} - 
                                                                {{ \Carbon\Carbon::parse($res->meeting_time_end)->format('H:i') }}
                                                            </td>
                                                            <td>{{ $res->requestor->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @php
                                                                    $badgeClass = match ($res->status) {
                                                                        'pending_dept_head' => 'warning',
                                                                        'pending_ga_admin' => 'info',
                                                                        'approved' => 'success',
                                                                        'confirmed' => 'primary',
                                                                        'rejected' => 'danger',
                                                                        default => 'secondary'
                                                                    };
                                                                @endphp
                                                                <span class="badge badge-{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $res->status)) }}</span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('meeting-rooms.reservations.show', $res) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No reservations for this date</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Set today's date as default if not set
            if (!$('#diagram-date').val()) {
                $('#diagram-date').val('{{ date('Y-m-d') }}');
            }
        });
    </script>
@endpush
