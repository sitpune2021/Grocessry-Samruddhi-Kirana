@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   WAREHOUSE STOCK RETURN
========================================================= */

.stock-return-card {
    border: 0 !important;
    border-radius: 18px !important;
    overflow: hidden;
}

/* ================= HEADER ================= */

.stock-return-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 24px 18px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfffd 100%);
}

.stock-return-header-title {
    flex: 1;
}

.stock-return-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.stock-return-title-icon {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
        135deg,
        rgba(25, 135, 84, 0.16),
        rgba(25, 135, 84, 0.06)
    );
    color: #198754;
    font-size: 22px;
    flex-shrink: 0;
}

.stock-return-header .card-title {
    margin: 0 !important;
    font-size: 20px;
    font-weight: 700;
    color: #263238;
}

.stock-return-subtitle {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    font-weight: 500;
    color: #8a959e;
}

/* ================= HEADER BUTTON ================= */

.stock-return-header-actions {
    display: flex;
    align-items: center;
}

.stock-return-header-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 16px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}

.stock-return-add-btn {
    color: #fff !important;
    background: linear-gradient(135deg, #198754, #157347);
    box-shadow: 0 5px 14px rgba(25, 135, 84, 0.18);
}

.stock-return-add-btn:hover {
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(25, 135, 84, 0.24);
}

.stock-return-btn-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    background: rgba(255, 255, 255, 0.16);
}

/* ================= SEARCH ================= */

.stock-return-search {
    padding: 0 24px 8px;
}

/* ================= TABLE ================= */

.stock-return-table-wrapper {
    margin: 8px 20px 0;
    border: 1px solid #e8efeb;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
}

.stock-return-table-responsive {
    width: 100%;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.stock-return-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.stock-return-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.stock-return-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}

.stock-return-responsive-table {
    width: 100%;
    min-width: 1500px;
    table-layout: fixed;
    margin: 0 !important;
}

.stock-return-responsive-table thead th {
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

.stock-return-responsive-table tbody td {
    height: 64px;
    padding: 10px 13px;
    border-bottom: 1px solid #edf2ef !important;
    color: #4f5b56;
    font-size: 13px;
    font-weight: 500;
    vertical-align: middle;
    background: #fff;
}

.stock-return-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}

/* ================= COLUMN WIDTHS ================= */

.stock-return-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.stock-return-number-column {
    width: 160px;
    min-width: 160px;
}

.stock-return-warehouse-column {
    width: 210px;
    min-width: 190px;
}

.stock-return-reason-column {
    width: 180px;
    min-width: 170px;
}

.stock-return-items-column {
    width: 110px;
    min-width: 110px;
}

.stock-return-status-column {
    width: 130px;
    min-width: 130px;
}

.stock-return-created-column {
    width: 180px;
    min-width: 170px;
}

.stock-return-date-column {
    width: 140px;
    min-width: 140px;
}

.stock-return-pdf-column {
    width: 75px;
    min-width: 75px;
}

.stock-return-action-column {
    width: 125px;
    min-width: 125px;
}

/* ================= ROW HOVER ================= */

.stock-return-table-row {
    position: relative;
    transition: background-color 0.18s ease;
}

.stock-return-table-row:hover td {
    background: #f8fcfa !important;
}

.stock-return-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}

/* ================= SR BADGE ================= */

.stock-return-sr-badge {
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

/* ================= RETURN NUMBER ================= */

.stock-return-number-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.stock-return-number-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eef7f2;
    color: #198754;
}

.stock-return-number {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #263238;
    font-weight: 700;
}

/* ================= WAREHOUSE ================= */

.stock-return-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.stock-return-warehouse-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
}

.stock-return-warehouse-icon.from {
    background: #fff7e8;
    color: #d88a00;
}

.stock-return-warehouse-icon.to {
    background: #eef8f2;
    color: #198754;
}

.stock-return-warehouse-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #4c5953;
    font-weight: 600;
}

/* ================= REASON ================= */

.stock-return-reason-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.stock-return-reason-icon {
    color: #7b8982;
    flex-shrink: 0;
}

.stock-return-reason {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ================= ITEMS ================= */

.stock-return-items-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    min-height: 30px;
    padding: 4px 9px;
    border-radius: 8px;
    background: #edf7fb;
    color: #087ea4;
    font-size: 11px;
    font-weight: 700;
}

/* ================= STATUS ================= */

.stock-return-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 92px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.4px;
}

.stock-return-status-badge .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* Draft */
.stock-return-status-badge.status-draft {
    color: #6c757d;
    background: #f0f1f2;
}

/* Approved */
.stock-return-status-badge.status-approved {
    color: #198754;
    background: #eaf7ef;
}

/* Dispatched */
.stock-return-status-badge.status-dispatched {
    color: #b77900;
    background: #fff5dc;
}

/* Received */
.stock-return-status-badge.status-received {
    color: #0d6efd;
    background: #eaf2ff;
}

/* Rejected */
.stock-return-status-badge.status-rejected {
    color: #dc3545;
    background: #fdecef;
}

/* ================= CREATOR ================= */

.stock-return-creator-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
}

.stock-return-creator-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f0f7f3;
    color: #198754;
}

.stock-return-creator-info {
    min-width: 0;
}

.stock-return-creator-name {
    display: block;
    color: #3f4c46;
    font-weight: 600;
    line-height: 1.3;
}

.stock-return-creator-role {
    display: block;
    margin-top: 2px;
    color: #8b9690;
    font-size: 10px;
    white-space: nowrap;
}

.stock-return-creator-role i {
    margin-right: 2px;
}

/* ================= DATE ================= */

.stock-return-date-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stock-return-date-icon {
    color: #198754;
}

.stock-return-date {
    white-space: nowrap;
    font-weight: 600;
    color: #59665f;
}

/* ================= ACTIONS ================= */

.stock-return-action-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
}

.stock-return-action-form {
    display: inline-flex;
    margin: 0 !important;
}

.stock-return-action-btn {
    width: 32px;
    height: 32px;
    padding: 0 !important;
    border-radius: 8px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent !important;
    font-size: 14px;
    transition: all 0.18s ease;
}

/* PDF */
.stock-return-pdf-btn {
    color: #dc3545 !important;
    background: #fff1f2 !important;
    border-color: #f5c2c7 !important;
}

.stock-return-pdf-btn:hover {
    color: #fff !important;
    background: #dc3545 !important;
    transform: translateY(-1px);
}

/* Approve */
.stock-return-approve-btn {
    color: #198754 !important;
    background: #eaf7ef !important;
    border-color: #bfe4cd !important;
}

.stock-return-approve-btn:hover {
    color: #fff !important;
    background: #198754 !important;
}

/* Dispatch */
.stock-return-dispatch-btn {
    color: #a66a00 !important;
    background: #fff5dc !important;
    border-color: #f2d48d !important;
}

.stock-return-dispatch-btn:hover {
    color: #fff !important;
    background: #f0ad00 !important;
}

/* Receive */
.stock-return-receive-btn {
    color: #0d6efd !important;
    background: #eaf2ff !important;
    border-color: #b8d3ff !important;
}

.stock-return-receive-btn:hover {
    color: #fff !important;
    background: #0d6efd !important;
}

/* ================= EMPTY STATE ================= */

.stock-return-empty-cell {
    padding: 55px 20px !important;
    text-align: center !important;
}

.stock-return-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.stock-return-empty-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    background: #f0f7f3;
    color: #198754;
    font-size: 25px;
    margin-bottom: 12px;
}

.stock-return-empty-title {
    color: #44514b;
    font-size: 15px;
    font-weight: 700;
}

.stock-return-empty-text {
    margin-top: 4px;
    color: #98a29d;
    font-size: 12px;
}

/* ================= MOBILE ================= */

@media (max-width: 991.98px) {

    .stock-return-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .stock-return-header-actions {
        width: 100%;
    }

    .stock-return-header-btn {
        width: 100%;
        justify-content: center;
    }

    .stock-return-table-wrapper {
        margin: 8px 12px 0;
    }

    .stock-return-search {
        padding: 0 15px 8px;
    }
}

@media (max-width: 575.98px) {

    .stock-return-header {
        padding: 18px 15px 15px;
    }

    .stock-return-title-row {
        gap: 10px;
    }

    .stock-return-title-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
        border-radius: 11px;
    }

    .stock-return-header .card-title {
        font-size: 17px;
    }

    .stock-return-subtitle {
        font-size: 11px;
    }

    .stock-return-table-wrapper {
        margin: 5px 8px 0;
        border-radius: 11px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card stock-return-card">
    <div class="card-datatable text-nowrap">

        <!-- ================= HEADER ================= -->
        <div class="stock-return-header">
            <div class="stock-return-header-title">
                <div class="stock-return-title-row">

                    <div class="stock-return-title-icon">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>

                    <div>
                        <h4 class="card-title mb-0">
                            Raise Warehouse Stock Return
                        </h4>

                        <span class="stock-return-subtitle">
                            Manage warehouse stock return requests and movements
                        </span>
                    </div>

                </div>
            </div>

            @php
                $warehouseType = auth()->user()?->warehouse?->type;
            @endphp

            @if ($warehouseType === 'taluka' || $warehouseType === 'district' || $warehouseType === 'distribution_center')
                <div class="stock-return-header-actions">

                    <a href="{{ route('stock-returns.create') }}"
                       class="stock-return-header-btn stock-return-add-btn">

                        <span class="stock-return-btn-icon">
                            <i class="bi bi-plus-lg"></i>
                        </span>

                        <span>Raise Return</span>
                    </a>

                </div>
            @endif
        </div>

        <!-- ================= SEARCH ================= -->
        <div class="stock-return-search">
            <x-datatable-search />
        </div>

        <!-- ================= TABLE ================= -->
        <div class="stock-return-table-wrapper">
            <div class="stock-return-table-responsive">

                <table class="table stock-return-responsive-table mb-0">

                    <thead>
                        <tr>
                            <th class="text-center stock-return-sr-column">
                                Sr No
                            </th>

                            <th class="stock-return-number-column">
                                Return No
                            </th>

                            <th class="stock-return-warehouse-column">
                                From
                            </th>

                            <th class="stock-return-warehouse-column">
                                To
                            </th>

                            <th class="stock-return-reason-column">
                                Reason
                            </th>

                            <th class="text-center stock-return-items-column">
                                Items
                            </th>

                            <th class="text-center stock-return-status-column">
                                Status
                            </th>

                            <th class="stock-return-created-column">
                                Created By
                            </th>

                            <th class="stock-return-date-column">
                                Created At
                            </th>

                            <th class="text-center stock-return-pdf-column">
                                PDF
                            </th>

                            <th class="text-center stock-return-action-column">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($returns as $key => $return)

                            <tr class="stock-return-table-row">

                                <!-- SR NO -->
                                <td class="text-center">

                                    <span class="stock-return-sr-badge">
                                        {{ $returns->firstItem() + $key }}
                                    </span>

                                </td>

                                <!-- RETURN NO -->
                                <td>

                                    <div class="stock-return-number-wrapper">

                                        <div class="stock-return-number-icon">
                                            <i class="bi bi-receipt"></i>
                                        </div>

                                        <span class="stock-return-number"
                                              title="{{ $return->return_number ?? 'WR-' . str_pad($return->id, 5, '0', STR_PAD_LEFT) }}">

                                            {{ $return->return_number ?? 'WR-' . str_pad($return->id, 5, '0', STR_PAD_LEFT) }}

                                        </span>

                                    </div>

                                </td>

                                <!-- FROM -->
                                <td>

                                    <div class="stock-return-warehouse-wrapper">

                                        <span class="stock-return-warehouse-icon from">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </span>

                                        <span class="stock-return-warehouse-name"
                                              title="{{ $return->fromWarehouse->name ?? '-' }}">
                                            {{ $return->fromWarehouse->name ?? '-' }}
                                        </span>

                                    </div>

                                </td>

                                <!-- TO -->
                                <td>

                                    <div class="stock-return-warehouse-wrapper">

                                        <span class="stock-return-warehouse-icon to">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </span>

                                        <span class="stock-return-warehouse-name"
                                              title="{{ $return->toWarehouse->name ?? '-' }}">
                                            {{ $return->toWarehouse->name ?? '-' }}
                                        </span>

                                    </div>

                                </td>

                                <!-- REASON -->
                                <td>

                                    <div class="stock-return-reason-wrapper">

                                        <span class="stock-return-reason-icon">
                                            <i class="bi bi-chat-left-text"></i>
                                        </span>

                                        <span class="stock-return-reason"
                                              title="{{ ucfirst(str_replace('_',' ', $return->return_reason)) }}">

                                            {{ ucfirst(str_replace('_',' ', $return->return_reason)) }}

                                        </span>

                                    </div>

                                </td>

                                <!-- ITEMS -->
                                <td class="text-center">

                                    <span class="stock-return-items-badge">
                                        <i class="bi bi-box-seam"></i>
                                        {{ $return->WarehouseStockReturnItem->sum('return_qty') }} Items
                                    </span>

                                </td>

                                <!-- STATUS -->
                                <td class="text-center">

                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'approved' => 'success',
                                            'dispatched' => 'warning',
                                            'received' => 'primary',
                                            'rejected' => 'danger',
                                        ];
                                    @endphp

                                    <span class="stock-return-status-badge status-{{ $return->status }}">
                                        <span class="status-dot"></span>
                                        {{ strtoupper($return->status) }}
                                    </span>

                                </td>

                                <!-- CREATED BY -->
                                <td>

                                    <div class="stock-return-creator-wrapper">

                                        <div class="stock-return-creator-icon">
                                            <i class="bi bi-person"></i>
                                        </div>

                                        <div class="stock-return-creator-info">

                                            <span class="stock-return-creator-name">
                                                {{ $return->creator->first_name ?? '-' }}
                                            </span>

                                            <small class="stock-return-creator-role">
                                                <i class="bi bi-shield-check"></i>
                                                {{ ucfirst(str_replace('_', ' ', $return->creator->role->name ?? 'N/A')) }}
                                            </small>

                                        </div>

                                    </div>

                                </td>

                                <!-- CREATED AT -->
                                <td>

                                    <div class="stock-return-date-wrapper">

                                        <span class="stock-return-date-icon">
                                            <i class="bi bi-calendar3"></i>
                                        </span>

                                        <span class="stock-return-date">
                                            {{ $return->created_at->format('d M Y') }}
                                        </span>

                                    </div>

                                </td>

                                <!-- PDF -->
                                <td class="text-center">

                                    <a href="{{ route('warehouse-stock-returns.download-pdf', $return->id) }}"
                                       class="stock-return-action-btn stock-return-pdf-btn"
                                       title="Download PDF">

                                        <i class="bi bi-file-earmark-pdf"></i>

                                    </a>

                                </td>

                                <!-- ACTION -->
                                <td class="text-center">

                                    @php
                                        $userWarehouseId = auth()->user()->warehouse_id;
                                        $userWarehouseType = optional(auth()->user()->warehouse)->type;
                                    @endphp

                                    <div class="stock-return-action-wrapper">

                                        {{-- MASTER → APPROVE --}}
                                        @if(
                                            $return->status === 'draft' &&
                                            $userWarehouseType === 'master' &&
                                            $userWarehouseId === $return->to_warehouse_id
                                        )

                                            <form action="{{ route('approveByMaster', $return->id) }}"
                                                  method="POST"
                                                  class="stock-return-action-form">

                                                @csrf

                                                <button type="submit"
                                                        class="stock-return-action-btn stock-return-approve-btn"
                                                        title="Approve Return">

                                                    <i class="bi bi-check-lg"></i>

                                                </button>

                                            </form>

                                        @endif


                                        {{-- FROM WAREHOUSE → DISPATCH --}}
                                        @if(
                                            $return->status === 'approved' &&
                                            $userWarehouseId === $return->from_warehouse_id
                                        )

                                            <form action="{{ route('dispatch', $return->id) }}"
                                                  method="POST"
                                                  class="stock-return-action-form">

                                                @csrf

                                                <button type="submit"
                                                        class="stock-return-action-btn stock-return-dispatch-btn"
                                                        title="Dispatch Return">

                                                    <i class="bi bi-truck"></i>

                                                </button>

                                            </form>

                                        @endif


                                        {{-- MASTER → RECEIVE --}}
                                        @if(
                                            $return->status === 'dispatched' &&
                                            $userWarehouseType === 'master' &&
                                            $userWarehouseId === $return->to_warehouse_id
                                        )

                                            <form action="{{ route('receiveAtMaster', $return->id) }}"
                                                  method="POST"
                                                  class="stock-return-action-form">

                                                @csrf

                                                <button type="submit"
                                                        class="stock-return-action-btn stock-return-receive-btn"
                                                        title="Receive Return">

                                                    <i class="bi bi-box-arrow-in-down"></i>

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="11"
                                    class="stock-return-empty-cell">

                                    <div class="stock-return-empty-state">

                                        <div class="stock-return-empty-icon">
                                            <i class="bi bi-arrow-return-left"></i>
                                        </div>

                                        <div class="stock-return-empty-title">
                                            No Stock Returns Found
                                        </div>

                                        <div class="stock-return-empty-text">
                                            There are no warehouse stock return requests available at the moment.
                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

        <!-- ================= PAGINATION ================= -->
        <div class="px-3 py-2">
            {{ $returns->onEachSide(0)->links('pagination::bootstrap-5') }}
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