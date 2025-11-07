@extends('layouts.main')

@section('title_page', 'Rejected Distributions')
@section('breadcrumb_title', 'Rejected Distributions')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rejected Distributions</h3>
                            <p class="text-muted mb-0">Review distributions that were rejected by requestors</p>
                        </div>
                        <div class="card-body">
                            <table id="tbl-rejected-distributions" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Number</th>
                                        <th>Supply Item</th>
                                        <th>Code</th>
                                        <th>Quantity</th>
                                        <th>Department</th>
                                        <th>Requestor</th>
                                        <th>Request Reference</th>
                                        <th>Distribution Date</th>
                                        <th>Rejected At</th>
                                        <th>Rejected By</th>
                                        <th>Rejection Reason</th>
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

@push('js')
    <script>
        $(document).ready(function() {
            var table = $('#tbl-rejected-distributions').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplies.fulfillment.rejected-distributions') }}",
                columns: [{
                        data: 'index',
                        name: 'index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'form_number',
                        name: 'form_number'
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
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'requestor_name',
                        name: 'requestor_name'
                    },
                    {
                        data: 'request_reference',
                        name: 'request_reference'
                    },
                    {
                        data: 'distribution_date',
                        name: 'distribution_date'
                    },
                    {
                        data: 'rejected_at',
                        name: 'rejected_at'
                    },
                    {
                        data: 'rejected_by_name',
                        name: 'rejected_by_name'
                    },
                    {
                        data: 'rejection_reason',
                        name: 'rejection_reason'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [9, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                autoWidth: false,
            });
        });
    </script>
@endpush
