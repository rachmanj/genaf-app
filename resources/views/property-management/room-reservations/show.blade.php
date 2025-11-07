@extends('layouts.main')

@section('title', 'Reservation Details')

@section('title_page')
    Reservation Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pms.reservations.index') }}">Reservations</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Reservation #{{ $reservation->form_number }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                        <div class="card-body" id="printable-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Form Number</th><td>{{ $reservation->form_number }}</td></tr>
                                        <tr><th>Building</th><td>{{ $reservation->room?->building?->name }}</td></tr>
                                        <tr><th>Room</th><td>{{ $reservation->room?->room_number }} ({{ $reservation->room?->room_type }})</td></tr>
                                        <tr><th>Guest Name</th><td>{{ $reservation->guest_name }}</td></tr>
                                        <tr><th>Company</th><td>{{ $reservation->company ?? '-' }}</td></tr>
                                        <tr><th>Phone</th><td>{{ $reservation->phone }}</td></tr>
                                        <tr><th>Email</th><td>{{ $reservation->email ?? '-' }}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr><th>Check-in</th><td>{{ $reservation->check_in->format('Y-m-d') }}</td></tr>
                                        <tr><th>Check-out</th><td>{{ $reservation->check_out->format('Y-m-d') }}</td></tr>
                                        <tr><th>Status</th>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'secondary',
                                                        'confirmed' => 'primary',
                                                        'checked_in' => 'success',
                                                        'checked_out' => 'info',
                                                        'cancelled' => 'danger',
                                                    ];
                                                    $color = $statusColors[$reservation->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                            </td>
                                        </tr>
                                        @if($reservation->approved_at)
                                            <tr><th>Approved By</th><td>{{ $reservation->approver?->name }} on {{ $reservation->approved_at->format('Y-m-d H:i') }}</td></tr>
                                        @endif
                                        @if($reservation->checked_in_at)
                                            <tr><th>Checked In</th><td>{{ $reservation->checked_in_at->format('Y-m-d H:i') }}</td></tr>
                                        @endif
                                        @if($reservation->checked_out_at)
                                            <tr><th>Checked Out</th><td>{{ $reservation->checked_out_at->format('Y-m-d H:i') }}</td></tr>
                                        @endif
                                        @if($reservation->cancelled_at)
                                            <tr><th>Cancelled</th><td>{{ $reservation->cancelled_at->format('Y-m-d H:i') }} by {{ $reservation->canceller?->name }}</td></tr>
                                            @if($reservation->cancellation_reason)
                                                <tr><th>Cancellation Reason</th><td>{{ $reservation->cancellation_reason }}</td></tr>
                                            @endif
                                        @endif
                                        <tr><th>Created By</th><td>{{ $reservation->creator?->name }} on {{ $reservation->created_at->format('Y-m-d H:i') }}</td></tr>
                                    </table>
                                </div>
                            </div>
                            @if($reservation->notes)
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Notes</h5>
                                        <p>{{ $reservation->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                    <h5>Quick Actions</h5>
                                    <div class="btn-group">
                                        @if($reservation->status === 'pending')
                                            <form method="POST" action="{{ route('pms.reservations.approve', $reservation) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this reservation?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                                            <form method="POST" action="{{ route('pms.reservations.check-in', $reservation) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" onclick="return confirm('Check in this guest?')">
                                                    <i class="fas fa-sign-in-alt"></i> Check In
                                                </button>
                                            </form>
                                        @endif
                                        @if($reservation->status === 'checked_in')
                                            <form method="POST" action="{{ route('pms.reservations.check-out', $reservation) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Check out this guest?')">
                                                    <i class="fas fa-sign-out-alt"></i> Check Out
                                                </button>
                                            </form>
                                        @endif
                                        @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('pms.reservations.cancel', $reservation) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cancellation Reason (Optional)</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-content, #printable-content * {
            visibility: visible;
        }
        #printable-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn-group, .card-tools {
            display: none !important;
        }
    }
</style>
@endpush

@push('js')
<script>
    $(function() {
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@endpush

