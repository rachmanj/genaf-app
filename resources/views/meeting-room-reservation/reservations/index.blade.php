@extends('layouts.main')

@section('title_page', 'Meeting Room Reservations')
@section('breadcrumb_title', 'Meeting Room Reservations')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-door-open"></i> Meeting Room Reservations</h3>
                            @can('create meeting room reservations')
                                <div class="card-tools">
                                    <a href="{{ route('meeting-rooms.reservations.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> New Reservation
                                    </a>
                                </div>
                            @endcan
                            @can('view meeting room allocation diagram')
                                <div class="card-tools" style="margin-right: 10px;">
                                    <a href="{{ route('meeting-rooms.allocation-diagram') }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-calendar-alt"></i> Allocation Diagram
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-status">Status:</label>
                                    <select id="filter-status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="pending_dept_head">Pending Dept Head</option>
                                        <option value="pending_ga_admin">Pending GA Admin</option>
                                        <option value="approved">Approved</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-date-from">Date From:</label>
                                    <input type="date" id="filter-date-from" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-date-to">Date To:</label>
                                    <input type="date" id="filter-date-to" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="clear-filters" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <table id="tbl-reservations" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Number</th>
                                        <th>Requestor</th>
                                        <th>Department</th>
                                        <th>Meeting Title</th>
                                        <th>Dates</th>
                                        <th>Time</th>
                                        <th>Requested Room</th>
                                        <th>Allocated Room</th>
                                        <th>Location</th>
                                        <th>Status</th>
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
                    <h5 class="modal-title">Reject Reservation</h5>
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
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Allocate Room Modal -->
    <div class="modal fade" id="allocateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Allocate Room</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="allocateForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="allocated_room_id">Select Room:</label>
                            <select id="allocated_room_id" name="allocated_room_id" class="form-control" required>
                                <option value="">-- Select Room --</option>
                                @foreach(\App\Models\MeetingRoom::where('is_active', true)->orderBy('name')->get() as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} (Capacity: {{ $room->capacity }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Allocate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Send Response Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Response to Requestor</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="responseForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="response_notes">Response Notes (Optional):</label>
                            <textarea id="response_notes" name="response_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Response</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-reservations').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('meeting-rooms.reservations.index') }}",
                    data: function(d) {
                        d.status = $('#filter-status').val();
                        d.date_from = $('#filter-date-from').val();
                        d.date_to = $('#filter-date-to').val();
                    }
                },
                columns: [
                    { data: 'index', name: 'index', orderable: false, searchable: false },
                    { data: 'form_number', name: 'form_number' },
                    { data: 'requestor_name', name: 'requestor_name' },
                    { data: 'department_name', name: 'department_name' },
                    { data: 'meeting_title', name: 'meeting_title' },
                    { data: 'meeting_dates', name: 'meeting_date_start' },
                    { data: 'meeting_time', name: 'meeting_time_start' },
                    { data: 'requested_room', name: 'requested_room' },
                    { data: 'allocated_room', name: 'allocated_room' },
                    { data: 'location', name: 'location' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']],
                pageLength: 25,
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                language: {
                    processing: "Loading..."
                }
            });

            // Status filter
            $('#filter-status').on('change', function() {
                table.ajax.reload();
            });

            // Date filters
            $('#filter-date-from, #filter-date-to').on('change', function() {
                table.ajax.reload();
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#filter-status').val('');
                $('#filter-date-from').val('');
                $('#filter-date-to').val('');
                table.ajax.reload();
            });

            // Approve Department Head
            window.approveDeptHead = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Approve this reservation as Department Head?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('meeting-rooms/reservations') }}/" + id + "/approve-dept-head",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.message || 'Failed to approve');
                            }
                        });
                    }
                });
            };

            // Reject Department Head
            window.rejectDeptHead = function(id) {
                $('#rejectModal').modal('show');
                $('#rejectForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('meeting-rooms/reservations') }}/" + id + "/reject-dept-head",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: $('#rejection_reason').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#rejectModal').modal('hide');
                            $('#rejection_reason').val('');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Failed to reject');
                        }
                    });
                });
            };

            // Approve GA Admin
            window.approveGAAdmin = function(id) {
                Swal.fire({
                    title: 'Approve Request',
                    text: "Approve this reservation as GA Admin?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('meeting-rooms/reservations') }}/" + id + "/approve-ga-admin",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.message || 'Failed to approve');
                            }
                        });
                    }
                });
            };

            // Reject GA Admin
            window.rejectGAAdmin = function(id) {
                $('#rejectModal').modal('show');
                $('#rejectForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('meeting-rooms/reservations') }}/" + id + "/reject-ga-admin",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: $('#rejection_reason').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#rejectModal').modal('hide');
                            $('#rejection_reason').val('');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Failed to reject');
                        }
                    });
                });
            };

            // Allocate Room
            window.allocateRoom = function(id) {
                $('#allocateModal').modal('show');
                $('#allocateForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('meeting-rooms/reservations') }}/" + id + "/allocate-room",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            allocated_room_id: $('#allocated_room_id').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#allocateModal').modal('hide');
                            $('#allocated_room_id').val('');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Failed to allocate room');
                        }
                    });
                });
            };

            // Send Response
            window.sendResponse = function(id) {
                $('#responseModal').modal('show');
                $('#responseForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('meeting-rooms/reservations') }}/" + id + "/send-response",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            response_notes: $('#response_notes').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#responseModal').modal('hide');
                            $('#response_notes').val('');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Failed to send response');
                        }
                    });
                });
            };

            // Cancel Reservation
            window.cancelReservation = function(id) {
                Swal.fire({
                    title: 'Cancel Reservation',
                    text: "Are you sure you want to cancel this reservation?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('meeting-rooms/reservations') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success('Reservation cancelled successfully');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.message || 'Failed to cancel');
                            }
                        });
                    }
                });
            };

            // Session messages
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
