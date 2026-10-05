@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   TRANSFER CHALLANS
========================================================= */

.transfer-challan-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 14px;
    background: #ffffff;
    border-bottom: 1px solid #edf1ef;
}

.transfer-challan-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.transfer-challan-title-icon {
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

.transfer-challan-title-row .card-title {
    margin: 0;
    color: #212529;
    font-size: 20px;
    font-weight: 700;
}

.transfer-challan-subtitle {
    display: block;
    margin-top: 3px;
    color: #8a939b;
    font-size: 13px;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.transfer-challan-table-wrapper {
    padding: 16px;
    background: #ffffff;
}

.transfer-challan-table-responsive {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e9efeb;
    border-radius: 12px;
    background: #ffffff;
}

.transfer-challan-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.transfer-challan-table-responsive::-webkit-scrollbar-track {
    background: #f4f6f5;
}

.transfer-challan-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd8d3;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.transfer-challan-responsive-table {
    width: 100%;
    min-width: 900px;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.transfer-challan-sr-column {
    width: 75px;
    min-width: 75px;
    max-width: 75px;
}

.transfer-challan-number-column {
    width: 165px;
    min-width: 145px;
}

.transfer-challan-warehouse-column {
    width: 220px;
    min-width: 190px;
}

.transfer-challan-date-column {
    width: 150px;
    min-width: 135px;
}

.transfer-challan-status-column {
    width: 140px;
    min-width: 125px;
}

.transfer-challan-action-column {
    width: 155px;
    min-width: 145px;
    max-width: 155px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.transfer-challan-responsive-table thead th {
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

.transfer-challan-responsive-table tbody td {
    padding: 13px 12px;
    border: 0;
    border-bottom: 1px solid #edf1ef;
    color: #495057;
    font-size: 13px;
    vertical-align: middle;
}

.transfer-challan-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.transfer-challan-table-row {
    position: relative;
    transition: background-color .2s ease;
}

.transfer-challan-table-row:hover {
    background: #f8fcfa;
}

.transfer-challan-table-row td:first-child {
    position: relative;
}

.transfer-challan-table-row:hover td:first-child::before {
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
   SR BADGE
========================================================= */

.transfer-challan-sr-badge {
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
   CHALLAN NUMBER
========================================================= */

.transfer-challan-number {
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

.transfer-challan-number i {
    font-size: 14px;
}


/* =========================================================
   WAREHOUSE
========================================================= */

.transfer-challan-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.transfer-challan-from-icon,
.transfer-challan-to-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

.transfer-challan-from-icon {
    background: #fff7e8;
    color: #d88900;
}

.transfer-challan-to-icon {
    background: #eaf7f0;
    color: #198754;
}

.transfer-challan-warehouse-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #30363b;
    font-weight: 600;
}


/* =========================================================
   DATE
========================================================= */

.transfer-challan-date-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.transfer-challan-date-icon {
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

.transfer-challan-date {
    color: #4b5550;
    font-size: 12px;
    font-weight: 600;
}


/* =========================================================
   STATUS
========================================================= */

.transfer-challan-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-width: 95px;
    padding: 6px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.transfer-challan-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

.status-pending {
    background: #fff3cd;
    color: #b77900;
}

.status-pending .transfer-challan-status-dot {
    background: #ffc107;
}

.status-dispatched {
    background: #eef6ff;
    color: #0d6efd;
}

.status-dispatched .transfer-challan-status-dot {
    background: #0d6efd;
}

.status-completed {
    background: #eaf7f0;
    color: #198754;
}

.status-completed .transfer-challan-status-dot {
    background: #198754;
}


/* =========================================================
   ACTIONS
========================================================= */

.transfer-challan-action-cell {
    white-space: nowrap;
}

.transfer-challan-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.transfer-challan-dispatch-form {
    margin: 0;
    padding: 0;
}

.transfer-challan-action-btn {
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


/* Dispatch */

.transfer-challan-dispatch-btn {
    background: #eaf7f0;
    border-color: #d5efdf;
    color: #198754;
}

.transfer-challan-dispatch-btn:hover {
    background: #198754;
    border-color: #198754;
    color: #ffffff;
    transform: translateY(-1px);
}


/* PDF */

.transfer-challan-pdf-btn {
    background: #fff1f1;
    border-color: #ffdada;
    color: #dc3545;
}

.transfer-challan-pdf-btn:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
    transform: translateY(-1px);
}


/* CSV */

.transfer-challan-csv-btn {
    background: #eaf7f0;
    border-color: #d5efdf;
    color: #198754;
}

.transfer-challan-csv-btn:hover {
    background: #198754;
    border-color: #198754;
    color: #ffffff;
    transform: translateY(-1px);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.transfer-challan-empty-cell {
    padding: 50px 20px !important;
    text-align: center;
}

.transfer-challan-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.transfer-challan-empty-icon {
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

.transfer-challan-empty-title {
    color: #343a40;
    font-size: 16px;
    font-weight: 700;
}

.transfer-challan-empty-text {
    margin-top: 5px;
    color: #929aa1;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .transfer-challan-header {
        padding: 15px;
    }

    .transfer-challan-title-row {
        align-items: flex-start;
    }

    .transfer-challan-title-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        font-size: 18px;
    }

    .transfer-challan-title-row .card-title {
        font-size: 18px;
    }

    .transfer-challan-table-wrapper {
        padding: 10px;
    }

    .transfer-challan-responsive-table {
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

            @php
            $canView = hasPermission('transfer_challan.view');
            $canEdit = hasPermission('transfer_challan.edit');
            $canDelete = hasPermission('transfer_challan.delete');
            @endphp

            <!-- Transfer Challan Header -->
            <div class="transfer-challan-header">

                <div class="transfer-challan-header-title">

                    <div class="transfer-challan-title-row">

                        <div class="transfer-challan-title-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Transfer Challans
                            </h4>

                            <span class="transfer-challan-subtitle">
                                Manage warehouse transfer challans and dispatch records
                            </span>
                        </div>

                    </div>

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

            <!-- Warehouse Filter (Super Admin Only) -->
            @if (auth()->user()->role_id == 1)
            <form method="GET" action="{{ route('transfer-challans.index') }}" class="row px-3 mb-3">
                <div class="col-md-4">
                    <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Warehouses</option>
                        @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}"
                            {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- <a href="{{ route('transfer-challans.download.pdf', $transferChallan->id) }}"
            class="btn btn-sm btn-outline-danger">PDF</a>

            <a href="{{ route('transfer-challans.download.csv', $transferChallan->id) }}"
                class="btn btn-sm btn-outline-success">CSV</a> --}}

            @endif

            <!-- Transfer Challan Table -->
            <div class="transfer-challan-table-wrapper">

                <div class="transfer-challan-table-responsive">

                    <table id="batchTable"
                        class="table transfer-challan-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center transfer-challan-sr-column">
                                    Sr No
                                </th>

                                <th class="transfer-challan-number-column">
                                    Challan No
                                </th>

                                <th class="transfer-challan-warehouse-column">
                                    From Warehouse
                                </th>

                                <th class="transfer-challan-warehouse-column">
                                    To Warehouse
                                </th>

                                <th class="transfer-challan-date-column">
                                    Transfer Date
                                </th>

                                <th class="text-center transfer-challan-status-column">
                                    Status
                                </th>

                                @if($canView)
                                    <th class="text-center transfer-challan-action-column">
                                        Actions
                                    </th>
                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($challans as $index => $item)

                                <tr class="transfer-challan-table-row">

                                    {{-- SR NO --}}
                                    <td class="text-center">

                                        <span class="transfer-challan-sr-badge">
                                            {{ $index + 1 }}
                                        </span>

                                    </td>


                                    {{-- CHALLAN NO --}}
                                    <td>

                                        <span class="transfer-challan-number">
                                            <i class="bi bi-receipt"></i>
                                            {{ $item->challan_no }}
                                        </span>

                                    </td>


                                    {{-- FROM WAREHOUSE --}}
                                    <td>

                                        <div class="transfer-challan-warehouse-wrapper">

                                            <div class="transfer-challan-from-icon">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </div>

                                            <span class="transfer-challan-warehouse-name"
                                                title="{{ $item->fromWarehouse->name ?? '-' }}">
                                                {{ $item->fromWarehouse->name ?? '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- TO WAREHOUSE --}}
                                    <td>

                                        <div class="transfer-challan-warehouse-wrapper">

                                            <div class="transfer-challan-to-icon">
                                                <i class="bi bi-box-arrow-in-down"></i>
                                            </div>

                                            <span class="transfer-challan-warehouse-name"
                                                title="{{ $item->toWarehouse->name ?? '-' }}">
                                                {{ $item->toWarehouse->name ?? '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- TRANSFER DATE --}}
                                    <td>

                                        <div class="transfer-challan-date-wrapper">

                                            <span class="transfer-challan-date-icon">
                                                <i class="bi bi-calendar3"></i>
                                            </span>

                                            <span class="transfer-challan-date">
                                                {{ \Carbon\Carbon::parse($item->transfer_date)->format('d-m-Y') }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="text-center">

                                        @if($item->status == 'pending')

                                            <span class="transfer-challan-status-badge status-pending">
                                                <span class="transfer-challan-status-dot"></span>
                                                Pending
                                            </span>

                                        @elseif($item->status == 'dispatched')

                                            <span class="transfer-challan-status-badge status-dispatched">
                                                <span class="transfer-challan-status-dot"></span>
                                                Dispatched
                                            </span>

                                        @else

                                            <span class="transfer-challan-status-badge status-completed">
                                                <span class="transfer-challan-status-dot"></span>
                                                {{ ucfirst($item->status ?? 'N/A') }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}
                                    @if($canView)

                                        <td class="text-center transfer-challan-action-cell">

                                            <div class="transfer-challan-actions">

                                                {{-- DISPATCH --}}
                                                @if(
                                                    $item->status == 'pending' &&
                                                    auth()->user()->warehouse_id == $item->from_warehouse_id
                                                )

                                                    <form method="POST"
                                                        action="{{ route('warehouse.transfer.dispatch.bulk') }}"
                                                        class="transfer-challan-dispatch-form">

                                                        @csrf

                                                        <input type="hidden"
                                                            name="challan_id"
                                                            value="{{ $item->id }}">

                                                        <button type="submit"
                                                                class="transfer-challan-action-btn transfer-challan-dispatch-btn"
                                                                title="Dispatch Challan">

                                                            <i class="bi bi-truck"></i>

                                                        </button>

                                                    </form>

                                                @endif


                                                {{-- PDF --}}
                                                <a href="{{ route('transfer-challans.download.pdf', $item->id) }}"
                                                class="transfer-challan-action-btn transfer-challan-pdf-btn"
                                                target="_blank"
                                                title="Download PDF">

                                                    <i class="bi bi-file-earmark-pdf"></i>

                                                </a>


                                                {{-- CSV --}}
                                                <a href="{{ route('transfer-challans.download.csv', $item->id) }}"
                                                class="transfer-challan-action-btn transfer-challan-csv-btn"
                                                title="Download CSV">

                                                    <i class="bi bi-filetype-csv"></i>

                                                </a>

                                            </div>

                                        </td>

                                    @endif

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ $canView ? 7 : 6 }}"
                                        class="transfer-challan-empty-cell">

                                        <div class="transfer-challan-empty-state">

                                            <div class="transfer-challan-empty-icon">
                                                <i class="bi bi-receipt-cutoff"></i>
                                            </div>

                                            <div class="transfer-challan-empty-title">
                                                No Transfer Challans Found
                                            </div>

                                            <div class="transfer-challan-empty-text">
                                                There are no warehouse transfer challans available at the moment.
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
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("batchTable");

        if (!searchInput || !table) return;

        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {
                if (row.cells.length === 1) return;
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });
    });
</script>
@endpush