@extends('layouts.main')

@section('title', 'Supply Details')

@section('title_page')
    Supply Details
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('supplies.index') }}">Supplies</a></li>
    <li class="breadcrumb-item active">{{ $supply->name }}</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box mr-1"></i>
                                {{ $supply->name }}
                            </h3>
                            <div class="card-tools">
                                @can('edit supplies')
                                    <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Code:</dt>
                                        <dd class="col-sm-8"><strong>{{ $supply->code }}</strong></dd>

                                        <dt class="col-sm-4">Category:</dt>
                                        <dd class="col-sm-8">{{ $supply->category }}</dd>

                                        <dt class="col-sm-4">Unit:</dt>
                                        <dd class="col-sm-8">{{ $supply->unit }}</dd>

                                        <dt class="col-sm-4">Price:</dt>
                                        <dd class="col-sm-8">
                                            @if ($supply->price)
                                                Rp {{ number_format($supply->price, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row">
                                        <dt class="col-sm-4">Current Stock:</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge badge-primary">{{ $supply->current_stock }}</span>
                                        </dd>

                                        <dt class="col-sm-4">Min Stock:</dt>
                                        <dd class="col-sm-8">{{ $supply->min_stock }}</dd>

                                        <dt class="col-sm-4">Status:</dt>
                                        <dd class="col-sm-8">
                                            @if ($supply->stock_status == 'out_of_stock')
                                                <span class="badge badge-danger">Out of Stock</span>
                                            @elseif($supply->stock_status == 'low_stock')
                                                <span class="badge badge-warning">Low Stock</span>
                                            @else
                                                <span class="badge badge-success">In Stock</span>
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Created:</dt>
                                        <dd class="col-sm-8">{{ $supply->created_at->format('d M Y H:i') }}</dd>
                                    </dl>
                                </div>
                            </div>

                            @if ($supply->description)
                                <div class="row">
                                    <div class="col-12">
                                        <h5>Description</h5>
                                        <p>{{ $supply->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                Recent Transactions
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($supply->transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Quantity</th>
                                                <th>Reference</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($supply->transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->date->format('d M Y') }}</td>
                                                    <td>
                                                        @if ($transaction->type == 'in')
                                                            <span class="badge badge-success">Stock In</span>
                                                        @else
                                                            <span class="badge badge-danger">Stock Out</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $transaction->quantity }}</td>
                                                    <td>{{ $transaction->reference_no }}</td>
                                                    <td>{{ $transaction->notes ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No transactions recorded yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Stock Status Card -->
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Stock Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="description-block">
                                        <span class="description-percentage text-primary">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                        <h5 class="description-header">{{ $supply->current_stock }}</h5>
                                        <span class="description-text">Current Stock</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="description-block">
                                        <span class="description-percentage text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                        <h5 class="description-header">{{ $supply->min_stock }}</h5>
                                        <span class="description-text">Min Stock</span>
                                    </div>
                                </div>
                            </div>

                            @if ($supply->isLowStock())
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Low Stock Alert!</strong><br>
                                    Current stock is below minimum level.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('supplies.index') }}" class="btn btn-secondary mb-2">
                                    <i class="fas fa-list"></i> Back to List
                                </a>
                                @can('edit supplies')
                                    <a href="{{ route('supplies.edit', $supply) }}" class="btn btn-warning mb-2">
                                        <i class="fas fa-edit"></i> Edit Supply
                                    </a>
                                @endcan
                                @can('create supply transactions')
                                    <button type="button" class="btn btn-success mb-2" onclick="showStockInModal()">
                                        <i class="fas fa-plus"></i> Stock In
                                    </button>
                                    <button type="button" class="btn btn-danger mb-2" onclick="showStockOutModal()">
                                        <i class="fas fa-minus"></i> Stock Out
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        function showStockInModal() {
            // TODO: Implement stock in modal
            alert('Stock In functionality will be implemented in the next phase');
        }

        function showStockOutModal() {
            // TODO: Implement stock out modal
            alert('Stock Out functionality will be implemented in the next phase');
        }
    </script>
@endpush
