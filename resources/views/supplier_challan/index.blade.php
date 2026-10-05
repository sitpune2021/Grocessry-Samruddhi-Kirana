@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   SUPPLIER CHALLAN HEADER
========================================================= */

.supplier-challan-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 20px;
    background: #ffffff;
    border-bottom: 1px solid #edf1ee;
}

.supplier-challan-header-title {
    min-width: 0;
}

.supplier-challan-title-row {
    display: flex;
    align-items: center;
    gap: 13px;
}

.supplier-challan-title-icon {
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

.supplier-challan-title-row .card-title {
    margin: 0;
    font-size: 19px;
    font-weight: 700;
    color: #202a24;
}

.supplier-challan-subtitle {
    display: block;
    margin-top: 3px;

    font-size: 12.5px;
    color: #7a8580;
}


/* =========================================================
   HEADER BUTTON
========================================================= */

.supplier-challan-header-actions {
    display: flex;
    align-items: center;
}

.supplier-challan-header-btn {
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

.supplier-challan-add-btn {
    color: #ffffff !important;
    background: #198754;

    box-shadow:
        0 4px 12px rgba(25, 135, 84, 0.16);
}

.supplier-challan-add-btn:hover {
    color: #ffffff !important;
    background: #157347;

    transform: translateY(-1px);

    box-shadow:
        0 7px 16px rgba(25, 135, 84, 0.22);
}

.supplier-challan-btn-icon {
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

.supplier-challan-table-wrapper {
    width: 100%;
    margin-top: 14px;

    background: #ffffff;

    border: 1px solid #e9efeb;
    border-radius: 12px;

    overflow: hidden;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.035);
}

.supplier-challan-table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.supplier-challan-responsive-table {
    width: 100%;
    min-width: 900px;

    margin: 0 !important;

    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.supplier-challan-sr-column {
    width: 75px;
    min-width: 75px;
    max-width: 75px;
}

.supplier-challan-bill-column {
    width: 145px;
    min-width: 130px;
}

.supplier-challan-number-column {
    width: 155px;
    min-width: 140px;
}

.supplier-challan-supplier-column {
    width: 220px;
    min-width: 190px;
}

.supplier-challan-warehouse-column {
    width: 220px;
    min-width: 190px;
}

.supplier-challan-date-column {
    width: 140px;
    min-width: 125px;
}

.supplier-challan-action-column {
    width: 155px;
    min-width: 155px;
    max-width: 155px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.supplier-challan-responsive-table thead th {
    padding: 13px 14px;

    background: #f8faf9;

    border-bottom: 1px solid #e3e9e5;
    border-top: 0;

    color: #66716b;

    font-size: 11.5px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.35px;

    white-space: nowrap;
    vertical-align: middle;
}


/* =========================================================
   TABLE BODY
========================================================= */

.supplier-challan-responsive-table tbody td {
    padding: 13px 14px;

    border-bottom: 1px solid #edf1ee;
    border-top: 0;

    color: #414a45;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;
}

.supplier-challan-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.supplier-challan-table-row {
    position: relative;
    transition: background 0.18s ease;
}

.supplier-challan-table-row:hover {
    background: #fbfefc;
}

.supplier-challan-table-row td:first-child {
    position: relative;
}

.supplier-challan-table-row td:first-child::before {
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

.supplier-challan-table-row:hover td:first-child::before {
    opacity: 1;
}


/* =========================================================
   SR BADGE
========================================================= */

.supplier-challan-sr-badge {
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
   BILL NUMBER
========================================================= */

.supplier-challan-bill-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.supplier-challan-bill-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef5ff;
    color: #3977c8;

    font-size: 14px;
}

.supplier-challan-bill {
    min-width: 0;

    color: #26332c;
    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   CHALLAN NUMBER
========================================================= */

.supplier-challan-number {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 6px 9px;

    border-radius: 7px;

    background: #f3f7f5;
    color: #198754;

    font-size: 12px;
    font-weight: 700;

    white-space: nowrap;
}

.supplier-challan-number i {
    font-size: 13px;
}


/* =========================================================
   SUPPLIER
========================================================= */

.supplier-challan-supplier-wrapper,
.supplier-challan-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.supplier-challan-supplier-icon,
.supplier-challan-warehouse-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #f1f8f4;
    color: #198754;

    font-size: 14px;
}

.supplier-challan-supplier,
.supplier-challan-warehouse {
    min-width: 0;

    color: #3e4842;
    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   DATE
========================================================= */

.supplier-challan-date {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    color: #5f6964;

    font-size: 12.5px;
    font-weight: 600;

    white-space: nowrap;
}

.supplier-challan-date i {
    color: #198754;
    font-size: 14px;
}


/* =========================================================
   ACTIONS
========================================================= */

.supplier-challan-action-cell {
    text-align: center !important;
    white-space: nowrap;
}

.supplier-challan-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.supplier-challan-delete-form {
    display: inline-flex;
    margin: 0 !important;
    padding: 0 !important;
}

.supplier-challan-action-btn {
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

    cursor: pointer;

    transition:
        transform 0.18s ease,
        background 0.18s ease,
        box-shadow 0.18s ease;
}


/* View */

.supplier-challan-view-btn {
    background: #eef5ff;
    color: #3977c8;
    border-color: #dceaff;
}

.supplier-challan-view-btn:hover {
    background: #3977c8;
    color: #ffffff;

    transform: translateY(-1px);
    box-shadow: 0 4px 9px rgba(57, 119, 200, 0.18);
}


/* Edit */

.supplier-challan-edit-btn {
    background: #fff8e8;
    color: #c88a18;
    border-color: #f9e9bd;
}

.supplier-challan-edit-btn:hover {
    background: #c88a18;
    color: #ffffff;

    transform: translateY(-1px);
    box-shadow: 0 4px 9px rgba(200, 138, 24, 0.18);
}


/* Delete */

.supplier-challan-delete-btn {
    background: #fff0f0;
    color: #dc3545;
    border-color: #ffd9dc;
}

.supplier-challan-delete-btn:hover {
    background: #dc3545;
    color: #ffffff;

    transform: translateY(-1px);
    box-shadow: 0 4px 9px rgba(220, 53, 69, 0.18);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.supplier-challan-empty-cell {
    padding: 45px 20px !important;
    text-align: center;
}

.supplier-challan-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.supplier-challan-empty-icon {
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

.supplier-challan-empty-title {
    color: #39443e;

    font-size: 14px;
    font-weight: 700;
}

.supplier-challan-empty-text {
    margin-top: 4px;

    color: #89938e;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    .supplier-challan-header {
        align-items: flex-start;
        flex-direction: column;
        padding: 16px;
    }

    .supplier-challan-header-actions {
        width: 100%;
    }

    .supplier-challan-header-btn {
        width: 100%;
    }

    .supplier-challan-title-row {
        align-items: flex-start;
    }

    .supplier-challan-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        font-size: 18px;
    }

    .supplier-challan-title-row .card-title {
        font-size: 17px;
    }

    .supplier-challan-subtitle {
        font-size: 11.5px;
    }

    .supplier-challan-table-wrapper {
        margin-top: 12px;
        border-radius: 10px;
    }

    .supplier-challan-responsive-table {
        min-width: 900px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">

            {{-- Permissions --}}
            @php
            $canView = hasPermission('supplier_challan.view');
            $canCreate = hasPermission('supplier_challan.create');
            $canEdit = hasPermission('supplier_challan.edit');
            $canDelete = hasPermission('supplier_challan.delete');
            @endphp

            <!-- Header -->
            <div class="supplier-challan-header">
                <div class="supplier-challan-header-title">
                    <div class="supplier-challan-title-row">

                        <div class="supplier-challan-title-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Supplier Challans
                            </h4>

                            <span class="supplier-challan-subtitle">
                                Manage your supplier challans and purchase details
                            </span>
                        </div>

                    </div>
                </div>

                @if($canCreate)
                    <div class="supplier-challan-header-actions">

                        <a href="{{ route('supplier_challan.create') }}"
                        class="supplier-challan-header-btn supplier-challan-add-btn">

                            <span class="supplier-challan-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Create Challan</span>

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
            <div class="supplier-challan-table-wrapper">

                <div class="supplier-challan-table-responsive">

                    <table id="challanTable"
                        class="table supplier-challan-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center supplier-challan-sr-column">
                                    Sr No
                                </th>

                                <th class="supplier-challan-bill-column">
                                    Bill No
                                </th>

                                <th class="supplier-challan-number-column">
                                    Challan No
                                </th>

                                <th class="supplier-challan-supplier-column">
                                    Supplier
                                </th>

                                <th class="supplier-challan-warehouse-column">
                                    Warehouse
                                </th>

                                <th class="text-center supplier-challan-date-column">
                                    Date
                                </th>

                                @if($canView || $canEdit || $canDelete)
                                    <th class="text-center supplier-challan-action-column">
                                        Actions
                                    </th>
                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($challans as $index => $challan)

                                <tr class="supplier-challan-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="supplier-challan-sr-badge">
                                            {{ $challans->firstItem() + $index }}
                                        </span>

                                    </td>

                                    {{-- Bill No --}}
                                    <td>

                                        <div class="supplier-challan-bill-wrapper">

                                            <div class="supplier-challan-bill-icon">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>

                                            <span class="supplier-challan-bill"
                                                title="{{ $challan->bill_no }}">

                                                {{ $challan->bill_no }}

                                            </span>

                                        </div>

                                    </td>

                                    {{-- Challan No --}}
                                    <td>

                                        <span class="supplier-challan-number">
                                            <i class="bi bi-receipt"></i>
                                            {{ $challan->challan_no }}
                                        </span>

                                    </td>

                                    {{-- Supplier --}}
                                    <td>

                                        <div class="supplier-challan-supplier-wrapper">

                                            <div class="supplier-challan-supplier-icon">
                                                <i class="bi bi-person-badge"></i>
                                            </div>

                                            <span class="supplier-challan-supplier"
                                                title="{{ $challan->supplier->supplier_name ?? '-' }}">

                                                {{ $challan->supplier->supplier_name ?? '-' }}

                                            </span>

                                        </div>

                                    </td>

                                    {{-- Warehouse --}}
                                    <td>

                                        <div class="supplier-challan-warehouse-wrapper">

                                            <div class="supplier-challan-warehouse-icon">
                                                <i class="bi bi-building"></i>
                                            </div>

                                            <span class="supplier-challan-warehouse"
                                                title="{{ $challan->warehouse->name ?? '-' }}">

                                                {{ $challan->warehouse->name ?? '-' }}

                                            </span>

                                        </div>

                                    </td>

                                    {{-- Date --}}
                                    <td class="text-center">

                                        <span class="supplier-challan-date">

                                            <i class="bi bi-calendar3"></i>

                                            {{ \Carbon\Carbon::parse($challan->challan_date)->format('d-m-Y') }}

                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center supplier-challan-action-cell">

                                            <div class="supplier-challan-actions">

                                                @if($canView)

                                                    <a href="{{ route('supplier_challan.show', $challan->id) }}"
                                                    class="supplier-challan-action-btn supplier-challan-view-btn"
                                                    title="View Challan">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif


                                                @if($canEdit)

                                                    <a href="{{ route('supplier_challan.edit', $challan->id) }}"
                                                    class="supplier-challan-action-btn supplier-challan-edit-btn"
                                                    title="Edit Challan">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif


                                                @if($canDelete)

                                                    <form action="{{ route('supplier_challan.destroy', $challan->id) }}"
                                                        method="POST"
                                                        class="supplier-challan-delete-form"
                                                        onsubmit="return confirm('Are you sure you want to delete this challan?')">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                class="supplier-challan-action-btn supplier-challan-delete-btn"
                                                                title="Delete Challan">

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
                                        class="supplier-challan-empty-cell">

                                        <div class="supplier-challan-empty-state">

                                            <div class="supplier-challan-empty-icon">
                                                <i class="bi bi-receipt"></i>
                                            </div>

                                            <div class="supplier-challan-empty-title">
                                                No Supplier Challans Found
                                            </div>

                                            <div class="supplier-challan-empty-text">
                                                There are no supplier challans available at the moment.
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
                {{ $challans->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("challanTable");

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
