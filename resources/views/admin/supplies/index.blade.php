@extends('layouts.main')

@section('title', 'Supplies Management')

@section('title_page')
    Supplies Management
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Supplies</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box mr-1"></i>
                                Supplies Management
                            </h3>
                            <div class="card-tools">
                                @can('create supplies')
                                    <a href="{{ route('supplies.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i>
                                        Add New Supply
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-category">Filter by Category:</label>
                                    <select id="filter-category" class="form-control form-control-sm">
                                        <option value="">All Categories</option>
                                        <option value="ATK">ATK - Alat Tulis Kantor</option>
                                        <option value="Cleaning">Cleaning - Peralatan Kebersihan</option>
                                        <option value="Pantry">Pantry - Perlengkapan Dapur</option>
                                        <option value="IT">IT - Perlengkapan IT</option>
                                        <option value="Office">Office - Perlengkapan Kantor</option>
                                        <option value="Other">Other - Lain-lain</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-status">Filter by Status:</label>
                                    <select id="filter-status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-unit">Filter by Unit:</label>
                                    <select id="filter-unit" class="form-control form-control-sm">
                                        <option value="">All Units</option>
                                        <option value="pcs">Pieces</option>
                                        <option value="box">Box</option>
                                        <option value="pack">Pack</option>
                                        <option value="roll">Roll</option>
                                        <option value="bottle">Bottle</option>
                                        <option value="kg">Kilogram</option>
                                        <option value="liter">Liter</option>
                                        <option value="meter">Meter</option>
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
                            <!-- End Filters -->
                            <table class="table table-bordered table-striped" id="tbl-supplies">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th class="text-right">Current Stock</th>
                                        <th class="text-right">Min Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTables will populate this --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script>
        $(function() {
            // Handle session notifications with Toastr
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
            $('#tbl-supplies').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('supplies.index') }}',
                columns: [{
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'code',
                        name: 'code',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'current_stock',
                        name: 'current_stock',
                        className: 'text-right',
                        render: function(data, type, row) {
                            return '<span class="badge badge-primary">' + data + '</span>';
                        }
                    },
                    {
                        data: 'min_stock',
                        name: 'min_stock',
                        className: 'text-right'
                    },
                    {
                        data: 'stock_status',
                        name: 'stock_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var html = '';
                            html += '<a href="/supplies/' + row.id +
                                '" class="btn btn-sm btn-info mr-1" title="View">';
                            html += '<i class="fas fa-eye"></i></a>';

                            @can('edit supplies')
                                html += '<a href="/supplies/' + row.id +
                                    '/edit" class="btn btn-sm btn-warning mr-1" title="Edit">';
                                html += '<i class="fas fa-edit"></i></a>';
                            @endcan

                            @can('delete supplies')
                                html +=
                                    '<button class="btn btn-sm btn-danger delete-supply" data-id="' +
                                    row.id + '" title="Delete">';
                                html += '<i class="fas fa-trash"></i></button>';
                            @endcan

                            return html;
                        }
                    }
                ],
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false
            });

            // Filter functionality
            var table = $('#tbl-supplies').DataTable();

            // Category filter
            $('#filter-category').on('change', function() {
                var category = $(this).val();
                if (category === '') {
                    table.column(3).search('').draw();
                } else {
                    table.column(3).search('^' + category + '$', true, false).draw();
                }
            });

            // Status filter
            $('#filter-status').on('change', function() {
                var status = $(this).val();
                if (status === '') {
                    table.column(7).search('').draw();
                } else {
                    table.column(7).search('^' + status + '$', true, false).draw();
                }
            });

            // Unit filter
            $('#filter-unit').on('change', function() {
                var unit = $(this).val();
                if (unit === '') {
                    table.column(4).search('').draw();
                } else {
                    table.column(4).search('^' + unit + '$', true, false).draw();
                }
            });

            // Clear filters
            $('#clear-filters').on('click', function() {
                $('#filter-category').val('').trigger('change');
                $('#filter-status').val('').trigger('change');
                $('#filter-unit').val('').trigger('change');
            });

            // Delete supply functionality
            $('#tbl-supplies').on('click', '.delete-supply', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Supply?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('<form method="POST" action="/supplies/' + id + '">' +
                            '@csrf' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '</form>');
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
