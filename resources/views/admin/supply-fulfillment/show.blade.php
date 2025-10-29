@extends('layouts.main')

@section('title_page', 'Fulfill Supply Request')
@section('breadcrumb_title', 'Fulfill Supply Request')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Fulfill Supply Request #{{ $request->id }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Request Details</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Request ID:</strong></td>
                                            <td>#{{ $request->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Employee:</strong></td>
                                            <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td>{{ $request->department->department_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Request Date:</strong></td>
                                            <td>{{ $request->request_date ? $request->request_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span
                                                    class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Notes</h5>
                                    <p>{{ $request->notes ?: 'No notes provided' }}</p>
                                </div>
                            </div>

                            <hr>

                            <h5>Request Items</h5>
                            <form action="{{ route('supplies.fulfillment.fulfill', $request) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Supply Item</th>
                                                <th>Code</th>
                                                <th>Requested Qty</th>
                                                <th>Approved Qty</th>
                                                <th>Current Stock</th>
                                                <th>Fulfill Qty</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($request->items as $item)
                                                <tr>
                                                    <td>{{ $item->supply->name ?? 'N/A' }}</td>
                                                    <td>{{ $item->supply->code ?? 'N/A' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ $item->approved_quantity ?? 0 }}</td>
                                                    <td>{{ $item->supply->current_stock ?? 0 }}</td>
                                                    <td>
                                                        <input type="number"
                                                            name="items[{{ $loop->index }}][fulfill_quantity]"
                                                            value="{{ $item->approved_quantity ?? 0 }}" min="0"
                                                            max="{{ $item->approved_quantity ?? 0 }}"
                                                            class="form-control form-control-sm" required>
                                                        <input type="hidden" name="items[{{ $loop->index }}][item_id]"
                                                            value="{{ $item->id }}">
                                                    </td>
                                                    <td>{{ $item->supply->unit ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Fulfillment Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3"
                                        placeholder="Add any notes about this fulfillment..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="distribution_date">Distribution Date</label>
                                    <input type="date" name="distribution_date" id="distribution_date"
                                        class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Complete Fulfillment
                                    </button>
                                    <a href="{{ route('supplies.fulfillment.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            </form>
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
