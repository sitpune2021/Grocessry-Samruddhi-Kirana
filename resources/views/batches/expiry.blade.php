@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   EXPIRING BATCHES
========================================================= */

.expiring-batch-card {
    border: 0 !important;
    box-shadow: none !important;
    background: #ffffff;
}


/* =========================================================
   HEADER
========================================================= */

.expiring-batch-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 14px;
    border-bottom: 1px solid #edf1ef;
    background: #ffffff;
}

.expiring-batch-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.expiring-batch-title-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    background: #fff3cd;
    color: #d88900;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.expiring-batch-title-row .card-title {
    margin: 0;
    color: #212529;
    font-size: 20px;
    font-weight: 700;
}

.expiring-batch-subtitle {
    display: block;
    margin-top: 3px;
    color: #8a939b;
    font-size: 13px;
}


/* =========================================================
   SEARCH
========================================================= */

.expiring-batch-search {
    padding: 14px 20px 0;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.expiring-batch-table-wrapper {
    padding: 16px;
}

.expiring-batch-table-responsive {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e9efeb;
    border-radius: 12px;
    background: #ffffff;
}

.expiring-batch-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.expiring-batch-table-responsive::-webkit-scrollbar-track {
    background: #f4f6f5;
}

.expiring-batch-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd8d3;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.expiring-batch-responsive-table {
    width: 100%;
    min-width: 950px;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.expiring-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
    text-align: center;
}

.expiring-warehouse-column {
    width: 190px;
    min-width: 170px;
}

.expiring-product-column {
    width: 220px;
    min-width: 190px;
}

.expiring-batch-column {
    width: 160px;
    min-width: 140px;
}

.expiring-qty-column {
    width: 100px;
    min-width: 90px;
}

.expiring-date-column {
    width: 150px;
    min-width: 135px;
}

.expiring-action-column {
    width: 125px;
    min-width: 115px;
    max-width: 125px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.expiring-batch-responsive-table thead th {
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

.expiring-batch-responsive-table tbody td {
    padding: 13px 12px;
    border: 0;
    border-bottom: 1px solid #edf1ef;
    color: #495057;
    font-size: 13px;
    vertical-align: middle;
}

.expiring-batch-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW STATUS
========================================================= */

.expiring-batch-row {
    position: relative;
    transition: all .2s ease;
}


/* Expired */

.expiring-batch-row.batch-expired {
    background: #fff7f7;
}

.expiring-batch-row.batch-expired:hover {
    background: #fff1f1;
}

.expiring-batch-row.batch-expired td:first-child {
    position: relative;
}

.expiring-batch-row.batch-expired:hover td:first-child::before {
    content: "";
    position: absolute;
    left: 0;
    top: 7px;
    bottom: 7px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: #dc3545;
}


/* Expiring Soon */

.expiring-batch-row.batch-expiring-soon {
    background: #fffdf5;
}

.expiring-batch-row.batch-expiring-soon:hover {
    background: #fff8df;
}

.expiring-batch-row.batch-expiring-soon td:first-child {
    position: relative;
}

.expiring-batch-row.batch-expiring-soon:hover td:first-child::before {
    content: "";
    position: absolute;
    left: 0;
    top: 7px;
    bottom: 7px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: #ffc107;
}


/* Upcoming */

.expiring-batch-row.batch-upcoming {
    background: #ffffff;
}

.expiring-batch-row.batch-upcoming:hover {
    background: #f8fcfa;
}

.expiring-batch-row.batch-upcoming td:first-child {
    position: relative;
}

.expiring-batch-row.batch-upcoming:hover td:first-child::before {
    content: "";
    position: absolute;
    left: 0;
    top: 7px;
    bottom: 7px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background: #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.expiring-sr-badge {
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

.expiring-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.expiring-warehouse-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 8px;
    background: #fff7e8;
    color: #d88900;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.expiring-warehouse-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #4b5550;
    font-weight: 600;
}


/* =========================================================
   PRODUCT
========================================================= */

.expiring-product-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.expiring-product-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 9px;
    background: #eef8f2;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.expiring-product-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #30363b;
    font-weight: 600;
}


/* =========================================================
   BATCH NUMBER
========================================================= */

.expiring-batch-number {
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

.expiring-batch-number i {
    font-size: 13px;
}


/* =========================================================
   QUANTITY
========================================================= */

.expiring-quantity-badge {
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

.expiring-quantity-badge i {
    color: #198754;
}


/* =========================================================
   DATE
========================================================= */

.expiring-date-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.expiring-date-icon {
    width: 31px;
    height: 31px;
    min-width: 31px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.mfg-date-icon {
    background: #f1f7ff;
    color: #0d6efd;
}

.upcoming-date-icon {
    background: #eaf7f0;
    color: #198754;
}

.soon-date-icon {
    background: #fff3cd;
    color: #d88900;
}

.expired-date-icon {
    background: #fff1f1;
    color: #dc3545;
}

.expiring-date {
    color: #4b5550;
    font-size: 12px;
    font-weight: 600;
}

.expiry-date-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.expiry-status-text {
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
}

.expiry-status-expired {
    color: #dc3545;
}

.expiry-status-soon {
    color: #d88900;
}

.expiry-status-upcoming {
    color: #198754;
}


/* =========================================================
   SALE BUTTON
========================================================= */

.expiring-action-cell {
    white-space: nowrap;
}

.expiring-sale-btn {
    min-height: 34px;
    padding: 6px 11px;
    border-radius: 8px;
    background: #198754;
    border: 1px solid #198754;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    transition: all .2s ease;
}

.expiring-sale-btn:hover {
    background: #157347;
    border-color: #157347;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(25, 135, 84, .18);
}

.expiring-sale-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.expiring-disabled-action {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #f4f5f6;
    color: #adb5bd;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.expiring-empty-cell {
    padding: 50px 20px !important;
    text-align: center;
}

.expiring-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.expiring-empty-icon {
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

.expiring-empty-title {
    color: #343a40;
    font-size: 16px;
    font-weight: 700;
}

.expiring-empty-text {
    margin-top: 5px;
    color: #929aa1;
    font-size: 13px;
}


/* =========================================================
   PAGINATION
========================================================= */

.expiring-batch-pagination {
    background: #ffffff;
}

.expiring-batch-pagination .pagination {
    margin-bottom: 0;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {

    .expiring-batch-header {
        padding: 16px;
    }

}

@media (max-width: 768px) {

    .expiring-batch-title-row {
        align-items: flex-start;
    }

    .expiring-batch-title-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        font-size: 18px;
    }

    .expiring-batch-title-row .card-title {
        font-size: 18px;
    }

    .expiring-batch-table-wrapper {
        padding: 10px;
    }

    .expiring-batch-search {
        padding: 12px 15px 0;
    }

    .expiring-batch-responsive-table {
        min-width: 950px;
    }

}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card expiring-batch-card">

    <div class="card-datatable text-nowrap">

        <!-- Header -->
        <div class="expiring-batch-header">

            <div class="expiring-batch-header-title">

                <div class="expiring-batch-title-row">

                    <div class="expiring-batch-title-icon">
                        <i class="bi bi-calendar2-x-fill"></i>
                    </div>

                    <div>
                        <h4 class="card-title">
                            Expiring Batches
                        </h4>

                        <span class="expiring-batch-subtitle">
                            Batches expiring within the next 30 days
                        </span>
                    </div>

                </div>

            </div>

        </div>

        <!-- Search -->
        <div class="expiring-batch-search">
            <x-datatable-search />
        </div>

        <!-- Table -->
        <div class="expiring-batch-table-wrapper">

            <div class="expiring-batch-table-responsive">

                <table id="expiry"
                       class="table expiring-batch-responsive-table mb-0">

                    <thead>
                        <tr>

                            <th class="text-center expiring-sr-column">
                                Sr No
                            </th>

                            @if(auth()->user()->role_id == 1)
                                <th class="expiring-warehouse-column">
                                    Warehouse
                                </th>
                            @endif

                            <th class="expiring-product-column">
                                Product
                            </th>

                            <th class="expiring-batch-column">
                                Batch
                            </th>

                            <th class="text-center expiring-qty-column">
                                Qty
                            </th>

                            <th class="expiring-date-column">
                                MFG
                            </th>

                            <th class="expiring-date-column">
                                Expiry
                            </th>

                            <th class="text-center expiring-action-column">
                                Action
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @forelse($batches as $batch)

                            @php

                                $rowClass = '';

                                if ($batch->expiry_date < now()) {

                                    // Expired
                                    $rowClass = 'batch-expired';

                                } elseif ($batch->expiry_date <= now()->addDays(7)) {

                                    // Expiring within 7 days
                                    $rowClass = 'batch-expiring-soon';

                                } else {

                                    // Upcoming
                                    $rowClass = 'batch-upcoming';

                                }

                            @endphp


                            <tr class="expiring-batch-row {{ $rowClass }}">

                                <!-- SR NO -->
                                <td class="text-center">

                                    <span class="expiring-sr-badge">
                                        {{ $loop->iteration }}
                                    </span>

                                </td>


                                <!-- WAREHOUSE -->
                                @if(auth()->user()->role_id == 1)

                                    <td>

                                        <div class="expiring-warehouse-wrapper">

                                            <div class="expiring-warehouse-icon">
                                                <i class="bi bi-building"></i>
                                            </div>

                                            <span class="expiring-warehouse-name"
                                                  title="{{ $batch->warehouse->name ?? '-' }}">
                                                {{ $batch->warehouse->name ?? '-' }}
                                            </span>

                                        </div>

                                    </td>

                                @endif


                                <!-- PRODUCT -->
                                <td>

                                    <div class="expiring-product-wrapper">

                                        <div class="expiring-product-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <span class="expiring-product-name"
                                              title="{{ $batch->product->name ?? '-' }}">
                                            {{ $batch->product->name ?? '-' }}
                                        </span>

                                    </div>

                                </td>


                                <!-- BATCH -->
                                <td>

                                    <span class="expiring-batch-number">
                                        <i class="bi bi-upc-scan"></i>
                                        {{ $batch->batch_no }}
                                    </span>

                                </td>


                                <!-- QUANTITY -->
                                <td class="text-center">

                                    <span class="expiring-quantity-badge">
                                        <i class="bi bi-boxes"></i>
                                        {{ $batch->quantity }}
                                    </span>

                                </td>


                                <!-- MFG -->
                                <td>

                                    <div class="expiring-date-wrapper">

                                        <span class="expiring-date-icon mfg-date-icon">
                                            <i class="bi bi-calendar3"></i>
                                        </span>

                                        <span class="expiring-date">
                                            {{ $batch->mfg_date
                                                ? \Carbon\Carbon::parse($batch->mfg_date)->format('d/m/Y')
                                                : '-' }}
                                        </span>

                                    </div>

                                </td>


                                <!-- EXPIRY -->
                                <td>

                                    <div class="expiring-date-wrapper">

                                        @if($batch->expiry_date < now())

                                            <span class="expiring-date-icon expired-date-icon">
                                                <i class="bi bi-calendar-x"></i>
                                            </span>

                                        @elseif($batch->expiry_date <= now()->addDays(7))

                                            <span class="expiring-date-icon soon-date-icon">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </span>

                                        @else

                                            <span class="expiring-date-icon upcoming-date-icon">
                                                <i class="bi bi-calendar-check"></i>
                                            </span>

                                        @endif


                                        <div class="expiry-date-content">

                                            <span class="expiring-date">
                                                {{ $batch->expiry_date
                                                    ? \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y')
                                                    : '-' }}
                                            </span>


                                            @if($batch->expiry_date < now())

                                                <span class="expiry-status-text expiry-status-expired">
                                                    Expired
                                                </span>

                                            @elseif($batch->expiry_date <= now()->addDays(7))

                                                <span class="expiry-status-text expiry-status-soon">
                                                    Expiring Soon
                                                </span>

                                            @else

                                                <span class="expiry-status-text expiry-status-upcoming">
                                                    Upcoming
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <!-- ACTION -->
                                <td class="text-center expiring-action-cell">

                                    @if(auth()->user()->role_id != 1
                                        && $batch->quantity > 0
                                        && $batch->expiry_date >= now())

                                        <a href="{{ route('sale.create', ['batch_id' => $batch->id]) }}"
                                           title="Sale Product"
                                           class="expiring-sale-btn">

                                            <span class="expiring-sale-icon">
                                                <i class="bi bi-cart3"></i>
                                            </span>

                                            <span>Sale</span>

                                        </a>

                                    @else

                                        <span class="expiring-disabled-action"
                                              title="Sale not available">
                                            <i class="bi bi-x-circle"></i>
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role_id == 1 ? 8 : 7 }}"
                                    class="expiring-empty-cell">

                                    <div class="expiring-empty-state">

                                        <div class="expiring-empty-icon">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>

                                        <div class="expiring-empty-title">
                                            No Expiring Batches
                                        </div>

                                        <div class="expiring-empty-text">
                                            There are no batches expiring within the next 30 days.
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
        <div class="expiring-batch-pagination px-3 py-2">
            {{ $batches->onEachSide(0)->links('pagination::bootstrap-5') }}
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
        const table = document.getElementById("expiry");

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
@endpush