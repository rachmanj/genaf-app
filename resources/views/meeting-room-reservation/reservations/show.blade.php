@extends('layouts.main')

@section('title_page', 'Meeting Room Reservation Details')
@section('breadcrumb_title', 'Meeting Room Reservation Details')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-door-open"></i> Room & Consumption Request Form</h3>
                            <div class="card-tools">
                                <a href="{{ route('meeting-rooms.reservations.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                @can('edit meeting room reservations')
                                    @if ($reservation->canBeEdited())
                                        <a href="{{ route('meeting-rooms.reservations.edit', $reservation) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <strong>Form Number:</strong> {{ $reservation->form_number }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Requestor:</dt>
                                        <dd class="col-sm-7">{{ $reservation->requestor->name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-5">Department:</dt>
                                        <dd class="col-sm-7">{{ $reservation->department->department_name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-5">Meeting Title:</dt>
                                        <dd class="col-sm-7"><strong>{{ $reservation->meeting_title }}</strong></dd>

                                        <dt class="col-sm-5">Location:</dt>
                                        <dd class="col-sm-7">{{ $reservation->location }}</dd>

                                        <dt class="col-sm-5">Requested Room:</dt>
                                        <dd class="col-sm-7">{{ $reservation->requestedRoom->name ?? 'Any Room' }}</dd>

                                        <dt class="col-sm-5">Allocated Room:</dt>
                                        <dd class="col-sm-7">
                                            @if($reservation->allocatedRoom)
                                                <span class="badge badge-success">{{ $reservation->allocatedRoom->name }}</span>
                                            @else
                                                <span class="badge badge-warning">Not Allocated</span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-5">Meeting Date:</dt>
                                        <dd class="col-sm-7">
                                            {{ \Carbon\Carbon::parse($reservation->meeting_date_start)->format('d/m/Y') }}
                                            @if($reservation->isMultiDay())
                                                - {{ \Carbon\Carbon::parse($reservation->meeting_date_end)->format('d/m/Y') }}
                                            @endif
                                        </dd>

                                        <dt class="col-sm-5">Meeting Time:</dt>
                                        <dd class="col-sm-7">
                                            {{ \Carbon\Carbon::parse($reservation->meeting_time_start)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($reservation->meeting_time_end)->format('H:i') }}
                                        </dd>

                                        <dt class="col-sm-5">Participants:</dt>
                                        <dd class="col-sm-7">{{ $reservation->participant_count }} people</dd>

                                        <dt class="col-sm-5">Status:</dt>
                                        <dd class="col-sm-7">
                                            @php
                                                $badgeClass = match ($reservation->status) {
                                                    'pending_dept_head' => 'badge-warning',
                                                    'pending_ga_admin' => 'badge-info',
                                                    'approved' => 'badge-success',
                                                    'confirmed' => 'badge-primary',
                                                    'rejected' => 'badge-danger',
                                                    'cancelled' => 'badge-secondary',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                                        </dd>

                                        @if($reservation->department_head_approved_at)
                                            <dt class="col-sm-5">Dept Head Approved:</dt>
                                            <dd class="col-sm-7">
                                                {{ $reservation->departmentHeadApprover->name ?? 'N/A' }}<br>
                                                <small>{{ \Carbon\Carbon::parse($reservation->department_head_approved_at)->format('d/m/Y H:i') }}</small>
                                            </dd>
                                        @endif

                                        @if($reservation->ga_admin_approved_at)
                                            <dt class="col-sm-5">GA Admin Approved:</dt>
                                            <dd class="col-sm-7">
                                                {{ $reservation->gaAdminApprover->name ?? 'N/A' }}<br>
                                                <small>{{ \Carbon\Carbon::parse($reservation->ga_admin_approved_at)->format('d/m/Y H:i') }}</small>
                                            </dd>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if($reservation->required_facilities)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Required Facilities:</label>
                                            <p>{{ $reservation->required_facilities }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->notes)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Notes:</label>
                                            <p>{{ $reservation->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->department_head_rejection_reason)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <strong>Department Head Rejection Reason:</strong><br>
                                            {{ $reservation->department_head_rejection_reason }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->ga_admin_rejection_reason)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <strong>GA Admin Rejection Reason:</strong><br>
                                            {{ $reservation->ga_admin_rejection_reason }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->response_notes)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-success">
                                            <strong>Response to Requestor:</strong><br>
                                            {{ $reservation->response_notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <h5>Consumption Requests</h5>
                            @if($reservation->consumptionRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Coffee Break Pagi</th>
                                                <th>Coffee Break Sore</th>
                                                <th>Lunch</th>
                                                <th>Dinner</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservation->consumptionRequests->groupBy('consumption_date') as $date => $requests)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                                                    <td>
                                                        @php
                                                            $morning = $requests->where('consumption_type', 'coffee_break_morning')->first();
                                                        @endphp
                                                        @if($morning && $morning->requested)
                                                            <i class="fas fa-check text-success"></i>
                                                            @if($morning->description)
                                                                <small class="d-block">{{ $morning->description }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $afternoon = $requests->where('consumption_type', 'coffee_break_afternoon')->first();
                                                        @endphp
                                                        @if($afternoon && $afternoon->requested)
                                                            <i class="fas fa-check text-success"></i>
                                                            @if($afternoon->description)
                                                                <small class="d-block">{{ $afternoon->description }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $lunch = $requests->where('consumption_type', 'lunch')->first();
                                                        @endphp
                                                        @if($lunch && $lunch->requested)
                                                            <i class="fas fa-check text-success"></i>
                                                            @if($lunch->description)
                                                                <small class="d-block">{{ $lunch->description }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $dinner = $requests->where('consumption_type', 'dinner')->first();
                                                        @endphp
                                                        @if($dinner && $dinner->requested)
                                                            <i class="fas fa-check text-success"></i>
                                                            @if($dinner->description)
                                                                <small class="d-block">{{ $dinner->description }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No consumption requests</p>
                            @endif

                            <hr>

                            <!-- Approval Actions -->
                            @if($reservation->canBeDeptHeadApproved() && Auth::user()->can('approve dept head meeting room reservations'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" onclick="approveDeptHead({{ $reservation->id }})">
                                            <i class="fas fa-check"></i> Approve as Department Head
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="rejectDeptHead({{ $reservation->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->canBeGAAdminApproved() && Auth::user()->can('approve ga admin meeting room reservations'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" onclick="approveGAAdmin({{ $reservation->id }})">
                                            <i class="fas fa-check-double"></i> Approve as GA Admin
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="rejectGAAdmin({{ $reservation->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->status === 'approved' && Auth::user()->can('allocate meeting room reservations'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" onclick="allocateRoom({{ $reservation->id }})">
                                            <i class="fas fa-door-open"></i> Allocate Room
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($reservation->status === 'approved' && $reservation->allocatedRoom && Auth::user()->can('send response meeting room reservations'))
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" onclick="sendResponse({{ $reservation->id }})">
                                            <i class="fas fa-envelope"></i> Send Response to Requestor
                                        </button>
                                    </div>
                                </div>
                            @endif
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
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
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
                            setTimeout(() => location.reload(), 1000);
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
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
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
                            setTimeout(() => location.reload(), 1000);
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
                            setTimeout(() => location.reload(), 1000);
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
                            setTimeout(() => location.reload(), 1000);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Failed to send response');
                        }
                    });
                });
            };
        });
    </script>
@endpush
