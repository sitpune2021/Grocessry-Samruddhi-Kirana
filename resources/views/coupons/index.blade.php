@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   COUPON HEADER
========================================================= */

.coupon-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    padding: 20px 24px 18px;

    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #fbfffd 100%
    );
}

.coupon-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.coupon-title-icon {
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

.coupon-header .card-title {
    color: #263238;
    font-size: 20px;
    font-weight: 700;
}

.coupon-subtitle {
    display: block;
    margin-top: 4px;

    color: #8b9690;
    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   ADD COUPON BUTTON
========================================================= */

.coupon-header-actions {
    display: flex;
    align-items: center;
}

.coupon-add-btn {
    min-height: 42px;
    padding: 0 16px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    border-radius: 10px;

    color: #fff !important;

    background: linear-gradient(
        135deg,
        #198754,
        #157347
    );

    text-decoration: none !important;

    font-size: 13px;
    font-weight: 600;

    box-shadow: 0 5px 14px rgba(25, 135, 84, 0.18);

    transition: all 0.2s ease;
}

.coupon-add-btn:hover {
    color: #fff !important;

    transform: translateY(-2px);

    box-shadow: 0 8px 18px rgba(25, 135, 84, 0.25);
}

.coupon-btn-icon {
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

.coupon-table-wrapper {
    margin: 8px 20px 20px;

    border: 1px solid #e7eee9;
    border-radius: 14px;

    overflow: hidden;

    background: #fff;
}

.coupon-table-responsive {
    width: 100%;
    overflow-x: auto;

    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.coupon-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.coupon-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.coupon-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.coupon-responsive-table {
    width: 100%;
    min-width: 1450px;

    table-layout: fixed;

    margin: 0 !important;
}

.coupon-responsive-table thead th {
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

.coupon-responsive-table tbody td {
    height: 66px;
    padding: 10px 13px;

    border-bottom: 1px solid #edf2ef !important;

    color: #4f5b56;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;

    background: #fff;
}

.coupon-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.coupon-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.coupon-code-column {
    width: 190px;
    min-width: 170px;
}

.coupon-discount-type-column {
    width: 170px;
    min-width: 150px;
}

.coupon-discount-value-column {
    width: 150px;
    min-width: 130px;
}

.coupon-date-column {
    width: 165px;
    min-width: 150px;
}

.coupon-amount-column {
    width: 150px;
    min-width: 130px;
}

.coupon-usage-column {
    width: 160px;
    min-width: 140px;
}

.coupon-status-column {
    width: 120px;
    min-width: 120px;
}

.coupon-action-column {
    width: 145px;
    min-width: 145px;
}


/* =========================================================
   ROW HOVER
========================================================= */

.coupon-table-row {
    transition: background-color 0.18s ease;
}

.coupon-table-row:hover td {
    background: #f8fcfa !important;
}

.coupon-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.coupon-sr-badge {
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
   COUPON CODE
========================================================= */

.coupon-code-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.coupon-code-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #eaf7ef;
    color: #198754;
}

.coupon-code {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-weight: 700;
    letter-spacing: 0.3px;
}


/* =========================================================
   DISCOUNT TYPE
========================================================= */

.coupon-info-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;

    min-width: 0;
}

.coupon-info-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef4ff;
    color: #0d6efd;

    font-size: 13px;
}

.coupon-discount-type {
    color: #4f5b56;
    font-weight: 600;
}


/* =========================================================
   DISCOUNT VALUE
========================================================= */

.coupon-value-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 70px;

    padding: 7px 11px;

    border-radius: 9px;

    background: #eaf7ef;
    color: #198754;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   DATES
========================================================= */

.coupon-date-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.coupon-date-icon {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef7f2;
    color: #198754;

    font-size: 12px;
}

.coupon-date-icon.end {
    background: #fff5dc;
    color: #a66a00;
}


/* =========================================================
   MINIMUM AMOUNT
========================================================= */

.coupon-amount-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.coupon-amount-icon {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eaf7ef;
    color: #198754;

    font-size: 12px;
}

.coupon-amount {
    font-weight: 600;
}


/* =========================================================
   MAXIMUM USAGE
========================================================= */

.coupon-usage-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.coupon-usage-icon {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef4ff;
    color: #0d6efd;

    font-size: 12px;
}

.coupon-usage {
    font-weight: 600;
}


/* =========================================================
   STATUS
========================================================= */

.coupon-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    min-width: 82px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 0.3px;
}

.coupon-status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: currentColor;
}

.coupon-status.active {
    color: #198754;
    background: #eaf7ef;
}

.coupon-status.inactive {
    color: #dc3545;
    background: #fdecef;
}


/* =========================================================
   ACTIONS
========================================================= */

.coupon-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.coupon-delete-form {
    display: inline-flex;
    margin: 0 !important;
}

.coupon-action-btn {
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

.coupon-view-btn {
    color: #0d6efd !important;
    background: #eaf2ff !important;
    border-color: #b8d3ff !important;
}

.coupon-view-btn:hover {
    color: #fff !important;
    background: #0d6efd !important;
    transform: translateY(-1px);
}


/* Edit */

.coupon-edit-btn {
    color: #a66a00 !important;
    background: #fff5dc !important;
    border-color: #f2d48d !important;
}

.coupon-edit-btn:hover {
    color: #fff !important;
    background: #f0ad00 !important;
    transform: translateY(-1px);
}


/* Delete */

.coupon-delete-btn {
    color: #dc3545 !important;
    background: #fff1f2 !important;
    border-color: #f5c2c7 !important;
}

.coupon-delete-btn:hover {
    color: #fff !important;
    background: #dc3545 !important;
    transform: translateY(-1px);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.coupon-empty-cell {
    padding: 55px 20px !important;
    text-align: center !important;
}

.coupon-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.coupon-empty-icon {
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

.coupon-empty-title {
    color: #44514b;

    font-size: 15px;
    font-weight: 700;
}

.coupon-empty-text {
    margin-top: 4px;

    color: #98a29d;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .coupon-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .coupon-header-actions {
        width: 100%;
    }

    .coupon-add-btn {
        width: 100%;
    }

    .coupon-table-wrapper {
        margin: 8px 12px 15px;
    }
}


@media (max-width: 575.98px) {

    .coupon-header {
        padding: 18px 15px 15px;
    }

    .coupon-title-row {
        gap: 10px;
    }

    .coupon-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;

        font-size: 18px;
        border-radius: 11px;
    }

    .coupon-header .card-title {
        font-size: 17px;
    }

    .coupon-subtitle {
        font-size: 11px;
    }

    .coupon-table-wrapper {
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

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission( 'coupons.view');
            $canEdit = hasPermission('coupons.edit');
            $canDelete = hasPermission('coupons.delete');
            @endphp

            <!-- ================= HEADER ================= -->
            <div class="coupon-header">

                <div class="coupon-header-title">
                    <div class="coupon-title-row">

                        <div class="coupon-title-icon">
                            <i class="bi bi-ticket-perforated-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Coupon
                            </h4>

                            <span class="coupon-subtitle">
                                Manage your discount coupons and promotional offers
                            </span>
                        </div>

                    </div>
                </div>

                <div class="coupon-header-actions">

                    <a href="{{ route('coupons.create') }}"
                    class="coupon-add-btn">

                        <span class="coupon-btn-icon">
                            <i class="bi bi-plus-lg"></i>
                        </span>

                        <span>Add Coupon</span>

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
            <div class="coupon-table-wrapper">

                <div class="coupon-table-responsive">

                    <table id="batchTable"
                        class="table coupon-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center coupon-sr-column">
                                    Sr No
                                </th>

                                <th class="coupon-code-column">
                                    Code
                                </th>

                                <th class="coupon-discount-type-column">
                                    Discount Type
                                </th>

                                <th class="coupon-discount-value-column">
                                    Discount Value
                                </th>

                                <th class="coupon-date-column">
                                    Start Date
                                </th>

                                <th class="coupon-date-column">
                                    End Date
                                </th>

                                <th class="coupon-amount-column">
                                    Minimum Amt
                                </th>

                                <th class="coupon-usage-column">
                                    Maximum Usage
                                </th>

                                <th class="text-center coupon-status-column">
                                    Status
                                </th>

                                @if($canView || $canEdit || $canDelete)

                                    <th class="text-center coupon-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($offers as $index => $offer)

                                <tr class="coupon-table-row">

                                    <!-- Sr No -->
                                    <td class="text-center">

                                        <span class="coupon-sr-badge">
                                            {{ $offers->firstItem() + $index }}
                                        </span>

                                    </td>


                                    <!-- Code -->
                                    <td>

                                        <div class="coupon-code-wrapper">

                                            <div class="coupon-code-icon">
                                                <i class="bi bi-ticket-perforated"></i>
                                            </div>

                                            <span class="coupon-code"
                                                title="{{ $offer->code }}">
                                                {{ $offer->code }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Discount Type -->
                                    <td>

                                        <div class="coupon-info-wrapper">

                                            <span class="coupon-info-icon type">
                                                <i class="bi bi-percent"></i>
                                            </span>

                                            <span class="coupon-discount-type">
                                                {{ $offer->discount_type }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Discount Value -->
                                    <td>

                                        <span class="coupon-value-badge">
                                            {{ $offer->discount_value }}
                                        </span>

                                    </td>


                                    <!-- Start Date -->
                                    <td>

                                        <div class="coupon-date-wrapper">

                                            <span class="coupon-date-icon">
                                                <i class="bi bi-calendar-event"></i>
                                            </span>

                                            <span>
                                                {{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- End Date -->
                                    <td>

                                        <div class="coupon-date-wrapper">

                                            <span class="coupon-date-icon end">
                                                <i class="bi bi-calendar-check"></i>
                                            </span>

                                            <span>
                                                {{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Minimum Amount -->
                                    <td>

                                        <div class="coupon-amount-wrapper">

                                            <span class="coupon-amount-icon">
                                                <i class="bi bi-currency-rupee"></i>
                                            </span>

                                            <span class="coupon-amount">
                                                {{ $offer->min_amount }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Maximum Usage -->
                                    <td>

                                        <div class="coupon-usage-wrapper">

                                            <span class="coupon-usage-icon">
                                                <i class="bi bi-people"></i>
                                            </span>

                                            <span class="coupon-usage">
                                                {{ $offer->max_usage }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Status -->
                                    <td class="text-center">

                                        @if($offer->status)

                                            <span class="coupon-status active">

                                                <span class="coupon-status-dot"></span>

                                                Active

                                            </span>

                                        @else

                                            <span class="coupon-status inactive">

                                                <span class="coupon-status-dot"></span>

                                                Inactive

                                            </span>

                                        @endif

                                    </td>


                                    <!-- Actions -->
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center">

                                            <div class="coupon-actions">

                                                @if(hasPermission('coupons.view'))

                                                    <a href="{{ route('coupons.show', $offer->id) }}"
                                                    class="coupon-action-btn coupon-view-btn"
                                                    title="View Coupon">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('coupons.edit'))

                                                    <a href="{{ route('coupons.edit', $offer->id) }}"
                                                    class="coupon-action-btn coupon-edit-btn"
                                                    title="Edit Coupon">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('coupons.delete'))

                                                    <form action="{{ route('coupons.destroy', $offer->id) }}"
                                                        method="POST"
                                                        class="coupon-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete offers?')"
                                                                class="coupon-action-btn coupon-delete-btn"
                                                                title="Delete Coupon">

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

                                    <td colspan="{{ ($canView || $canEdit || $canDelete) ? 10 : 9 }}"
                                        class="coupon-empty-cell">

                                        <div class="coupon-empty-state">

                                            <div class="coupon-empty-icon">
                                                <i class="bi bi-ticket-perforated"></i>
                                            </div>

                                            <div class="coupon-empty-title">
                                                No Coupons Found
                                            </div>

                                            <div class="coupon-empty-text">
                                                There are no discount coupons available at the moment.
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
                {{-- <x-pagination :from="$offers->firstItem()" :to="$offers->lastItem()" :total="$offers->total()" /> --}}
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
                if (row.cells.length === 1) return; // skip empty row
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });
    });
</script>
@endpush