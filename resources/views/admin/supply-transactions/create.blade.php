@extends('layouts.main')

@section('title_page', 'Create Stock Transaction')
@section('breadcrumb_title', 'Create Stock Transaction')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create New Stock Transaction</h3>
                            <div class="card-tools">
                                <a href="{{ route('supplies.transactions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('supplies.transactions.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="supply_id">Supply Item <span class="text-danger">*</span></label>
                                            <select class="form-control @error('supply_id') is-invalid @enderror"
                                                id="supply_id" name="supply_id" required>
                                                <option value="">Select Supply Item</option>
                                                @foreach ($supplies as $supply)
                                                    <option value="{{ $supply->id }}" data-unit="{{ $supply->unit }}"
                                                        data-stock="{{ $supply->current_stock }}"
                                                        {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                                                        {{ $supply->name }} ({{ $supply->code }}) - Stock:
                                                        {{ $supply->current_stock }} {{ $supply->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('supply_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Transaction Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="type"
                                                name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Stock In
                                                    (+)</option>
                                                <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stock
                                                    Out (-)</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                                                name="quantity" min="1" value="{{ old('quantity') }}" required>
                                            <small class="form-text text-muted">
                                                <span id="quantity-help">Enter the quantity to adjust</span>
                                            </small>
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reference_no">Reference Number</label>
                                            <input type="text"
                                                class="form-control @error('reference_no') is-invalid @enderror"
                                                id="reference_no" name="reference_no" value="{{ old('reference_no') }}"
                                                placeholder="e.g., PO-2024-001, REQ-123">
                                            @error('reference_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transaction_date">Transaction Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('transaction_date') is-invalid @enderror"
                                                id="transaction_date" name="transaction_date"
                                                value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                            @error('transaction_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
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

                                <!-- Stock Information Display -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-info" id="stock-info" style="display: none;">
                                            <h5><i class="icon fas fa-info"></i> Stock Information</h5>
                                            <div id="stock-details"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Transaction
                                </button>
                                <a href="{{ route('supplies.transactions.index') }}" class="btn btn-secondary">
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
            // Update stock information when supply is selected
            $('#supply_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const supplyName = selectedOption.text();
                const currentStock = selectedOption.data('stock');
                const unit = selectedOption.data('unit');

                if ($(this).val()) {
                    $('#stock-info').show();
                    $('#stock-details').html(`
                <p><strong>Supply:</strong> ${supplyName}</p>
                <p><strong>Current Stock:</strong> ${currentStock} ${unit}</p>
            `);
                } else {
                    $('#stock-info').hide();
                }
            });

            // Update quantity help text when type is selected
            $('#type').on('change', function() {
                const type = $(this).val();
                const helpText = $('#quantity-help');

                if (type === 'in') {
                    helpText.text('Enter the quantity to add to stock');
                } else if (type === 'out') {
                    helpText.text('Enter the quantity to remove from stock');
                } else {
                    helpText.text('Enter the quantity to adjust');
                }
            });

            // Validate quantity for stock out transactions
            $('#quantity').on('input', function() {
                const type = $('#type').val();
                const quantity = parseInt($(this).val());
                const selectedOption = $('#supply_id').find('option:selected');
                const currentStock = selectedOption.data('stock');

                if (type === 'out' && quantity > currentStock) {
                    $(this).addClass('is-invalid');
                    $(this).next('.form-text').html(`
                <span class="text-danger">Insufficient stock! Available: ${currentStock}</span>
            `);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.form-text').html(`
                <span id="quantity-help">Enter the quantity to adjust</span>
            `);
                }
            });

            // Form validation
            $('form').on('submit', function(e) {
                const type = $('#type').val();
                const quantity = parseInt($('#quantity').val());
                const selectedOption = $('#supply_id').find('option:selected');
                const currentStock = selectedOption.data('stock');

                if (type === 'out' && quantity > currentStock) {
                    e.preventDefault();
                    toastr.error(`Insufficient stock! Available: ${currentStock}`);
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

            // Trigger change events for initial state
            $('#supply_id').trigger('change');
            $('#type').trigger('change');
        });
    </script>
@endpush
