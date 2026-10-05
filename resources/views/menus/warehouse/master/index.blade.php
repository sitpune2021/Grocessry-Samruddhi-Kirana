@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   WAREHOUSE HEADER
========================================================= */

.warehouse-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    padding: 18px 20px;

    background: #ffffff;
    border-bottom: 1px solid #edf1ee;
}

.warehouse-header-title {
    min-width: 0;
}

.warehouse-title-row {
    display: flex;
    align-items: center;
    gap: 13px;
}

.warehouse-title-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: linear-gradient(
        135deg,
        #e8f7ef,
        #d8f0e3
    );

    color: #198754;

    font-size: 20px;

    box-shadow:
        0 4px 12px rgba(25, 135, 84, 0.10);
}

.warehouse-title-row .card-title {
    margin: 0;

    color: #202a24;

    font-size: 19px;
    font-weight: 700;
}

.warehouse-subtitle {
    display: block;

    margin-top: 3px;

    color: #7a8580;

    font-size: 12.5px;
}


/* =========================================================
   HEADER BUTTON
========================================================= */

.warehouse-header-actions {
    display: flex;
    align-items: center;
}

.warehouse-header-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    min-height: 40px;
    padding: 0 15px;

    border-radius: 9px;

    text-decoration: none !important;

    font-size: 13px;
    font-weight: 600;

    transition: all 0.2s ease;
}

.warehouse-add-btn {
    color: #ffffff !important;
    background: #198754;

    box-shadow:
        0 4px 12px rgba(25, 135, 84, 0.16);
}

.warehouse-add-btn:hover {
    color: #ffffff !important;
    background: #157347;

    transform: translateY(-1px);

    box-shadow:
        0 7px 16px rgba(25, 135, 84, 0.22);
}

.warehouse-btn-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: rgba(255, 255, 255, 0.16);
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.warehouse-table-wrapper {
    width: 100%;

    margin-top: 14px;

    background: #ffffff;

    border: 1px solid #e9efeb;
    border-radius: 12px;

    overflow: hidden;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.035);
}

.warehouse-table-responsive {
    width: 100%;

    overflow-x: auto;

    -webkit-overflow-scrolling: touch;
}

.warehouse-responsive-table {
    width: 100%;
    min-width: 1250px;

    margin: 0 !important;

    table-layout: fixed;

    border-collapse: separate;
    border-spacing: 0;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.warehouse-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.warehouse-name-column {
    width: 185px;
    min-width: 170px;
}

.warehouse-type-column {
    width: 145px;
    min-width: 130px;
}

.warehouse-address-column {
    width: 230px;
    min-width: 200px;
}

.warehouse-contact-column {
    width: 180px;
    min-width: 160px;
}

.warehouse-mobile-column {
    width: 155px;
    min-width: 145px;
}

.warehouse-email-column {
    width: 220px;
    min-width: 190px;
}

.warehouse-status-column {
    width: 105px;
    min-width: 105px;
    max-width: 105px;
}

.warehouse-action-column {
    width: 135px;
    min-width: 135px;
    max-width: 135px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.warehouse-responsive-table thead th {
    padding: 13px 12px;

    background: #f8faf9;

    border-top: 0;
    border-bottom: 1px solid #e3e9e5;

    color: #66716b;

    font-size: 11.5px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.3px;

    white-space: nowrap;

    vertical-align: middle;
}


/* =========================================================
   TABLE BODY
========================================================= */

.warehouse-responsive-table tbody td {
    padding: 12px;

    border-top: 0;
    border-bottom: 1px solid #edf1ee;

    color: #414a45;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;
}

.warehouse-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.warehouse-table-row {
    position: relative;

    transition: background 0.18s ease;
}

.warehouse-table-row:hover {
    background: #fbfefc;
}

.warehouse-table-row td:first-child {
    position: relative;
}

.warehouse-table-row td:first-child::before {
    content: "";

    position: absolute;

    left: 0;
    top: 8px;
    bottom: 8px;

    width: 3px;

    background: #198754;

    border-radius: 0 4px 4px 0;

    opacity: 0;

    transition: opacity 0.18s ease;
}

.warehouse-table-row:hover td:first-child::before {
    opacity: 1;
}


/* =========================================================
   SR BADGE
========================================================= */

.warehouse-sr-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 27px;

    padding: 0 8px;

    border-radius: 7px;

    background: #eef8f2;
    color: #198754;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   WAREHOUSE NAME
========================================================= */

.warehouse-name-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.warehouse-name-icon {
    width: 33px;
    height: 33px;
    flex: 0 0 33px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef8f2;
    color: #198754;

    font-size: 14px;
}

.warehouse-name {
    min-width: 0;

    color: #27332c;

    font-weight: 700;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   TYPE
========================================================= */

.warehouse-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    max-width: 100%;

    padding: 6px 9px;

    border-radius: 7px;

    background: #f1f6ff;
    color: #3977c8;

    font-size: 11.5px;
    font-weight: 700;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.warehouse-type-badge i {
    font-size: 13px;
}


/* =========================================================
   ADDRESS
========================================================= */

.warehouse-address-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;

    min-width: 0;
}

.warehouse-address-wrapper > i {
    flex: 0 0 auto;

    color: #198754;

    font-size: 14px;
}

.warehouse-address {
    min-width: 0;

    color: #5c6761;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   CONTACT PERSON
========================================================= */

.warehouse-contact-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 5px;
}

.warehouse-contact-wrapper:last-child {
    margin-bottom: 0;
}

.warehouse-contact-icon {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: #f1f8f4;
    color: #198754;

    font-size: 12px;
}

.warehouse-contact-name {
    color: #47524c;

    font-size: 12.5px;
    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   PHONE
========================================================= */

.warehouse-phone-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;

    margin-bottom: 5px;

    color: #58635d;

    font-size: 12.5px;
    font-weight: 600;
}

.warehouse-phone-wrapper:last-child {
    margin-bottom: 0;
}

.warehouse-phone-wrapper i {
    color: #198754;
}


/* =========================================================
   EMAIL
========================================================= */

.warehouse-email-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;

    margin-bottom: 5px;

    min-width: 0;

    color: #58635d;
}

.warehouse-email-wrapper:last-child {
    margin-bottom: 0;
}

.warehouse-email-wrapper i {
    flex: 0 0 auto;

    color: #198754;

    font-size: 13px;
}

.warehouse-email {
    min-width: 0;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   EMPTY / DASH
========================================================= */

.warehouse-dash {
    color: #9aa39e;
}


/* =========================================================
   STATUS
========================================================= */

.warehouse-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    min-width: 78px;

    padding: 6px 9px;

    border-radius: 20px;

    font-size: 11.5px;
    font-weight: 700;

    white-space: nowrap;
}

.warehouse-status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    display: inline-block;
}

.warehouse-status-active {
    background: #eaf8f0;
    color: #198754;
}

.warehouse-status-active .warehouse-status-dot {
    background: #198754;
}

.warehouse-status-inactive {
    background: #f2f3f3;
    color: #7b8580;
}

.warehouse-status-inactive .warehouse-status-dot {
    background: #9aa39e;
}


/* =========================================================
   ACTIONS
========================================================= */

.warehouse-action-cell {
    text-align: center !important;
    white-space: nowrap;
}

.warehouse-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.warehouse-action-btn {
    width: 32px;
    height: 32px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 1px solid transparent;
    border-radius: 8px;

    text-decoration: none !important;

    font-size: 14px;

    transition: all 0.18s ease;
}


/* View */

.warehouse-view-btn {
    background: #eef5ff;
    color: #3977c8;
    border-color: #dceaff;
}

.warehouse-view-btn:hover {
    background: #3977c8;
    color: #ffffff;

    transform: translateY(-1px);

    box-shadow:
        0 4px 9px rgba(57, 119, 200, 0.18);
}


/* Edit */

.warehouse-edit-btn {
    background: #fff8e8;
    color: #c88a18;
    border-color: #f9e9bd;
}

.warehouse-edit-btn:hover {
    background: #c88a18;
    color: #ffffff;

    transform: translateY(-1px);

    box-shadow:
        0 4px 9px rgba(200, 138, 24, 0.18);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.warehouse-empty-cell {
    padding: 45px 20px !important;

    text-align: center;
}

.warehouse-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-direction: column;
}

.warehouse-empty-icon {
    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 15px;

    background: #eef8f2;
    color: #198754;

    font-size: 25px;
}

.warehouse-empty-title {
    color: #39443e;

    font-size: 14px;
    font-weight: 700;
}

.warehouse-empty-text {
    margin-top: 4px;

    color: #89938e;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    .warehouse-header {
        align-items: flex-start;
        flex-direction: column;

        padding: 16px;
    }

    .warehouse-header-actions {
        width: 100%;
    }

    .warehouse-header-btn {
        width: 100%;
    }

    .warehouse-title-row {
        align-items: flex-start;
    }

    .warehouse-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;

        font-size: 18px;
    }

    .warehouse-title-row .card-title {
        font-size: 17px;
    }

    .warehouse-subtitle {
        font-size: 11.5px;
    }

    .warehouse-table-wrapper {
        border-radius: 10px;
    }

    .warehouse-responsive-table {
        min-width: 1250px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('warehouse.view');
            $canEdit = hasPermission('warehouse.edit');
            // $canDelete = hasPermission(permission: 'warehouse.delete');
            @endphp

            <!-- Header -->
            <div class="warehouse-header">

                <div class="warehouse-header-title">
                    <div class="warehouse-title-row">

                        <div class="warehouse-title-icon">
                            <i class="bi bi-building-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Warehouse / Distribution Center
                            </h4>

                            <span class="warehouse-subtitle">
                                Manage your warehouses and distribution centers
                            </span>
                        </div>

                    </div>
                </div>

                @if(hasPermission('warehouse.create'))
                    <div class="warehouse-header-actions">

                        <a href="{{ route('warehouse.create') }}"
                        class="warehouse-header-btn warehouse-add-btn">

                            <span class="warehouse-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Warehouse</span>

                        </a>

                    </div>
                @endif

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
            <div class="warehouse-table-wrapper">

                <div class="warehouse-table-responsive">

                    <table id="warehouse_id"
                        class="table warehouse-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center warehouse-sr-column">
                                    Sr No
                                </th>

                                <th class="warehouse-name-column">
                                    Warehouse Name
                                </th>

                                <th class="warehouse-type-column">
                                    Type
                                </th>

                                <th class="warehouse-address-column">
                                    Address
                                </th>

                                <th class="warehouse-contact-column">
                                    Contact Person
                                </th>

                                <th class="warehouse-mobile-column">
                                    Contact Number
                                </th>

                                <th class="warehouse-email-column">
                                    Email
                                </th>

                                <th class="text-center warehouse-status-column">
                                    Status
                                </th>

                                @if($canView || $canEdit) {{-- || $canDelete --}}

                                    <th class="text-center warehouse-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($warehouses as $warehouse)

                                <tr class="warehouse-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="warehouse-sr-badge">
                                            {{ $loop->iteration }}
                                        </span>

                                    </td>


                                    {{-- Warehouse Name --}}
                                    <td>

                                        <div class="warehouse-name-wrapper">

                                            <div class="warehouse-name-icon">
                                                <i class="bi bi-building"></i>
                                            </div>

                                            <span class="warehouse-name"
                                                title="{{ $warehouse->name }}">

                                                {{ $warehouse->name }}

                                            </span>

                                        </div>

                                    </td>


                                    {{-- Type --}}
                                    <td>

                                        <span class="warehouse-type-badge">
                                            <i class="bi bi-diagram-3"></i>
                                            {{ $warehouse->type }}
                                        </span>

                                    </td>


                                    {{-- Address --}}
                                    <td>

                                        <div class="warehouse-address-wrapper">

                                            <i class="bi bi-geo-alt"></i>

                                            <span class="warehouse-address"
                                                title="{{ $warehouse->address ?? '-' }}">

                                                {{ $warehouse->address ?? '-' }}

                                            </span>

                                        </div>

                                    </td>


                                    {{-- Contact Person --}}
                                    <td>

                                        @forelse($warehouse->users as $user)

                                            <div class="warehouse-contact-wrapper">

                                                <div class="warehouse-contact-icon">
                                                    <i class="bi bi-person"></i>
                                                </div>

                                                <span class="warehouse-contact-name">

                                                    {{ $user->first_name }}
                                                    {{ $user->last_name }}

                                                </span>

                                            </div>

                                        @empty

                                            <span class="warehouse-dash">-</span>

                                        @endforelse

                                    </td>


                                    {{-- Contact Number --}}
                                    <td>

                                        @forelse($warehouse->users as $user)

                                            <div class="warehouse-phone-wrapper">

                                                <i class="bi bi-telephone"></i>

                                                <span>
                                                    {{ $user->mobile }}
                                                </span>

                                            </div>

                                        @empty

                                            <span class="warehouse-dash">-</span>

                                        @endforelse

                                    </td>


                                    {{-- Email --}}
                                    <td>

                                        @forelse($warehouse->users as $user)

                                            <div class="warehouse-email-wrapper">

                                                <i class="bi bi-envelope"></i>

                                                <span class="warehouse-email"
                                                    title="{{ $user->email ?? '-' }}">

                                                    {{ $user->email ?? '-' }}

                                                </span>

                                            </div>

                                        @empty

                                            <span class="warehouse-dash">-</span>

                                        @endforelse

                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        @if($warehouse->status)

                                            <span class="warehouse-status-badge warehouse-status-active">
                                                <span class="warehouse-status-dot"></span>
                                                {{ $warehouse->status }}
                                            </span>

                                        @else

                                            <span class="warehouse-status-badge warehouse-status-inactive">
                                                <span class="warehouse-status-dot"></span>
                                                -
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    @if($canView || $canEdit) {{-- || $canDelete --}}

                                        <td class="text-center warehouse-action-cell">

                                            <div class="warehouse-actions">

                                                @if(hasPermission('warehouse.view'))

                                                    <a href="{{ route('warehouse.show', $warehouse->id) }}"
                                                    class="warehouse-action-btn warehouse-view-btn"
                                                    title="View Warehouse">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('warehouse.edit'))

                                                    <a href="{{ route('warehouse.edit', $warehouse->id) }}"
                                                    class="warehouse-action-btn warehouse-edit-btn"
                                                    title="Edit Warehouse">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif

                                                {{-- Delete intentionally disabled --}}

                                            </div>

                                        </td>

                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ ($canView || $canEdit) ? 9 : 8 }}"
                                        class="warehouse-empty-cell">

                                        <div class="warehouse-empty-state">

                                            <div class="warehouse-empty-icon">
                                                <i class="bi bi-building"></i>
                                            </div>

                                            <div class="warehouse-empty-title">
                                                No Warehouse Found
                                            </div>

                                            <div class="warehouse-empty-text">
                                                There are no warehouses available at the moment.
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
                {{ $warehouses->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("warehouse_id");

        if (!searchInput || !table) return;

        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {

                // Skip "No role found" row
                if (row.cells.length === 1) return;

                row.style.display = row.textContent
                    .toLowerCase()
                    .includes(value) ?
                    "" :
                    "none";
            });
        });

    });
</script>