@extends('layouts.main')

@section('title_page', 'Stock Transactions')
@section('breadcrumb_title', 'Stock Transactions')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Stock Transactions Management</h3>
                            @can('create supply transactions')
                                <div class="card-tools">
                                    <a href="{{ route('supplies.transactions.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add New Transaction
                                    </a>
                                </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-type">Filter by Type:</label>
                                    <select id="filter-type" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="in">Stock In</option>
                                        <option value="out">Stock Out</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-supply">Filter by Supply:</label>
                                    <select id="filter-supply" class="form-control form-control-sm">
                                        <option value="">All Supplies</option>
                                        @foreach (\App\Models\Supply::orderBy('name')->get() as $supply)
                                            <option value="{{ $supply->name }}">{{ $supply->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-user">Filter by User:</label>
                                    <select id="filter-user" class="form-control form-control-sm">
                                        <option value="">All Users</option>
                                        @foreach (\App\Models\User::orderBy('name')->get() as $user)
                                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="clear-filters" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <table id="tbl-supply-transactions" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Transaction ID</th>
                                        <th>Supply Item</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th class="text-right">Quantity</th>
                                        <th>Reference No</th>
                                        <th>Transaction Date</th>
                                        <th>User</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-supply-transactions').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('supplies.transactions.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
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
                        data: 'type_badge',
                        name: 'type'
                    },
                    {
                        data: 'quantity_formatted',
                        name: 'quantity',
                        className: 'text-right'
                    },
                    {
                        data: 'reference_no',
                        name: 'reference_no'
                    },
                    {
                        data: 'transaction_date_formatted',
                        name: 'transaction_date'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading..."
                }
            });


            // Type filter
            $('#filter-type').on('change', function() {
                var type = $(this).val();
                if (type === '') {
                    table.column(4).search('').draw();
                } else {
                    table.column(4).search('^' + type + '$', true, false).draw();
                }
            });

            // Supply filter
            $('#filter-supply').on('change', function() {
                var supply = $(this).val();
                if (supply === '') {
                    table.column(2).search('').draw();
                } else {
                    table.column(2).search('^' + supply + '$', true, false).draw();
                }
            });

            // User filter
            $('#filter-user').on('change', function() {
                var user = $(this).val();
                if (user === '') {
                    table.column(8).search('').draw();
                } else {
                    table.column(8).search('^' + user + '$', true, false).draw();
                }
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#filter-type').val('').trigger('change');
                $('#filter-supply').val('').trigger('change');
                $('#filter-user').val('').trigger('change');
            });

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
                                table.ajax.reload();
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
@stop
