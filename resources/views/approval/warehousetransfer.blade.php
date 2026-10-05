@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   WAREHOUSE / DISTRIBUTION CENTER STOCK REQUEST APPROVE
========================================================= */

.warehouse-approve-card {
    border: 0 !important;
    border-radius: 18px !important;
    overflow: hidden;
    margin-top: 28px !important;
}

/* ================= HEADER ================= */

.warehouse-approve-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 8px 16px;
}

.warehouse-approve-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.warehouse-approve-title-icon {
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

.warehouse-approve-header .card-title {
    color: #263238;
    font-size: 20px;
    font-weight: 700;
}

.warehouse-approve-subtitle {
    display: block;
    margin-top: 4px;
    color: #8b9690;
    font-size: 12px;
    font-weight: 500;
}

/* ================= SEARCH ================= */

.warehouse-approve-search {
    padding: 0 8px 10px;
}

/* ================= TABLE WRAPPER ================= */

.warehouse-approve-table-wrapper {
    margin: 8px 0 0;
    border: 1px solid #e7eee9;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
}

.warehouse-approve-table-responsive {
    width: 100%;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #b9d8c8 #f5f8f6;
}

.warehouse-approve-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.warehouse-approve-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.warehouse-approve-table-responsive::-webkit-scrollbar-thumb {
    background: #b9d8c8;
    border-radius: 10px;
}

/* ================= TABLE ================= */

.warehouse-approve-responsive-table {
    width: 100%;
    min-width: 1050px;
    table-layout: fixed;
    margin: 0 !important;
}

.warehouse-approve-responsive-table thead th {
    height: 52px;
    padding: 12px 14px;
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

.warehouse-approve-responsive-table tbody td {
    height: 62px;
    padding: 10px 14px;
    border-bottom: 1px solid #edf2ef !important;
    color: #4f5b56;
    font-size: 13px;
    font-weight: 500;
    vertical-align: middle;
    background: #fff;
}

/* ================= COLUMN WIDTHS ================= */

.warehouse-approve-warehouse-column {
    width: 220px;
    min-width: 200px;
}

.warehouse-approve-product-column {
    width: 260px;
    min-width: 220px;
}

.warehouse-approve-qty-column {
    width: 110px;
    min-width: 110px;
}

.warehouse-approve-status-column {
    width: 145px;
    min-width: 145px;
}

.warehouse-approve-action-column {
    width: 120px;
    min-width: 120px;
}

/* ================= GROUP ROW ================= */

.warehouse-approve-group-row td {
    padding: 9px 14px !important;
    background: linear-gradient(
        90deg,
        #f0f8f4,
        #f8fcfa
    ) !important;
    border-bottom: 1px solid #dcebe2 !important;
}

.warehouse-approve-group-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.warehouse-approve-group-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.warehouse-approve-group-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #e3f3ea;
    color: #198754;
    font-size: 15px;
}

.warehouse-approve-group-title {
    color: #3d4b44;
    font-size: 12px;
    font-weight: 700;
}

.warehouse-approve-group-subtitle {
    margin-top: 2px;
    color: #8a9690;
    font-size: 10px;
}

/* ================= GROUP ACTIONS ================= */

.warehouse-approve-group-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.warehouse-approve-group-actions form {
    margin: 0 !important;
}

.warehouse-approve-group-btn {
    min-height: 34px;
    padding: 0 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.18s ease;
}

.warehouse-approve-transfer-btn {
    color: #fff;
    background: #198754;
    border-color: #198754;
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.12);
}

.warehouse-approve-transfer-btn:hover {
    color: #fff;
    background: #157347;
    border-color: #157347;
    transform: translateY(-1px);
}

.warehouse-approve-receive-btn {
    color: #fff;
    background: #0d6efd;
    border-color: #0d6efd;
    box-shadow: 0 4px 10px rgba(13, 110, 253, 0.12);
}

.warehouse-approve-receive-btn:hover {
    color: #fff;
    background: #0b5ed7;
    border-color: #0b5ed7;
    transform: translateY(-1px);
}

/* ================= NORMAL ROW ================= */

.warehouse-approve-table-row {
    transition: background-color 0.18s ease;
}

.warehouse-approve-table-row:hover td {
    background: #f8fcfa !important;
}

.warehouse-approve-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}

/* ================= WAREHOUSE ================= */

.warehouse-approve-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.warehouse-approve-warehouse-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
}

.warehouse-approve-warehouse-icon.requested {
    background: #fff6e5;
    color: #c98200;
}

.warehouse-approve-warehouse-icon.approved {
    background: #eaf7ef;
    color: #198754;
}

.warehouse-approve-warehouse-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #4c5953;
    font-weight: 600;
}

/* ================= PRODUCT ================= */

.warehouse-approve-product-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.warehouse-approve-product-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eef7f2;
    color: #198754;
}

.warehouse-approve-product-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #36433d;
    font-weight: 600;
}

/* ================= QTY ================= */

.warehouse-approve-qty-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    min-width: 65px;
    min-height: 30px;
    padding: 4px 9px;
    border-radius: 8px;
    background: #edf7fb;
    color: #087ea4;
    font-size: 11px;
    font-weight: 700;
}

/* ================= STATUS ================= */

.warehouse-approve-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 90px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.warehouse-approve-status .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.status-pending {
    color: #a66a00;
    background: #fff5dc;
}

.status-dispatched {
    color: #198754;
    background: #eaf7ef;
}

.status-received {
    color: #0d6efd;
    background: #eaf2ff;
}

.status-rejected {
    color: #dc3545;
    background: #fdecef;
}

/* ================= ACTION ================= */

.warehouse-approve-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.warehouse-approve-actions form {
    margin: 0 !important;
}

.warehouse-approve-action-btn {
    width: 32px;
    height: 32px;
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    font-size: 14px;
    transition: all 0.18s ease;
}

.warehouse-reject-btn {
    color: #dc3545 !important;
    background: #fff1f2 !important;
    border-color: #f5c2c7 !important;
}

.warehouse-reject-btn:hover {
    color: #fff !important;
    background: #dc3545 !important;
    transform: translateY(-1px);
}

/* ================= MOBILE ================= */

@media (max-width: 991.98px) {

    .warehouse-approve-card {
        margin-top: 15px !important;
    }

    .warehouse-approve-header {
        padding: 18px 5px 14px;
    }

    .warehouse-approve-group-wrapper {
        align-items: flex-start;
        flex-direction: column;
    }

    .warehouse-approve-group-actions {
        width: 100%;
    }

    .warehouse-approve-group-actions form,
    .warehouse-approve-group-btn {
        width: 100%;
    }
}

@media (max-width: 575.98px) {

    .warehouse-approve-title-row {
        gap: 10px;
    }

    .warehouse-approve-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        font-size: 18px;
        border-radius: 11px;
    }

    .warehouse-approve-header .card-title {
        font-size: 17px;
        line-height: 1.35;
    }

    .warehouse-approve-subtitle {
        font-size: 11px;
    }

    .warehouse-approve-table-wrapper {
        border-radius: 11px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

    <div class="container">

        <div class="container bg-white shadow rounded p-3 warehouse-approve-card">

            <!-- ================= HEADER ================= -->
            <div class="warehouse-approve-header">

                <div class="warehouse-approve-header-title">
                    <div class="warehouse-approve-title-row">

                        <div class="warehouse-approve-title-icon">
                            <i class="bi bi-check2-square"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Warehouse / Distribution Center Stock Request Approve
                            </h4>

                            <span class="warehouse-approve-subtitle">
                                Review, transfer and manage warehouse stock requests
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            <!-- ================= SEARCH ================= -->
            <div class="warehouse-approve-search">
                <x-datatable-search />
            </div>

            <!-- ================= TABLE ================= -->
            <div class="warehouse-approve-table-wrapper">

                <div class="warehouse-approve-table-responsive">

                    <table id="transfersTable"
                           class="table warehouse-approve-responsive-table mb-0">

                        <thead>
                            <tr>
                                <th class="warehouse-approve-warehouse-column">
                                    Requested By Warehouse
                                </th>

                                <th class="warehouse-approve-warehouse-column">
                                    Approved By Warehouse
                                </th>

                                <th class="warehouse-approve-product-column">
                                    Product
                                </th>

                                <th class="text-center warehouse-approve-qty-column">
                                    Qty
                                </th>

                                <th class="text-center warehouse-approve-status-column">
                                    Status
                                </th>

                                <th class="text-center warehouse-approve-action-column">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($transfers ?? collect() as $groupKey => $group)

                                @php
                                    $first = $group->first();
                                    $userWarehouseId = auth()->user()->warehouse_id;
                                @endphp

                                <!-- ================= GROUP ACTION ROW ================= -->
                                <tr class="warehouse-approve-group-row">

                                    <td colspan="6">

                                        <div class="warehouse-approve-group-wrapper">

                                            <div class="warehouse-approve-group-info">

                                                <div class="warehouse-approve-group-icon">
                                                    <i class="bi bi-diagram-3"></i>
                                                </div>

                                                <div>
                                                    <div class="warehouse-approve-group-title">
                                                        Stock Transfer Group
                                                    </div>

                                                    <div class="warehouse-approve-group-subtitle">
                                                        {{ $group->count() }}
                                                        {{ $group->count() == 1 ? 'Product' : 'Products' }}
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="warehouse-approve-group-actions">

                                                {{-- MASTER: Dispatch All --}}
                                                @if ($first->status == 0 && $first->approved_by_warehouse_id == $userWarehouseId)

                                                    <form method="GET"
                                                          action="{{ route('transfer-challans.create') }}">

                                                        <input type="hidden"
                                                               name="from_warehouse_id"
                                                               value="{{ $first->approved_by_warehouse_id }}">

                                                        <input type="hidden"
                                                               name="to_warehouse_id"
                                                               value="{{ $first->requested_by_warehouse_id }}">

                                                        <input type="hidden"
                                                               name="transfer_group"
                                                               value="{{ $groupKey }}">

                                                        <button type="submit"
                                                                class="warehouse-approve-group-btn warehouse-approve-transfer-btn">

                                                            <i class="bi bi-arrow-left-right"></i>

                                                            <span>
                                                                All-Product Transfer Challan
                                                            </span>

                                                        </button>

                                                    </form>

                                                @endif


                                                {{-- DISTRICT: Receive All --}}
                                                @if ($first->status == 1 && $first->requested_by_warehouse_id == $userWarehouseId)

                                                    <form method="POST"
                                                          action="{{ route('warehouse.transfer.receive.bulk') }}">

                                                        @csrf

                                                        @foreach ($group as $t)
                                                            <input type="hidden"
                                                                   name="transfer_ids[]"
                                                                   value="{{ $t->id }}">
                                                        @endforeach

                                                        <button type="submit"
                                                                class="warehouse-approve-group-btn warehouse-approve-receive-btn">

                                                            <i class="bi bi-box-arrow-in-down"></i>

                                                            <span>
                                                                Receive All
                                                            </span>

                                                        </button>

                                                    </form>

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>


                                <!-- ================= PRODUCTS ================= -->
                                @foreach ($group as $t)

                                    <tr class="warehouse-approve-table-row">

                                        <!-- REQUESTED BY -->
                                        <td>

                                            <div class="warehouse-approve-warehouse-wrapper">

                                                <span class="warehouse-approve-warehouse-icon requested">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                </span>

                                                <span class="warehouse-approve-warehouse-name"
                                                      title="{{ $t->requestedByWarehouse->name ?? '-' }}">

                                                    {{ $t->requestedByWarehouse->name ?? '-' }}

                                                </span>

                                            </div>

                                        </td>


                                        <!-- APPROVED BY -->
                                        <td>

                                            <div class="warehouse-approve-warehouse-wrapper">

                                                <span class="warehouse-approve-warehouse-icon approved">
                                                    <i class="bi bi-building-check"></i>
                                                </span>

                                                <span class="warehouse-approve-warehouse-name"
                                                      title="{{ $t->approvedByWarehouse->name ?? '-' }}">

                                                    {{ $t->approvedByWarehouse->name ?? '-' }}

                                                </span>

                                            </div>

                                        </td>


                                        <!-- PRODUCT -->
                                        <td>

                                            <div class="warehouse-approve-product-wrapper">

                                                <div class="warehouse-approve-product-icon">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>

                                                <span class="warehouse-approve-product-name"
                                                      title="{{ $t->product->name ?? '-' }}">

                                                    {{ $t->product->name ?? '-' }}

                                                </span>

                                            </div>

                                        </td>


                                        <!-- QTY -->
                                        <td class="text-center">

                                            <span class="warehouse-approve-qty-badge">

                                                <i class="bi bi-stack"></i>

                                                {{ $t->challan?->items->where('product_id', $t->product_id)->first()?->quantity ?? $t->quantity }}

                                            </span>

                                        </td>


                                        <!-- STATUS -->
                                        <td class="text-center">

                                            @if ($t->status == 0)

                                                <span class="warehouse-approve-status status-pending">
                                                    <span class="status-dot"></span>
                                                    Pending
                                                </span>

                                            @elseif($t->status == 1)

                                                <span class="warehouse-approve-status status-dispatched">
                                                    <span class="status-dot"></span>
                                                    Dispatched
                                                </span>

                                            @elseif($t->status == 2)

                                                <span class="warehouse-approve-status status-received">
                                                    <span class="status-dot"></span>
                                                    Received
                                                </span>

                                            @elseif($t->status == 3)

                                                <span class="warehouse-approve-status status-rejected">
                                                    <span class="status-dot"></span>
                                                    Rejected
                                                </span>

                                            @endif

                                        </td>


                                        <!-- ACTION -->
                                        <td class="text-center">

                                            <div class="warehouse-approve-actions">

                                                {{-- MASTER: Single Dispatch / Reject --}}
                                                @if ($t->status == 0 && $t->approved_by_warehouse_id == $userWarehouseId)

                                                    <form method="POST"
                                                          action="{{ route('warehouse.transfer.reject', $t->id) }}">

                                                        @csrf

                                                        <button type="submit"
                                                                class="warehouse-approve-action-btn warehouse-reject-btn"
                                                                title="Reject Transfer"
                                                                onclick="return confirm('Are you sure you want to reject this transfer?')">

                                                            <i class="bi bi-x-lg"></i>

                                                        </button>

                                                    </form>

                                                @endif

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
    
@endsection