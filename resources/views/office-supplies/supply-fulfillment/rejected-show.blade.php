@extends('layouts.main')

@section('title_page', 'Rejected Distribution Details')
@section('breadcrumb_title', 'Rejected Distribution Details')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title">Rejected Distribution Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Distribution Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Form Number</th>
                                            <td>{{ $distribution->form_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supply Item</th>
                                            <td>{{ $distribution->supply->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Supply Code</th>
                                            <td>{{ $distribution->supply->code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Quantity</th>
                                            <td>{{ $distribution->quantity }} {{ $distribution->supply->unit ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $distribution->department->department_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Distribution Date</th>
                                            <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Distributed By</th>
                                            <td>{{ $distribution->distributedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Distribution Notes</th>
                                            <td>{{ $distribution->notes ?? 'No notes' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Request Information</h5>
                                    @if($distribution->requestItem && $distribution->requestItem->request)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Request Form Number</th>
                                            <td>{{ $distribution->requestItem->request->form_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Requestor</th>
                                            <td>{{ $distribution->requestItem->request->employee->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Request Date</th>
                                            <td>{{ $distribution->requestItem->request->request_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved Quantity</th>
                                            <td>{{ $distribution->requestItem->approved_quantity ?? 0 }} {{ $distribution->supply->unit ?? '' }}</td>
                                        </tr>
                                    </table>
                                    @else
                                    <p class="text-muted">No request information available</p>
                                    @endif

                                    <h5 class="mt-3">Rejection Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Rejection Reason</th>
                                            <td>
                                                <div class="alert alert-danger mb-0">
                                                    {{ $distribution->rejection_reason ?? 'No reason provided' }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Rejected By</th>
                                            <td>{{ $distribution->verifiedBy->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Rejected At</th>
                                            <td>{{ $distribution->verified_at ? $distribution->verified_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Additional Notes</th>
                                            <td>{{ $distribution->verification_notes ?? 'No additional notes' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                @if($distribution->requestItem && $distribution->requestItem->request)
                                <a href="{{ route('supplies.fulfillment.show', $distribution->requestItem->request->id) }}" class="btn btn-primary">
                                    <i class="fas fa-redo"></i> Re-fulfill Request
                                </a>
                                @endif
                                <a href="{{ route('supplies.fulfillment.rejected-distributions') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
