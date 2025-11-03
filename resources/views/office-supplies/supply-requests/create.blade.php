@extends('layouts.main')

@section('title_page', 'Create Supply Request')
@section('breadcrumb_title', 'Create Supply Request')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create New Supply Request</h3>
                            <div class="card-tools">
                                <a href="{{ route('supplies.requests.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <form id="supply-request-form" action="{{ route('supplies.requests.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="request_date">Request Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('request_date') is-invalid @enderror"
                                                id="request_date" name="request_date"
                                                value="{{ old('request_date', date('Y-m-d')) }}" required>
                                            @error('request_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                                placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Request Items</h5>
                                <div id="items-container">
                                    <div class="item-row row mb-3">
                                        <div class="col-md-4">
                                            <label>Supply Item <span class="text-danger">*</span></label>
                                            <input type="hidden" name="items[0][supply_id]" class="supply-id-input" value="">
                                            <div class="input-group">
                                                <input type="text" class="form-control supply-display" placeholder="Click to select supply item" readonly required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary select-supply-btn" data-index="0">
                                                        <i class="fas fa-search"></i> Select
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="items[0][quantity]"
                                                class="form-control quantity-input" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Stock</label>
                                            <input type="text" class="form-control stock-display" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unit</label>
                                            <input type="text" class="form-control unit-display" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <label>Notes</label>
                                            <input type="text" name="items[0][notes]" class="form-control"
                                                placeholder="Notes">
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-item"
                                                style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="add-item" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Request
                                </button>
                                <a href="{{ route('supplies.requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Supply Selection Modal -->
    <div class="modal fade" id="supplyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Supply Item</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="supplyTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let itemIndex = 1;
            let currentItemIndex = 0;
            let supplyTable = null;

            // Initialize DataTable when modal opens
            $('#supplyModal').on('show.bs.modal', function() {
                if (!supplyTable) {
                    supplyTable = $('#supplyTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '{{ route('supplies.requests.supplies-data') }}',
                        columns: [
                            { data: 'name', name: 'name' },
                            { data: 'code', name: 'code' },
                            { data: 'category', name: 'category' },
                            { data: 'current_stock', name: 'current_stock' },
                            { data: 'unit', name: 'unit' },
                            { 
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return '<button class="btn btn-sm btn-primary select-supply-row" data-id="' + row.id + 
                                           '" data-name="' + row.name + '" data-code="' + row.code + 
                                           '" data-stock="' + row.current_stock + '" data-unit="' + row.unit + 
                                           '"><i class="fas fa-check"></i> Select</button>';
                                }
                            }
                        ],
                        pageLength: 25,
                        order: [[0, 'asc']]
                    });
                }
            });

            // Handle select supply button click
            $(document).on('click', '.select-supply-btn', function() {
                currentItemIndex = $(this).data('index');
                $('#supplyModal').modal('show');
            });

            // Handle row select in DataTable
            $(document).on('click', '.select-supply-row', function() {
                const supplyId = $(this).data('id');
                const supplyName = $(this).data('name');
                const supplyCode = $(this).data('code');
                const supplyStock = $(this).data('stock');
                const supplyUnit = $(this).data('unit');

                // Update the current item row
                const itemRow = $(`.select-supply-btn[data-index="${currentItemIndex}"]`).closest('.item-row');
                itemRow.find('.supply-id-input').val(supplyId);
                itemRow.find('.supply-display').val(supplyName + ' (' + supplyCode + ')');
                itemRow.find('.unit-display').val(supplyUnit);
                itemRow.find('.stock-display').val(supplyStock);

                // Close modal
                $('#supplyModal').modal('hide');
            });

            // Add new item row
            $('#add-item').on('click', function() {
                const newRow = `
            <div class="item-row row mb-3">
                <div class="col-md-4">
                    <label>Supply Item <span class="text-danger">*</span></label>
                    <input type="hidden" name="items[${itemIndex}][supply_id]" class="supply-id-input" value="">
                    <div class="input-group">
                        <input type="text" class="form-control supply-display" placeholder="Click to select supply item" readonly required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary select-supply-btn" data-index="${itemIndex}">
                                <i class="fas fa-search"></i> Select
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" 
                           min="1" required>
                </div>
                <div class="col-md-2">
                    <label>Stock</label>
                    <input type="text" class="form-control stock-display" readonly>
                </div>
                <div class="col-md-2">
                    <label>Unit</label>
                    <input type="text" class="form-control unit-display" readonly>
                </div>
                <div class="col-md-1">
                    <label>Notes</label>
                    <input type="text" name="items[${itemIndex}][notes]" class="form-control" 
                           placeholder="Notes">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

                $('#items-container').append(newRow);
                itemIndex++;
                updateRemoveButtons();
            });

            // Remove item row
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                updateRemoveButtons();
            });

            // Update remove buttons visibility
            function updateRemoveButtons() {
                const itemRows = $('.item-row');
                if (itemRows.length > 1) {
                    $('.remove-item').show();
                } else {
                    $('.remove-item').hide();
                }
            }

            // Form validation and SweetAlert confirmation
            $('form').on('submit', function(e) {
                e.preventDefault();
                
                const itemRows = $('.item-row');
                if (itemRows.length === 0) {
                    toastr.error('Please add at least one item to the request.');
                    return false;
                }

                // Check if all required fields are filled
                let isValid = true;
                itemRows.each(function() {
                    const supplyId = $(this).find('.supply-id-input').val();
                    const quantityInput = $(this).find('.quantity-input');

                    if (!supplyId || !quantityInput.val()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    toastr.error('Please fill in all required fields for all items.');
                    return false;
                }

                // Show SweetAlert confirmation
                Swal.fire({
                    title: 'Confirm Submission',
                    text: 'Are you sure you want to create this supply request?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, create request!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit via AJAX to maintain session
                        const form = document.getElementById('supply-request-form');
                        const formData = new FormData(form);
                        
                        // Get CSRF token from form - FormData automatically includes _token field from form
                        // But we also set the header to ensure Laravel accepts it
                        const csrfToken = $('input[name="_token"]').val();
                        
                        $.ajax({
                            url: form.action,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            xhrFields: {
                                withCredentials: true
                            },
                            headers: {
                                'X-CSRF-TOKEN': csrfToken, // Use token from form to match what's in FormData
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message || 'Supply request created successfully.');
                                    window.location.href = response.redirect || '{{ route("supplies.requests.index") }}';
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message || 'Failed to create supply request.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Failed to create supply request.';
                                
                                // Handle 401 Unauthenticated
                                if (xhr.status === 401) {
                                    Swal.fire({
                                        title: 'Session Expired',
                                        text: 'Your session has expired. Please log in again.',
                                        icon: 'warning',
                                        confirmButtonText: 'Go to Login'
                                    }).then(() => {
                                        window.location.href = '{{ route("login") }}';
                                    });
                                    return;
                                }
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseText) {
                                    // Try to extract error message from response
                                    const match = xhr.responseText.match(/<div class="invalid-feedback">(.+?)<\/div>/);
                                    if (match) {
                                        errorMessage = match[1];
                                    } else if (xhr.responseText.includes('Unauthenticated')) {
                                        errorMessage = 'Your session has expired. Please refresh the page and try again.';
                                    }
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

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
