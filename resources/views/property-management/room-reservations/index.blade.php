@extends('layouts.main')

@section('title', 'Reservations')

@section('title_page')
    Reservations
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Reservations</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Reservations</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.reservations.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> New Reservation
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Form Number</th>
                                            <th>Building</th>
                                            <th>Room</th>
                                            <th>Guest</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reservations as $i => $res)
                                            <tr>
                                                <td>{{ $reservations->firstItem() + $i }}</td>
                                                <td>{{ $res->form_number }}</td>
                                                <td>{{ $res->room?->building?->name }}</td>
                                                <td>{{ $res->room?->room_number }}</td>
                                                <td>{{ $res->guest_name }}</td>
                                                <td>{{ $res->check_in->format('Y-m-d') }} → {{ $res->check_out->format('Y-m-d') }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'secondary',
                                                            'confirmed' => 'primary',
                                                            'checked_in' => 'success',
                                                            'checked_out' => 'info',
                                                            'cancelled' => 'danger',
                                                        ];
                                                        $color = $statusColors[$res->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $res->status)) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('pms.reservations.show', $res) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($res->status === 'pending')
                                                        <form method="POST" action="{{ route('pms.reservations.approve', $res) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this reservation?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(in_array($res->status, ['pending', 'confirmed']))
                                                        <form method="POST" action="{{ route('pms.reservations.check-in', $res) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary" title="Check In" onclick="return confirm('Check in this guest?')">
                                                                <i class="fas fa-sign-in-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($res->status === 'checked_in')
                                                        <form method="POST" action="{{ route('pms.reservations.check-out', $res) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Check Out" onclick="return confirm('Check out this guest?')">
                                                                <i class="fas fa-sign-out-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(!in_array($res->status, ['checked_out', 'cancelled']))
                                                        <button type="button" class="btn btn-sm btn-danger cancel-reservation" data-id="{{ $res->id }}" title="Cancel">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No reservations.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $reservations->links() }}
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
                <form id="cancelForm" method="POST">
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

@push('js')
<script>
    $(function() {
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        $('.cancel-reservation').on('click', function() {
            const reservationId = $(this).data('id');
            $('#cancelForm').attr('action', '{{ url('/pms/reservations') }}/' + reservationId + '/cancel');
            $('#cancelModal').modal('show');
        });
    });
</script>
@endpush


