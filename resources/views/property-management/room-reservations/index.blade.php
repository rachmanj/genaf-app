@extends('layouts.main')

@section('title', 'Reservations')

@section('title_page')
    Reservations
@endsection

@section('breadcrumb_title')
    <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Reservations</li>
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fas fa-calendar-check mr-1"></i> Reservations</h3>
                            <div class="card-tools">
                                <a href="{{ route('pms.reservations.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> New Reservation
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Form Number</th>
                                            <th>Building</th>
                                            <th>Room</th>
                                            <th>Guest</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reservations as $i => $res)
                                            <tr>
                                                <td>{{ $reservations->firstItem() + $i }}</td>
                                                <td>{{ $res->form_number }}</td>
                                                <td>{{ $res->room?->building?->name }}</td>
                                                <td>{{ $res->room?->room_number }}</td>
                                                <td>{{ $res->guest_name }}</td>
                                                <td>{{ $res->check_in->format('Y-m-d') }} → {{ $res->check_out->format('Y-m-d') }}</td>
                                                <td>{{ ucfirst($res->status) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No reservations.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $reservations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


