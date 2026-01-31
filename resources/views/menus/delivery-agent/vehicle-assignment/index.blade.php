@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission( 'vehical_assignment.view');
            $canEdit = hasPermission('vehical_assignment.edit');
            $canDelete = hasPermission('vehical_assignment.delete');
            @endphp
            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Vehicle Assignment</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('vehicle-assignments.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        Assign Vehicle
                    </a>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>
            @if(session('success'))
            <div id="successAlert"
                class="alert alert-success alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <script>
                setTimeout(function() {
                    let alert = document.getElementById('successAlert');
                    if (alert) {
                        let bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 10000); // 15 seconds
            </script>
            @endif
            
            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="driverVehicleTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 25%;">Agent Name</th>
                            <th style="width: 25%;">Vehicle No</th>
                            <th style="width: 25%;">Vehicle Type</th>
                            <th style="width: 25%;">License No</th>
                            <th style="width: 25%;">Status</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center" style="width: 150px;">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($driverVehicles as $driverVehicle)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $driverVehicle->driver?->first_name }}
                                {{ $driverVehicle->driver?->last_name }}
                            </td>
                            <td>{{ $driverVehicle->vehicle_no }}</td>
                            <td>{{ $driverVehicle->vehicle_type ?? '-' }}</td>
                            <td>{{ $driverVehicle->license_no ?? '-' }}</td>

                            {{-- Active Status --}}
                            <td>
                                @if(($driverVehicle->active ?? 0) == 1)
                                <span class="btn btn-success btn-sm">
                                    Yes
                                </span>
                                @else
                                <span class="btn btn-danger btn-sm">
                                    No
                                </span>
                                @endif
                            </td>

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('vehical_assignment.view'))
                                <a href="{{ route('vehicle-assignments.show', $driverVehicle->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('vehical_assignment.edit'))
                                <a href="{{ route('vehicle-assignments.edit', $driverVehicle->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('vehical_assignment.delete'))
                                <form action="{{ route('vehicle-assignments.destroy', $driverVehicle->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete vehical_assignment?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No delivery agents found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <!-- Pagination -->
            <div class="px-3 py-2">

            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>


@endpush