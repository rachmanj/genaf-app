@extends('layouts.main')

@section('title_page', 'Supply Request Details')
@section('breadcrumb_title', 'Supply Request Details')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Supply Request #{{ $supplyRequest->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('supplies.requests.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                @can('edit supply requests')
                                    @if ($supplyRequest->status === 'pending')
                                        <a href="{{ route('supplies.requests.edit', $supplyRequest) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit Request
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Request ID:</dt>
                                        <dd class="col-sm-8">#{{ $supplyRequest->id }}</dd>

                                        <dt class="col-sm-4">Employee:</dt>
                                        <dd class="col-sm-8">{{ $supplyRequest->employee->name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Request Date:</dt>
                                        <dd class="col-sm-8">
                                            {{ $supplyRequest->request_date ? $supplyRequest->request_date->format('d/m/Y') : 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $badgeClass = match ($supplyRequest->status) {
                                                    'pending' => 'badge-warning',
                                                    'approved' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $badgeClass }}">{{ ucfirst($supplyRequest->status) }}</span>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Approved By:</dt>
                                        <dd class="col-sm-8">{{ $supplyRequest->approver->name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Approved At:</dt>
                                        <dd class="col-sm-8">
                                            {{ $supplyRequest->approved_at ? $supplyRequest->approved_at->format('d/m/Y H:i') : 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Items Count:</dt>
                                        <dd class="col-sm-8">{{ $supplyRequest->items->count() }}</dd>

                                        <dt class="col-sm-4">Total Quantity:</dt>
                                        <dd class="col-sm-8">{{ $supplyRequest->items->sum('quantity') }}</dd>
                                    </dl>
                                </div>
                            </div>

                            @if ($supplyRequest->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Notes:</h5>
                                        <p class="text-muted">{{ $supplyRequest->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($supplyRequest->rejection_reason)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Rejection Reason:</h5>
                                        <p class="text-danger">{{ $supplyRequest->rejection_reason }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <h5>Request Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Supply Item</th>
                                            <th>Code</th>
                                            <th class="text-right">Quantity</th>
                                            <th>Unit</th>
                                            <th>Current Stock</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supplyRequest->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->supply->name }}</td>
                                                <td>{{ $item->supply->code }}</td>
                                                <td class="text-right">{{ number_format($item->quantity) }}</td>
                                                <td>{{ $item->supply->unit }}</td>
                                                <td class="text-right">{{ number_format($item->supply->current_stock) }}
                                                </td>
                                                <td>{{ $item->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($supplyRequest->status === 'pending')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        @can('approve supply requests')
                                            <button type="button" class="btn btn-success"
                                                onclick="approveRequest({{ $supplyRequest->id }})">
                                                <i class="fas fa-check"></i> Approve Request
                                            </button>
                                        @endcan
                                        @can('reject supply requests')
                                            <button type="button" class="btn btn-danger"
                                                onclick="rejectRequest({{ $supplyRequest->id }})">
                                                <i class="fas fa-times"></i> Reject Request
                                            </button>
                                        @endcan
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
                    <h5 class="modal-title">Reject Supply Request</h5>
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
                        <button type="submit" class="btn btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Approve request
            window.approveRequest = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to approve this supply request.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/requests') }}/" + id + "/approve",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to approve request');
                            }
                        });
                    }
                });
            };

            // Reject request
            window.rejectRequest = function(id) {
                $('#rejectModal').modal('show');
                $('#rejectForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ url('supplies/requests') }}/" + id + "/reject",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: $('#rejection_reason').val()
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#rejectModal').modal('hide');
                            $('#rejection_reason').val('');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error ||
                                'Failed to reject request');
                        }
                    });
                });
            };

            // Show session messages
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
