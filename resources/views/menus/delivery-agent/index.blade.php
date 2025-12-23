@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Delivery Agent</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('delivery-agents.create') }}"
                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Agent
                    </a>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5">
                <table id="driverVehicleTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 25%;">Agent Name</th>
                            <th style="width: 25%;">Vehicle No</th>
                            <th style="width: 25%;">Vehicle Type</th>
                            <th style="width: 25%;">License No</th>
                            <th style="width: 25%;">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($driverVehicles as $driverVehicle)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $driverVehicle->driver->first_name }} {{ $driverVehicle->driver->last_name}}</td>
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
                            
                            <td class="text-center">
                                <x-action-buttons
                                    :view-url="route('delivery-agents.show', $driverVehicle->id)"
                                    :edit-url="route('delivery-agents.edit', $driverVehicle->id)"
                                    :delete-url="route('delivery-agents.destroy', $driverVehicle->id)" />
                            </td>
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