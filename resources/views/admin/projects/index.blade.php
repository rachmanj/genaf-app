@extends('layouts.main')

@section('title_page', 'Projects Management')

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Projects</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-building mr-1"></i>
                            Projects Management
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i>
                                Add New Project
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="tbl-projects" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Code</th>
                                    <th>Owner</th>
                                    <th>Location</th>
                                    <th>Status</th>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-projects').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.projects.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'owner',
                        name: 'owner'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });

            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@endpush
