@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   VEHICLE ASSIGNMENT HEADER
========================================================= */

.vehicle-assignment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px 18px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfffd 100%);
}

.vehicle-assignment-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.vehicle-assignment-title-icon {
    width: 46px;
    height: 46px;
    flex: 0 0 46px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 13px;

    background: linear-gradient(
        135deg,
        rgba(25, 135, 84, 0.16),
        rgba(25, 135, 84, 0.05)
    );

    color: #198754;
    font-size: 21px;
}

.vehicle-assignment-header .card-title {
    color: #263238;
    font-size: 20px;
    font-weight: 700;
}

.vehicle-assignment-subtitle {
    display: block;
    margin-top: 4px;

    color: #8b9690;
    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   ADD BUTTON
========================================================= */

.vehicle-assignment-header-actions {
    display: flex;
    align-items: center;
}

.vehicle-assignment-add-btn {
    min-height: 42px;
    padding: 0 16px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    border-radius: 10px;

    color: #fff !important;
    background: linear-gradient(135deg, #198754, #157347);

    text-decoration: none !important;

    font-size: 13px;
    font-weight: 600;

    box-shadow: 0 5px 14px rgba(25, 135, 84, 0.18);

    transition: all 0.2s ease;
}

.vehicle-assignment-add-btn:hover {
    color: #fff !important;
    transform: translateY(-2px);

    box-shadow: 0 8px 18px rgba(25, 135, 84, 0.25);
}

.vehicle-assignment-btn-icon {
    width: 24px;
    height: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: rgba(255, 255, 255, 0.16);
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.vehicle-assignment-table-wrapper {
    margin: 8px 20px 20px;

    border: 1px solid #e7eee9;
    border-radius: 14px;

    overflow: hidden;

    background: #fff;
}

.vehicle-assignment-table-responsive {
    width: 100%;
    overflow-x: auto;

    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.vehicle-assignment-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.vehicle-assignment-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.vehicle-assignment-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.vehicle-assignment-responsive-table {
    width: 100%;
    min-width: 1050px;

    table-layout: fixed;

    margin: 0 !important;
}

.vehicle-assignment-responsive-table thead th {
    height: 52px;
    padding: 12px 13px;

    background: #f7faf8 !important;

    border-bottom: 1px solid #e3ebe6 !important;

    color: #52605a;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.35px;

    vertical-align: middle;
    white-space: nowrap;
}

.vehicle-assignment-responsive-table tbody td {
    height: 66px;
    padding: 10px 13px;

    border-bottom: 1px solid #edf2ef !important;

    color: #4f5b56;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;

    background: #fff;
}

.vehicle-assignment-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.vehicle-assignment-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.vehicle-assignment-agent-column {
    width: 230px;
    min-width: 200px;
}

.vehicle-assignment-number-column {
    width: 190px;
    min-width: 170px;
}

.vehicle-assignment-type-column {
    width: 190px;
    min-width: 170px;
}

.vehicle-assignment-license-column {
    width: 190px;
    min-width: 170px;
}

.vehicle-assignment-status-column {
    width: 120px;
    min-width: 120px;
}

.vehicle-assignment-action-column {
    width: 145px;
    min-width: 145px;
}


/* =========================================================
   ROW HOVER
========================================================= */

.vehicle-assignment-table-row {
    transition: background-color 0.18s ease;
}

.vehicle-assignment-table-row:hover td {
    background: #f8fcfa !important;
}

.vehicle-assignment-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.vehicle-assignment-sr-badge {
    min-width: 30px;
    height: 30px;
    padding: 0 8px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #f0f7f3;
    color: #198754;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   AGENT
========================================================= */

.vehicle-assignment-agent-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.vehicle-assignment-agent-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #eaf7ef;
    color: #198754;
}

.vehicle-assignment-agent-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;
    font-weight: 600;
}


/* =========================================================
   INFORMATION CELLS
========================================================= */

.vehicle-assignment-info-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;

    min-width: 0;
}

.vehicle-assignment-info-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef7f2;
    color: #198754;

    font-size: 13px;
}

.vehicle-assignment-info-icon.type-icon {
    background: #eef4ff;
    color: #0d6efd;
}

.vehicle-assignment-info-icon.license-icon {
    background: #fff5dc;
    color: #a66a00;
}

.vehicle-assignment-vehicle-no {
    color: #3f4c46;
    font-weight: 700;
}

.vehicle-assignment-license {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}


/* =========================================================
   STATUS
========================================================= */

.vehicle-assignment-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    min-width: 68px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 0.3px;
}

.vehicle-assignment-status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: currentColor;
}

.vehicle-assignment-status.active {
    color: #198754;
    background: #eaf7ef;
}

.vehicle-assignment-status.inactive {
    color: #dc3545;
    background: #fdecef;
}


/* =========================================================
   ACTIONS
========================================================= */

.vehicle-assignment-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.vehicle-assignment-delete-form {
    display: inline-flex;
    margin: 0 !important;
}

.vehicle-assignment-action-btn {
    width: 32px;
    height: 32px;

    padding: 0 !important;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px !important;

    border: 1px solid transparent !important;

    font-size: 14px;

    text-decoration: none !important;

    transition: all 0.18s ease;
}


/* View */

.vehicle-assignment-view-btn {
    color: #0d6efd !important;
    background: #eaf2ff !important;
    border-color: #b8d3ff !important;
}

.vehicle-assignment-view-btn:hover {
    color: #fff !important;
    background: #0d6efd !important;
    transform: translateY(-1px);
}


/* Edit */

.vehicle-assignment-edit-btn {
    color: #a66a00 !important;
    background: #fff5dc !important;
    border-color: #f2d48d !important;
}

.vehicle-assignment-edit-btn:hover {
    color: #fff !important;
    background: #f0ad00 !important;
    transform: translateY(-1px);
}


/* Delete */

.vehicle-assignment-delete-btn {
    color: #dc3545 !important;
    background: #fff1f2 !important;
    border-color: #f5c2c7 !important;
}

.vehicle-assignment-delete-btn:hover {
    color: #fff !important;
    background: #dc3545 !important;
    transform: translateY(-1px);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.vehicle-assignment-empty-cell {
    padding: 55px 20px !important;
    text-align: center !important;
}

.vehicle-assignment-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.vehicle-assignment-empty-icon {
    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 16px;

    background: #f0f7f3;
    color: #198754;

    font-size: 25px;
}

.vehicle-assignment-empty-title {
    color: #44514b;

    font-size: 15px;
    font-weight: 700;
}

.vehicle-assignment-empty-text {
    margin-top: 4px;

    color: #98a29d;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .vehicle-assignment-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .vehicle-assignment-header-actions {
        width: 100%;
    }

    .vehicle-assignment-add-btn {
        width: 100%;
    }

    .vehicle-assignment-table-wrapper {
        margin: 8px 12px 15px;
    }
}


@media (max-width: 575.98px) {

    .vehicle-assignment-header {
        padding: 18px 15px 15px;
    }

    .vehicle-assignment-title-row {
        gap: 10px;
    }

    .vehicle-assignment-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;

        font-size: 18px;
        border-radius: 11px;
    }

    .vehicle-assignment-header .card-title {
        font-size: 17px;
    }

    .vehicle-assignment-subtitle {
        font-size: 11px;
    }

    .vehicle-assignment-table-wrapper {
        margin: 5px 8px 10px;
        border-radius: 11px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission( 'vehical_assignment.view');
            $canEdit = hasPermission('vehical_assignment.edit');
            $canDelete = hasPermission('vehical_assignment.delete');
            @endphp

            <!-- ================= HEADER ================= -->
            <div class="vehicle-assignment-header">

                <div class="vehicle-assignment-header-title">
                    <div class="vehicle-assignment-title-row">

                        <div class="vehicle-assignment-title-icon">
                            <i class="bi bi-car-front-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Vehicle Assignment
                            </h4>

                            <span class="vehicle-assignment-subtitle">
                                Manage delivery agents and their assigned vehicles
                            </span>
                        </div>

                    </div>
                </div>

                <div class="vehicle-assignment-header-actions">

                    <a href="{{ route('vehicle-assignments.create') }}"
                    class="vehicle-assignment-add-btn">

                        <span class="vehicle-assignment-btn-icon">
                            <i class="bi bi-plus-lg"></i>
                        </span>

                        <span>Assign Vehicle</span>

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
            
            <!-- ================= TABLE ================= -->
            <div class="vehicle-assignment-table-wrapper">

                <div class="vehicle-assignment-table-responsive">

                    <table id="driverVehicleTable"
                        class="table vehicle-assignment-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center vehicle-assignment-sr-column">
                                    Sr No
                                </th>

                                <th class="vehicle-assignment-agent-column">
                                    Agent Name
                                </th>

                                <th class="vehicle-assignment-number-column">
                                    Vehicle No
                                </th>

                                <th class="vehicle-assignment-type-column">
                                    Vehicle Type
                                </th>

                                <th class="vehicle-assignment-license-column">
                                    License No
                                </th>

                                <th class="text-center vehicle-assignment-status-column">
                                    Status
                                </th>

                                @if($canView || $canEdit || $canDelete)

                                    <th class="text-center vehicle-assignment-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($driverVehicles as $driverVehicle)

                                <tr class="vehicle-assignment-table-row">

                                    <!-- Sr No -->
                                    <td class="text-center">

                                        <span class="vehicle-assignment-sr-badge">
                                            {{ $loop->iteration }}
                                        </span>

                                    </td>


                                    <!-- Agent Name -->
                                    <td>

                                        <div class="vehicle-assignment-agent-wrapper">

                                            <div class="vehicle-assignment-agent-icon">
                                                <i class="bi bi-person-badge"></i>
                                            </div>

                                            <span class="vehicle-assignment-agent-name"
                                                title="{{ $driverVehicle->driver?->first_name }} {{ $driverVehicle->driver?->last_name }}">

                                                {{ $driverVehicle->driver?->first_name }}
                                                {{ $driverVehicle->driver?->last_name }}

                                            </span>

                                        </div>

                                    </td>


                                    <!-- Vehicle No -->
                                    <td>

                                        <div class="vehicle-assignment-info-wrapper">

                                            <span class="vehicle-assignment-info-icon">
                                                <i class="bi bi-car-front"></i>
                                            </span>

                                            <span class="vehicle-assignment-vehicle-no">
                                                {{ $driverVehicle->vehicle_no }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Vehicle Type -->
                                    <td>

                                        <div class="vehicle-assignment-info-wrapper">

                                            <span class="vehicle-assignment-info-icon type-icon">
                                                <i class="bi bi-truck"></i>
                                            </span>

                                            <span>
                                                {{ $driverVehicle->vehicle_type ?? '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- License No -->
                                    <td>

                                        <div class="vehicle-assignment-info-wrapper">

                                            <span class="vehicle-assignment-info-icon license-icon">
                                                <i class="bi bi-card-text"></i>
                                            </span>

                                            <span class="vehicle-assignment-license">
                                                {{ $driverVehicle->license_no ?? '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Status -->
                                    <td class="text-center">

                                        @if(($driverVehicle->active ?? 0) == 1)

                                            <span class="vehicle-assignment-status active">

                                                <span class="vehicle-assignment-status-dot"></span>

                                                Yes

                                            </span>

                                        @else

                                            <span class="vehicle-assignment-status inactive">

                                                <span class="vehicle-assignment-status-dot"></span>

                                                No

                                            </span>

                                        @endif

                                    </td>


                                    <!-- Actions -->
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center">

                                            <div class="vehicle-assignment-actions">

                                                @if(hasPermission('vehical_assignment.view'))

                                                    <a href="{{ route('vehicle-assignments.show', $driverVehicle->id) }}"
                                                    class="vehicle-assignment-action-btn vehicle-assignment-view-btn"
                                                    title="View Vehicle Assignment">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('vehical_assignment.edit'))

                                                    <a href="{{ route('vehicle-assignments.edit', $driverVehicle->id) }}"
                                                    class="vehicle-assignment-action-btn vehicle-assignment-edit-btn"
                                                    title="Edit Vehicle Assignment">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('vehical_assignment.delete'))

                                                    <form action="{{ route('vehicle-assignments.destroy', $driverVehicle->id) }}"
                                                        method="POST"
                                                        class="vehicle-assignment-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete vehical_assignment?')"
                                                                class="vehicle-assignment-action-btn vehicle-assignment-delete-btn"
                                                                title="Delete Vehicle Assignment">

                                                            <i class="bi bi-trash3"></i>

                                                        </button>

                                                    </form>

                                                @endif

                                            </div>

                                        </td>

                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ ($canView || $canEdit || $canDelete) ? 7 : 6 }}"
                                        class="vehicle-assignment-empty-cell">

                                        <div class="vehicle-assignment-empty-state">

                                            <div class="vehicle-assignment-empty-icon">
                                                <i class="bi bi-car-front"></i>
                                            </div>

                                            <div class="vehicle-assignment-empty-title">
                                                No Vehicle Assignments Found
                                            </div>

                                            <div class="vehicle-assignment-empty-text">
                                                There are no vehicle assignments available at the moment.
                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

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