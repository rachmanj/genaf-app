@extends('layouts.main')

@section('title_page')
    Permissions
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permissions</h3>
                <div class="card-tools">
                    @can('create permissions')
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Permission
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <script>
                        toastr.success(@json(session('success')));
                    </script>
                @endif
                @if (session('error'))
                    <script>
                        toastr.error(@json(session('error')));
                    </script>
                @endif
                <table class="table table-bordered table-striped" id="perms-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Roles Count</th>
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
@endsection

@section('scripts')
    <script>
        $(function() {
            var table = $('#perms-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.permissions.index') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'roles_count',
                        name: 'roles_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#perms-table').on('click', '.edit-permission', function() {
                var id = $(this).data('id');
                window.location.href = '/admin/permissions/' + id + '/edit';
            });

            $('#perms-table').on('click', '.delete-permission', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Delete permission?',
                    icon: 'warning',
                    showCancelButton: true
                }).then((res) => {
                    if (res.isConfirmed) {
                        var f = $('<form method="POST" action="/admin/permissions/' + id +
                            '">@csrf<input type="hidden" name="_method" value="DELETE"></form>');
                        $('body').append(f);
                        f.submit();
                    }
                })
            });
        });
    </script>
@endsection
