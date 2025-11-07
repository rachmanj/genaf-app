@extends('layouts.main')

@section('title_page', 'Pending Verification')
@section('breadcrumb_title', 'Pending Verification')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Distributions Pending Verification</h3>
                        </div>
                        <div class="card-body">
                            <table id="tbl-pending-verification" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Number</th>
                                        <th>Supply Item</th>
                                        <th>Code</th>
                                        <th>Quantity</th>
                                        <th>Request Reference</th>
                                        <th>Distribution Date</th>
                                        <th>Distributed By</th>
                                        <th>Days Pending</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
                        <input type="hidden" id="reject_distribution_id" name="distribution_id">
                        <div class="form-group">
                            <label>Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="verification_notes" id="verification_notes" class="form-control" rows="2"></textarea>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-pending-verification').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplies.fulfillment.pending-verification') }}",
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'form_number',
                        name: 'form_number'
                    },
                    {
                        data: 'supply_name',
                        name: 'supply_name'
                    },
                    {
                        data: 'supply_code',
                        name: 'supply_code'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'request_reference',
                        name: 'request_reference'
                    },
                    {
                        data: 'distribution_date',
                        name: 'distribution_date'
                    },
                    {
                        data: 'distributed_by_name',
                        name: 'distributed_by_name'
                    },
                    {
                        data: 'days_pending',
                        name: 'days_pending'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [8, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });

            window.rejectDistribution = function(distributionId) {
                $('#reject_distribution_id').val(distributionId);
                $('#rejectForm')[0].reset();
                $('#reject_distribution_id').val(distributionId);
                $('#rejectModal').modal('show');
            };

            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();
                var distributionId = $('#reject_distribution_id').val();
                var formData = {
                    rejection_reason: $('#rejection_reason').val(),
                    verification_notes: $('#verification_notes').val()
                };

                $.ajax({
                    url: '{{ url('supplies/fulfillment/verification') }}/' + distributionId +
                        '/reject',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#rejectModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message || 'Failed to reject distribution');
                        }
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message ||
                            'Failed to reject distribution';
                        toastr.error(message);
                    }
                });
            });
        });
    </script>
@endpush
