@extends('layouts.main')

@section('title_page', 'Edit Supply Request')
@section('breadcrumb_title', 'Edit Supply Request')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Supply Request #{{ $supplyRequest->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('supplies.requests.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <a href="{{ route('supplies.requests.show', $supplyRequest) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('supplies.requests.update', $supplyRequest) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="request_date">Request Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('request_date') is-invalid @enderror"
                                                id="request_date" name="request_date"
                                                value="{{ old('request_date', $supplyRequest->request_date ? $supplyRequest->request_date->format('Y-m-d') : '') }}"
                                                required>
                                            @error('request_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                                placeholder="Enter any additional notes">{{ old('notes', $supplyRequest->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <h5>Request Items</h5>
                                <div id="items-container">
                                    @foreach ($supplyRequest->items as $index => $item)
                                        <div class="item-row row mb-3">
                                            <div class="col-md-4">
                                                <label>Supply Item <span class="text-danger">*</span></label>
                                                <select name="items[{{ $index }}][supply_id]"
                                                    class="form-control supply-select" required>
                                                    <option value="">Select Supply Item</option>
                                                    @foreach ($supplies as $supply)
                                                        <option value="{{ $supply->id }}" data-unit="{{ $supply->unit }}"
                                                            data-stock="{{ $supply->current_stock }}"
                                                            {{ $item->supply_id == $supply->id ? 'selected' : '' }}>
                                                            {{ $supply->name }} ({{ $supply->code }}) - Stock:
                                                            {{ $supply->current_stock }} {{ $supply->unit }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Quantity <span class="text-danger">*</span></label>
                                                <input type="number" name="items[{{ $index }}][quantity]"
                                                    class="form-control quantity-input" min="1"
                                                    value="{{ $item->quantity }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Unit</label>
                                                <input type="text" class="form-control unit-display" readonly
                                                    value="{{ $item->supply->unit }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Notes</label>
                                                <input type="text" name="items[{{ $index }}][notes]"
                                                    class="form-control" placeholder="Optional notes"
                                                    value="{{ $item->notes }}">
                                            </div>
                                            <div class="col-md-1">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
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
                                    <i class="fas fa-save"></i> Update Request
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let itemIndex = {{ $supplyRequest->items->count() }};

            // Add new item row
            $('#add-item').on('click', function() {
                const newRow = `
            <div class="item-row row mb-3">
                <div class="col-md-4">
                    <label>Supply Item <span class="text-danger">*</span></label>
                    <select name="items[${itemIndex}][supply_id]" class="form-control supply-select" required>
                        <option value="">Select Supply Item</option>
                        @foreach ($supplies as $supply)
                            <option value="{{ $supply->id }}" 
                                    data-unit="{{ $supply->unit }}"
                                    data-stock="{{ $supply->current_stock }}">
                                {{ $supply->name }} ({{ $supply->code }}) - Stock: {{ $supply->current_stock }} {{ $supply->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" 
                           min="1" required>
                </div>
                <div class="col-md-2">
                    <label>Unit</label>
                    <input type="text" class="form-control unit-display" readonly>
                </div>
                <div class="col-md-3">
                    <label>Notes</label>
                    <input type="text" name="items[${itemIndex}][notes]" class="form-control" 
                           placeholder="Optional notes">
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

            // Update unit display when supply is selected
            $(document).on('change', '.supply-select', function() {
                const selectedOption = $(this).find('option:selected');
                const unit = selectedOption.data('unit');
                const stock = selectedOption.data('stock');

                $(this).closest('.item-row').find('.unit-display').val(unit);

                // Set max quantity to current stock
                const quantityInput = $(this).closest('.item-row').find('.quantity-input');
                quantityInput.attr('max', stock);
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

            // Initialize remove buttons visibility
            updateRemoveButtons();

            // Form validation
            $('form').on('submit', function(e) {
                const itemRows = $('.item-row');
                if (itemRows.length === 0) {
                    e.preventDefault();
                    toastr.error('Please add at least one item to the request.');
                    return false;
                }

                // Check if all required fields are filled
                let isValid = true;
                itemRows.each(function() {
                    const supplySelect = $(this).find('.supply-select');
                    const quantityInput = $(this).find('.quantity-input');

                    if (!supplySelect.val() || !quantityInput.val()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Please fill in all required fields for all items.');
                    return false;
                }
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
