@extends('layouts.main')

@section('title_page', 'Ticket Reservations')
@section('breadcrumb_title', 'Ticket Reservations')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ticket Reservations Management</h3>
                            @can('create ticket reservations')
                                <div class="card-tools">
                                    <a href="{{ route('ticket-reservations.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> New Reservation
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="filter-status">Status:</label>
                                    <select id="filter-status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="booked">Booked</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-type">Type:</label>
                                    <select id="filter-type" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="flight">Flight</option>
                                        <option value="train">Train</option>
                                        <option value="bus">Bus</option>
                                        <option value="hotel">Hotel</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-employee">Employee:</label>
                                    <select id="filter-employee" class="form-control form-control-sm">
                                        <option value="">All Employees</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-date-from">Date From:</label>
                                    <input type="date" id="filter-date-from" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label for="filter-date-to">Date To:</label>
                                    <input type="date" id="filter-date-to" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="clear-filters" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <table id="tbl-ticket-reservations" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Type</th>
                                        <th>Destination</th>
                                        <th>Departure</th>
                                        <th>Return</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Approved By</th>
                                        <th>Approved At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Ticket Reservation</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason">Rejection Reason:</label>
                            <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Book Modal -->
    <div class="modal fade" id="bookModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Booked</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="bookForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="booking_reference">Booking Reference:</label>
                            <input type="text" id="booking_reference" name="booking_reference" class="form-control"
                                required>
                            <small class="text-muted">Enter the booking reference number from the travel provider</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark as Booked</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-ticket-reservations').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ticket-reservations.index') }}",
                    data: function(d) {
                        d.status = $('#filter-status').val();
                        d.ticket_type = $('#filter-type').val();
                        d.employee_id = $('#filter-employee').val();
                        d.date_from = $('#filter-date-from').val();
                        d.date_to = $('#filter-date-to').val();
                    }
                },
                columns: [{
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'ticket_type_badge',
                        name: 'ticket_type'
                    },
                    {
                        data: 'destination',
                        name: 'destination'
                    },
                    {
                        data: 'departure_date_formatted',
                        name: 'departure_date'
                    },
                    {
                        data: 'return_date_formatted',
                        name: 'return_date'
                    },
                    {
                        data: 'cost_formatted',
                        name: 'cost'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'approver_name',
                        name: 'approver_name'
                    },
                    {
                        data: 'approved_at_formatted',
                        name: 'approved_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [3, 'desc']
                ], // Order by departure date desc
                pageLength: 10
            });

            // Filter handlers
            $('#filter-status, #filter-type, #filter-employee, #filter-date-from, #filter-date-to').on('change',
                function() {
                    table.draw();
                });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#filter-status, #filter-type, #filter-employee, #filter-date-from, #filter-date-to').val(
                    '');
                table.draw();
            });

            // Global function for approving reservation
            window.approveReservation = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to approve this reservation?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('ticket-reservations') }}/" + id + "/approve",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Approved!',
                                            'Ticket reservation has been approved.',
                                            'success')
                                        .then(() => {
                                            location.reload();
                                        });
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            };

            // Global function for rejecting reservation
            window.rejectReservation = function(id) {
                $('#rejectForm').attr('reservation-id', id);
                $('#rejectModal').modal('show');
            };

            // Handle reject form submission
            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();
                var id = $(this).attr('reservation-id');
                $.ajax({
                    url: "{{ url('ticket-reservations') }}/" + id + "/reject",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        rejection_reason: $('#rejection_reason').val()
                    },
                    success: function(response) {
                        $('#rejectModal').modal('hide');
                        Swal.fire('Rejected!', 'Ticket reservation has been rejected.',
                                'success')
                            .then(() => {
                                location.reload();
                            });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            });

            // Global function for deleting reservation
            window.deleteReservation = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('ticket-reservations') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!',
                                        'Ticket reservation has been deleted.', 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            },
                            error: function() {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            };

            // Global function for marking as booked
            window.markBooked = function(id) {
                $('#bookForm').attr('reservation-id', id);
                $('#bookModal').modal('show');
            };

            // Handle book form submission
            $('#bookForm').on('submit', function(e) {
                e.preventDefault();
                var id = $(this).attr('reservation-id');
                $.ajax({
                    url: "{{ url('ticket-reservations') }}/" + id + "/mark-booked",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        booking_reference: $('#booking_reference').val()
                    },
                    success: function(response) {
                        $('#bookModal').modal('hide');
                        Swal.fire('Booked!', 'Reservation marked as booked.', 'success')
                            .then(() => {
                                location.reload();
                            });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
