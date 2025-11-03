@extends('layouts.main')

@section('title_page', 'Stock Transaction Details')
@section('breadcrumb_title', 'Stock Transaction Details')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stock Transaction #{{ $supplyTransaction->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('supplies.transactions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                @can('delete supply transactions')
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deleteTransaction({{ $supplyTransaction->id }})">
                                        <i class="fas fa-trash"></i> Delete Transaction
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Transaction ID:</dt>
                                        <dd class="col-sm-8">#{{ $supplyTransaction->id }}</dd>

                                        <dt class="col-sm-4">Supply Item:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->supply->name ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Supply Code:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->supply->code ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Transaction Type:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $badgeClass =
                                                    $supplyTransaction->type === 'in'
                                                        ? 'badge-success'
                                                        : 'badge-danger';
                                                $icon =
                                                    $supplyTransaction->type === 'in' ? 'fa-arrow-up' : 'fa-arrow-down';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                <i class="fas {{ $icon }}"></i>
                                                {{ strtoupper($supplyTransaction->type) }}
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Quantity:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $sign = $supplyTransaction->type === 'in' ? '+' : '-';
                                            @endphp
                                            <span
                                                class="font-weight-bold">{{ $sign }}{{ number_format($supplyTransaction->quantity) }}
                                                {{ $supplyTransaction->supply->unit ?? '' }}</span>
                                        </dd>

                                        <dt class="col-sm-4">Reference No:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->reference_no ?? 'N/A' }}</dd>

                                        <dt class="col-sm-4">Transaction Date:</dt>
                                        <dd class="col-sm-8">
                                            {{ $supplyTransaction->transaction_date ? $supplyTransaction->transaction_date->format('d/m/Y') : 'N/A' }}
                                        </dd>

                                        <dt class="col-sm-4">Recorded By:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->user->name ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>

                            @if ($supplyTransaction->notes)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5>Notes:</h5>
                                        <p class="text-muted">{{ $supplyTransaction->notes }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <h5>Supply Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Current Stock:</dt>
                                        <dd class="col-sm-8">
                                            {{ number_format($supplyTransaction->supply->current_stock ?? 0) }}
                                            {{ $supplyTransaction->supply->unit ?? '' }}</dd>

                                        <dt class="col-sm-4">Minimum Stock:</dt>
                                        <dd class="col-sm-8">
                                            {{ number_format($supplyTransaction->supply->min_stock ?? 0) }}
                                            {{ $supplyTransaction->supply->unit ?? '' }}</dd>

                                        <dt class="col-sm-4">Category:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->supply->category ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Stock Status:</dt>
                                        <dd class="col-sm-8">
                                            @php
                                                $supply = $supplyTransaction->supply;
                                                $stockStatus = 'in_stock';
                                                if ($supply && $supply->current_stock <= 0) {
                                                    $stockStatus = 'out_of_stock';
                                                } elseif ($supply && $supply->current_stock <= $supply->min_stock) {
                                                    $stockStatus = 'low_stock';
                                                }

                                                $badgeClass = match ($stockStatus) {
                                                    'in_stock' => 'badge-success',
                                                    'low_stock' => 'badge-warning',
                                                    'out_of_stock' => 'badge-danger',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $stockStatus)) }}</span>
                                        </dd>

                                        <dt class="col-sm-4">Price:</dt>
                                        <dd class="col-sm-8">
                                            @if ($supplyTransaction->supply && $supplyTransaction->supply->price)
                                                Rp {{ number_format($supplyTransaction->supply->price, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Description:</dt>
                                        <dd class="col-sm-8">{{ $supplyTransaction->supply->description ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <a href="{{ route('supplies.show', $supplyTransaction->supply) }}"
                                        class="btn btn-info">
                                        <i class="fas fa-eye"></i> View Supply Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Delete transaction
            window.deleteTransaction = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this! This will also reverse the stock change.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('supplies/transactions') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('supplies.transactions.index') }}";
                                }, 1500);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON.error ||
                                    'Failed to delete transaction');
                            }
                        });
                    }
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
