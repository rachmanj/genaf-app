@extends('layouts.main')

@section('title_page', 'Departments Management')

@section('breadcrumb_title', 'Departments')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Departments List</h3>
                        <div class="card-tools">
                            @can('create departments')
                                <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Department
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tbl-departments" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Users Count</th>
                                    <th>Requests Count</th>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#tbl-departments').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('departments.index') }}",
        columns: [
            {data: 'index', name: 'index', orderable: false, searchable: false},
            {data: 'department_name', name: 'department_name'},
            {data: 'slug', name: 'slug'},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'users_count', name: 'users_count', orderable: false, searchable: false},
            {data: 'requests_count', name: 'requests_count', orderable: false, searchable: false},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        responsive: true,
        autoWidth: false,
    });

    // Handle toggle status
    $(document).on('click', '.toggle-status', function() {
        var departmentId = $(this).data('id');
        var currentStatus = $(this).data('status');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${currentStatus ? 'deactivate' : 'activate'} this department?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, do it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/departments/${departmentId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while updating the department status.');
                    }
                });
            }
        });
    });
});
</script>
@endpush