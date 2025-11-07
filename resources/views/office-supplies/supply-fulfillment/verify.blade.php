@extends('layouts.main')

@section('title_page', 'Verify Distribution')
@section('breadcrumb_title', 'Verify Distribution')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Verify Distribution</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Distribution Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Form Number</th>
                                            <td>{{ $distribution->form_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supply Item</th>
                                            <td>{{ $distribution->supply->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supply Code</th>
                                            <td>{{ $distribution->supply->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Quantity</th>
                                            <td>{{ $distribution->quantity }} {{ $distribution->supply->unit }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $distribution->department->department_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Distribution Date</th>
                                            <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Distributed By</th>
                                            <td>{{ $distribution->distributedBy->name }}</td>
                                        </tr>
                                        @if ($distribution->notes)
                                            <tr>
                                                <th>Notes</th>
                                                <td>{{ $distribution->notes }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Request Information</h5>
                                    @if ($distribution->requestItem && $distribution->requestItem->request)
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Request Form Number</th>
                                                <td>{{ $distribution->requestItem->request->form_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Requestor</th>
                                                <td>{{ $distribution->requestItem->request->employee->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Request Date</th>
                                                <td>{{ $distribution->requestItem->request->request_date->format('d/m/Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Approved Quantity</th>
                                                <td>{{ $distribution->requestItem->approved_quantity }}
                                                    {{ $distribution->supply->unit }}</td>
                                            </tr>
                                        </table>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <form id="verifyForm">
                                <div class="form-group">
                                    <label>Verification Notes (Optional)</label>
                                    <textarea name="verification_notes" id="verification_notes" class="form-control" rows="3"
                                        placeholder="Add any notes about receiving this distribution..."></textarea>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-success" onclick="verifyDistribution()">
                                        <i class="fas fa-check"></i> Verify & Confirm Receipt
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <a href="{{ route('supplies.fulfillment.pending-verification') }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Distribution</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="verification_notes" id="reject_verification_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function verifyDistribution() {
            Swal.fire({
                title: 'Confirm Verification',
                text: 'Are you sure you have received this distribution?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, I received it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = {
                        verification_notes: $('#verification_notes').val()
                    };

                    $.ajax({
                        url: '{{ route('supplies.fulfillment.verify', $distribution->id) }}',
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = response.redirect ||
                                        '{{ route('supplies.fulfillment.pending-verification') }}';
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Failed to verify distribution',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            var message = xhr.responseJSON?.message || 'Failed to verify distribution';
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        }

        function showRejectModal() {
            $('#rejectModal').modal('show');
        }

        $('#rejectForm').on('submit', function(e) {
            e.preventDefault();
            var formData = {
                rejection_reason: $('#rejection_reason').val(),
                verification_notes: $('#reject_verification_notes').val()
            };

            $.ajax({
                url: '{{ route('supplies.fulfillment.reject', $distribution->id) }}',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Rejected',
                            text: response.message,
                            icon: 'info',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = response.redirect ||
                                '{{ route('supplies.fulfillment.pending-verification') }}';
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to reject distribution',
                        'error');
                    }
                },
                error: function(xhr) {
                    var message = xhr.responseJSON?.message || 'Failed to reject distribution';
                    Swal.fire('Error', message, 'error');
                }
            });
        });
    </script>
@endpush
