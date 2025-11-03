@extends('layouts.main')

@section('title', 'Ticket Reservation Details')
@section('title_page', 'Ticket Reservation Details')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ticket-reservations.index') }}">Ticket Reservations</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Reservation #{{ $ticketReservation->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('ticket-reservations.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                @can('edit ticket reservations')
                                    @if ($ticketReservation->status === 'pending')
                                        <a href="{{ route('ticket-reservations.edit', $ticketReservation) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Reservation ID:</dt>
                                        <dd class="col-sm-8">#{{ $ticketReservation->id }}</dd>

                                        <dt class="col-sm-4">Employee:</dt>
                                        <dd class="col-sm-8">{{ $ticketReservation->employee->name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Ticket Type:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $iconMap = [
                                                    'flight' => 'fas fa-plane',
                                                    'train' => 'fas fa-train',
                                                    'bus' => 'fas fa-bus',
                                                    'hotel' => 'fas fa-bed',
                                                ];
                                                $icon = $iconMap[$ticketReservation->ticket_type] ?? 'fas fa-ticket-alt';
                                            @endphp
                                            <i class="{{ $icon }}"></i> {{ ucfirst($ticketReservation->ticket_type) }}
                                        </dd>

                                        <dt class="col-sm-4">Destination:</dt>
                                        <dd class="col-sm-8">{{ $ticketReservation->destination }}</dd>

                                        <dt class="col-sm-4">Departure Date:</dt>
                                        <dd class="col-sm-8">
                                            {{ $ticketReservation->departure_date ? $ticketReservation->departure_date->format('d/m/Y') : 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Return Date:</dt>
                                        <dd class="col-sm-8">
                                            {{ $ticketReservation->return_date ? $ticketReservation->return_date->format('d/m/Y') : 'One-way trip' }}
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Estimated Cost:</dt>
                                        <dd class="col-sm-8">
                                            <strong>Rp {{ number_format($ticketReservation->cost, 0, ',', '.') }}</strong>
                                        </dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $badgeClass = match ($ticketReservation->status) {
                                                    'pending' => 'badge-warning',
                                                    'approved' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    'booked' => 'badge-info',
                                                    'completed' => 'badge-secondary',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($ticketReservation->status) }}</span>
                                        </dd>

                                        @if ($ticketReservation->approver)
                                            <dt class="col-sm-4">Approved By:</dt>
                                            <dd class="col-sm-8">{{ $ticketReservation->approver->name }}</dd>

                                            <dt class="col-sm-4">Approved At:</dt>
                                            <dd class="col-sm-8">
                                                {{ $ticketReservation->approved_at ? $ticketReservation->approved_at->format('d/m/Y H:i') : 'N/A' }}
                                            </dd>
                                        @endif

                                        @if ($ticketReservation->booking_reference)
                                            <dt class="col-sm-4">Booking Reference:</dt>
                                            <dd class="col-sm-8">
                                                <code>{{ $ticketReservation->booking_reference }}</code>
                                            </dd>
                                        @endif

                                        <dt class="col-sm-4">Created At:</dt>
                                        <dd class="col-sm-8">
                                            {{ $ticketReservation->created_at->format('d/m/Y H:i') }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            @if ($ticketReservation->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Additional Notes:</h5>
                                        <p class="text-muted">{{ $ticketReservation->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($ticketReservation->rejection_reason)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-danger">
                                            <h5><i class="icon fas fa-ban"></i> Rejection Reason</h5>
                                            <p>{{ $ticketReservation->rejection_reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <!-- Document Management -->
                            <h5><i class="fas fa-file-alt"></i> Travel Documents</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Uploaded At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ticketReservation->documents as $index => $document)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $document->original_name }}</td>
                                                <td><span class="badge badge-info">{{ strtoupper(pathinfo($document->original_name, PATHINFO_EXTENSION)) }}</span></td>
                                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ Storage::disk('public')->url($document->file_path) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @can('edit ticket reservations')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="deleteDocument({{ $document->id }})">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No documents uploaded yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @can('edit ticket reservations')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Upload Document</h5>
                                        <form action="{{ route('ticket-reservations.upload-document', $ticketReservation) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="document"
                                                        name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    <label class="custom-file-label" for="document">Choose file</label>
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Accepted formats: PDF, JPG, PNG (max 5MB)</small>
                                        </form>
                                    </div>
                                </div>
                            @endcan

                            <hr>

                            <!-- Action Buttons -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    @can('approve ticket reservations')
                                        @if ($ticketReservation->canBeApproved())
                                            <button type="button" class="btn btn-success"
                                                onclick="approveReservation({{ $ticketReservation->id }})">
                                                <i class="fas fa-check"></i> Approve Reservation
                                            </button>
                                        @endif

                                        @if ($ticketReservation->canBeRejected())
                                            <button type="button" class="btn btn-danger"
                                                onclick="rejectReservation({{ $ticketReservation->id }})">
                                                <i class="fas fa-times"></i> Reject Reservation
                                            </button>
                                        @endif

                                        @if ($ticketReservation->status === 'approved')
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#bookModal">
                                                <i class="fas fa-calendar-check"></i> Mark as Booked
                                            </button>
                                        @endif

                                        @if ($ticketReservation->status === 'booked')
                                            <button type="button" class="btn btn-secondary"
                                                onclick="markCompleted({{ $ticketReservation->id }})">
                                                <i class="fas fa-check-circle"></i> Mark as Completed
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
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
                            <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="3"
                                required></textarea>
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
                <form action="{{ route('ticket-reservations.mark-booked', $ticketReservation) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="booking_reference">Booking Reference:</label>
                            <input type="text" class="form-control" id="booking_reference" name="booking_reference"
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
            // Custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
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
                                Swal.fire('Approved!', 'Ticket reservation has been approved.', 'success')
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
                        Swal.fire('Rejected!', 'Ticket reservation has been rejected.', 'success')
                            .then(() => {
                                location.reload();
                            });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            });

            // Global function for marking as completed
            window.markCompleted = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Mark this reservation as completed?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, mark it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('ticket-reservations') }}/" + id + "/mark-completed",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Completed!', 'Reservation marked as completed.', 'success')
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

            // Global function for deleting document
            window.deleteDocument = function(documentId) {
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
                            url: "{{ url('ticket-reservations') }}/{{ $ticketReservation->id }}/delete-document/" + documentId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', 'Document has been deleted.', 'success')
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
        });
    </script>
@endpush