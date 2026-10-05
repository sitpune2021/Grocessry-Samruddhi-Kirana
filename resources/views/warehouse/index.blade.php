@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   WAREHOUSE STOCK TRANSFERS
========================================================= */

.transfer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 20px 14px;
    background: #ffffff;
    border-bottom: 1px solid #edf1ef;
}

.transfer-header-title {
    flex: 1;
}

.transfer-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.transfer-title-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    background: #eaf7f0;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.transfer-title-row .card-title {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #212529;
}

.transfer-subtitle {
    display: block;
    margin-top: 3px;
    font-size: 13px;
    color: #8a939b;
}


/* =========================================================
   HEADER BUTTON
========================================================= */

.transfer-header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.transfer-header-btn {
    min-height: 40px;
    padding: 8px 15px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all .2s ease;
}

.transfer-btn-icon {
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.transfer-request-btn {
    background: #198754;
    border: 1px solid #198754;
    color: #ffffff;
}

.transfer-request-btn:hover {
    background: #157347;
    border-color: #157347;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(25, 135, 84, .18);
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.transfer-table-wrapper {
    padding: 16px;
    background: #ffffff;
}

.transfer-table-responsive {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e9efeb;
    border-radius: 12px;
    background: #ffffff;
}

.transfer-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.transfer-table-responsive::-webkit-scrollbar-track {
    background: #f4f6f5;
}

.transfer-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd8d3;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.transfer-responsive-table {
    width: 100%;
    min-width: 1100px;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.transfer-id-column {
    width: 65px;
    min-width: 65px;
    max-width: 65px;
}

.transfer-warehouse-column {
    width: 210px;
    min-width: 190px;
}

.transfer-category-column {
    width: 175px;
    min-width: 150px;
}

.transfer-product-column {
    width: 220px;
    min-width: 190px;
}

.transfer-batch-column {
    width: 155px;
    min-width: 135px;
}

.transfer-qty-column {
    width: 105px;
    min-width: 95px;
}

.transfer-date-column {
    width: 135px;
    min-width: 125px;
}

.transfer-status-column {
    width: 125px;
    min-width: 115px;
}

.transfer-action-column {
    width: 140px;
    min-width: 125px;
    max-width: 140px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.transfer-responsive-table thead th {
    background: #f7faf8;
    color: #4f5b55;
    padding: 13px 12px;
    border: 0;
    border-bottom: 1px solid #e4ebe7;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
    vertical-align: middle;
}

.transfer-responsive-table tbody td {
    padding: 13px 12px;
    border: 0;
    border-bottom: 1px solid #edf1ef;
    color: #495057;
    font-size: 13px;
    vertical-align: middle;
}

.transfer-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.transfer-table-row {
    position: relative;
    transition: background-color .2s ease;
}

.transfer-table-row:hover {
    background: #f8fcfa;
}

.transfer-table-row td:first-child {
    position: relative;
}

.transfer-table-row:hover td:first-child::before {
    content: "";
    position: absolute;
    left: 0;
    top: 8px;
    bottom: 8px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: #198754;
}


/* =========================================================
   ID
========================================================= */

.transfer-id-badge {
    min-width: 31px;
    height: 31px;
    padding: 0 8px;
    border-radius: 8px;
    background: #eef8f2;
    color: #198754;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   WAREHOUSE
========================================================= */

.transfer-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.transfer-warehouse-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 9px;
    background: #fff7e8;
    color: #d88900;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

.transfer-warehouse-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #30363b;
    font-weight: 600;
}


/* =========================================================
   CATEGORY
========================================================= */

.transfer-category-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.transfer-category-icon {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 8px;
    background: #f1f7ff;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}


/* =========================================================
   PRODUCT
========================================================= */

.transfer-product-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.transfer-product-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 9px;
    background: #eef8f2;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

.transfer-product-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #30363b;
    font-weight: 600;
}


/* =========================================================
   BATCH
========================================================= */

.transfer-batch-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 10px;
    border-radius: 7px;
    background: #f1f7f4;
    color: #198754;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}


/* =========================================================
   QUANTITY
========================================================= */

.transfer-quantity-badge {
    min-width: 62px;
    padding: 6px 10px;
    border-radius: 7px;
    background: #f4f6f8;
    color: #495057;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
}

.transfer-quantity-badge i {
    color: #198754;
}


/* =========================================================
   DATE
========================================================= */

.transfer-date-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

.transfer-date-icon {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 8px;
    background: #f1f7ff;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.transfer-date {
    color: #4b5550;
    font-size: 12px;
    font-weight: 600;
}


/* =========================================================
   STATUS
========================================================= */

.transfer-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-width: 96px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.transfer-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.transfer-status-dispatched {
    background: #eaf7f0;
    color: #198754;
}

.transfer-status-dispatched .transfer-status-dot {
    background: #198754;
}

.transfer-status-received {
    background: #eaf7f0;
    color: #198754;
}

.transfer-status-received .transfer-status-dot {
    background: #198754;
}

.transfer-status-pending {
    background: #fff3cd;
    color: #b77900;
}

.transfer-status-pending .transfer-status-dot {
    background: #ffc107;
}


/* =========================================================
   ACTIONS
========================================================= */

.transfer-action-cell {
    white-space: nowrap;
}

.transfer-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.transfer-delete-form {
    margin: 0;
    padding: 0;
}

.transfer-action-btn {
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    transition: all .2s ease;
}

.transfer-view-btn {
    background: #eef6ff;
    border-color: #d9eaff;
    color: #0d6efd;
}

.transfer-view-btn:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    transform: translateY(-1px);
}

.transfer-edit-btn {
    background: #fff7e8;
    border-color: #ffe7b3;
    color: #d88900;
}

.transfer-edit-btn:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #ffffff;
    transform: translateY(-1px);
}

.transfer-delete-btn {
    background: #fff1f1;
    border-color: #ffdada;
    color: #dc3545;
}

.transfer-delete-btn:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
    transform: translateY(-1px);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.transfer-empty-cell {
    padding: 50px 20px !important;
    text-align: center;
}

.transfer-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.transfer-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: #eaf7f0;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 14px;
}

.transfer-empty-title {
    color: #343a40;
    font-size: 16px;
    font-weight: 700;
}

.transfer-empty-text {
    margin-top: 5px;
    color: #929aa1;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {

    .transfer-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .transfer-header-actions {
        width: 100%;
        justify-content: flex-start;
    }

}

@media (max-width: 768px) {

    .transfer-header {
        padding: 15px;
    }

    .transfer-title-row {
        align-items: flex-start;
    }

    .transfer-title-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        font-size: 18px;
    }

    .transfer-title-row .card-title {
        font-size: 18px;
    }

    .transfer-header-actions {
        width: 100%;
    }

    .transfer-header-btn {
        width: 100%;
    }

    .transfer-table-wrapper {
        padding: 10px;
    }

    .transfer-responsive-table {
        min-width: 1100px;
    }

}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm p-2">
            <div class="card-datatable">
                @php
                    $canView = hasPermission('warehouse_transfer_request.view');
                    $canEdit = hasPermission('warehouse_transfer_request.edit');
                    $canDelete = hasPermission('warehouse_transfer_request.delete');
                @endphp

                <!-- Warehouse Stock Transfer Header -->
                <div class="transfer-header">

                    <div class="transfer-header-title">
                        <div class="transfer-title-row">

                            <div class="transfer-title-icon">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>

                            <div>
                                <h4 class="card-title">
                                    Warehouse Stock Transfers
                                </h4>

                                <span class="transfer-subtitle">
                                    Manage warehouse stock transfer requests and movements
                                </span>
                            </div>

                        </div>
                    </div>

                    @if (hasPermission('warehouse_transfer_request.create') && Auth::user()->role_id != 1)

                        <div class="transfer-header-actions">

                            <a href="{{ route('transfer.create') }}"
                            class="transfer-header-btn transfer-request-btn">

                                <span class="transfer-btn-icon">
                                    <i class="bi bi-plus-lg"></i>
                                </span>

                                <span>Request Stock</span>

                            </a>

                        </div>

                    @endif

                </div>

                <!-- Search -->
                <x-datatable-search />

                @if (session('success'))
                    <div id="successAlert"
                        class="alert alert-success alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                        role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="errorAlert"
                        class="alert alert-danger alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                        role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <script>
                        setTimeout(function() {
                            let alertElem = document.getElementById('errorAlert');
                            if (alertElem) {
                                let bsAlert = new bootstrap.Alert(alertElem);
                                bsAlert.close();
                            }
                        }, 5000); // Closes after 5 seconds
                    </script>
                @endif

                <div class="transfer-table-wrapper">

                    <div class="transfer-table-responsive">

                        <table id="transfersTable"
                            class="table transfer-responsive-table mb-0">

                            <thead>
                                <tr>

                                    <th class="text-center transfer-id-column">
                                        ID
                                    </th>

                                    <th class="transfer-warehouse-column">
                                        Requested By Warehouse
                                    </th>

                                    <th class="transfer-category-column">
                                        Category
                                    </th>

                                    <th class="transfer-product-column">
                                        Product
                                    </th>

                                    <th class="transfer-batch-column">
                                        Batch
                                    </th>

                                    <th class="text-center transfer-qty-column">
                                        Quantity
                                    </th>

                                    <th class="transfer-date-column">
                                        Date
                                    </th>

                                    <th class="text-center transfer-status-column">
                                        Status
                                    </th>

                                    @if ($canView || $canEdit || $canDelete)
                                        <th class="text-center transfer-action-column">
                                            Actions
                                        </th>
                                    @endif

                                </tr>
                            </thead>

                            <tbody>

                                @forelse($transfers as $t)

                                    <tr class="transfer-table-row">

                                        {{-- ID --}}
                                        <td class="text-center">

                                            <span class="transfer-id-badge">
                                                {{ $loop->iteration }}
                                            </span>

                                        </td>


                                        {{-- REQUESTED BY WAREHOUSE --}}
                                        <td>

                                            <div class="transfer-warehouse-wrapper">

                                                <div class="transfer-warehouse-icon">
                                                    <i class="bi bi-building"></i>
                                                </div>

                                                <span class="transfer-warehouse-name"
                                                    title="{{ $t->requestedByWarehouse->name ?? '-' }}">
                                                    {{ $t->requestedByWarehouse->name ?? '-' }}
                                                </span>

                                            </div>

                                        </td>


                                        {{-- CATEGORY --}}
                                        <td>

                                            <div class="transfer-category-wrapper">

                                                <div class="transfer-category-icon">
                                                    <i class="bi bi-grid"></i>
                                                </div>

                                                <span>
                                                    {{ $t->category->name ?? '-' }}
                                                </span>

                                            </div>

                                        </td>


                                        {{-- PRODUCT --}}
                                        <td>

                                            <div class="transfer-product-wrapper">

                                                <div class="transfer-product-icon">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>

                                                <span class="transfer-product-name"
                                                    title="{{ $t->product->name ?? '-' }}">
                                                    {{ $t->product->name ?? '-' }}
                                                </span>

                                            </div>

                                        </td>


                                        {{-- BATCH --}}
                                        <td>

                                            <span class="transfer-batch-badge">
                                                <i class="bi bi-upc-scan"></i>
                                                {{ $t->batch->batch_no ?? '-' }}
                                            </span>

                                        </td>


                                        {{-- QUANTITY --}}
                                        <td class="text-center">

                                            <span class="transfer-quantity-badge">
                                                <i class="bi bi-boxes"></i>
                                                {{ $t->quantity }}
                                            </span>

                                        </td>


                                        {{-- DATE --}}
                                        <td>

                                            <div class="transfer-date-wrapper">

                                                <span class="transfer-date-icon">
                                                    <i class="bi bi-calendar3"></i>
                                                </span>

                                                <span class="transfer-date">
                                                    {{ optional($t->created_at)->format('d-m-Y') }}
                                                </span>

                                            </div>

                                        </td>


                                        {{-- STATUS --}}
                                        <td class="text-center">

                                            @if ($t->status == 1)

                                                <span class="transfer-status-badge transfer-status-dispatched">
                                                    <span class="transfer-status-dot"></span>
                                                    Dispatched
                                                </span>

                                            @elseif($t->status == 2)

                                                <span class="transfer-status-badge transfer-status-received">
                                                    <span class="transfer-status-dot"></span>
                                                    Received
                                                </span>

                                            @else

                                                <span class="transfer-status-badge transfer-status-pending">
                                                    <span class="transfer-status-dot"></span>
                                                    Pending
                                                </span>

                                            @endif

                                        </td>


                                        {{-- ACTIONS --}}
                                        @if ($canView || $canEdit || $canDelete)

                                            <td class="text-center transfer-action-cell">

                                                <div class="transfer-actions">

                                                    {{-- View --}}
                                                    @if ($canView)

                                                        <a href="{{ route('transfer.show', $t->id) }}"
                                                        class="transfer-action-btn transfer-view-btn"
                                                        title="View Transfer">

                                                            <i class="bi bi-eye"></i>

                                                        </a>

                                                    @endif


                                                    {{-- Edit --}}
                                                    @if (auth()->user()->warehouse_id == $t->requested_by_warehouse_id && $t->status == 0)

                                                        @if ($canEdit)

                                                            <a href="{{ route('transfer.edit', $t->id) }}"
                                                            class="transfer-action-btn transfer-edit-btn"
                                                            title="Edit Transfer">

                                                                <i class="bi bi-pencil"></i>

                                                            </a>

                                                        @endif

                                                    @endif


                                                    {{-- Delete --}}
                                                    @if ($canDelete)

                                                        <form action="{{ route('transfer.destroy', $t->id) }}"
                                                            method="POST"
                                                            class="transfer-delete-form">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    onclick="return confirm('Delete batch?')"
                                                                    class="transfer-action-btn transfer-delete-btn"
                                                                    title="Delete Transfer">

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

                                        <td colspan="{{ ($canView || $canEdit || $canDelete) ? 9 : 8 }}"
                                            class="transfer-empty-cell">

                                            <div class="transfer-empty-state">

                                                <div class="transfer-empty-icon">
                                                    <i class="bi bi-arrow-left-right"></i>
                                                </div>

                                                <div class="transfer-empty-title">
                                                    No Transfers Found
                                                </div>

                                                <div class="transfer-empty-text">
                                                    There are no warehouse stock transfers available at the moment.
                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection

<script>
    $(document).ready(function() {
        $('#transfersTable').DataTable({
            scrollX: true, // ✅ REQUIRED for wide tables
            autoWidth: false, // ✅ REQUIRED
            pageLength: 10,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                    targets: -1,
                    orderable: false
                } // Action column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search transfers..."
            }
        });
    });
</script>

<!-- table search box script -->
@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("transfersTable");

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
