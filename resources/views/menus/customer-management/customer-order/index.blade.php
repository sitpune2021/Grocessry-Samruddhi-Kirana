@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   CUSTOMER ORDER HEADER
========================================================= */

.customer-order-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 20px 24px 18px;

    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #fbfffd 100%
    );
}

.customer-order-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.customer-order-title-icon {
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

.customer-order-header .card-title {
    color: #263238;

    font-size: 20px;
    font-weight: 700;
}

.customer-order-subtitle {
    display: block;

    margin-top: 4px;

    color: #8b9690;

    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.customer-order-table-wrapper {
    margin: 8px 20px 20px;

    border: 1px solid #e7eee9;
    border-radius: 14px;

    overflow: hidden;

    background: #fff;
}

.customer-order-table-responsive {
    width: 100%;

    overflow-x: auto;

    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.customer-order-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.customer-order-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.customer-order-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.customer-order-responsive-table {
    width: 100%;

    min-width: 2350px;

    table-layout: fixed;

    margin: 0 !important;
}

.customer-order-responsive-table thead th {
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

.customer-order-responsive-table tbody td {
    min-height: 66px;

    padding: 12px 13px;

    border-bottom: 1px solid #edf2ef !important;

    color: #4f5b56;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;

    background: #fff;
}

.customer-order-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.customer-order-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.customer-order-customer-column {
    width: 190px;
    min-width: 180px;
}

.customer-order-address-column {
    width: 260px;
    min-width: 240px;
}

.customer-order-number-column {
    width: 175px;
    min-width: 160px;
}

.customer-order-agent-column {
    width: 200px;
    min-width: 180px;
}

.customer-order-product-column {
    width: 250px;
    min-width: 220px;
}

.customer-order-quantity-column {
    width: 110px;
    min-width: 100px;
}

.customer-order-price-column {
    width: 140px;
    min-width: 125px;
}

.customer-order-total-column {
    width: 150px;
    min-width: 135px;
}

.customer-order-type-column {
    width: 140px;
    min-width: 125px;
}

.customer-order-payment-column {
    width: 165px;
    min-width: 145px;
}

.customer-order-payment-status-column {
    width: 145px;
    min-width: 130px;
}

.customer-order-status-column {
    width: 145px;
    min-width: 130px;
}

.customer-order-action-column {
    width: 150px;
    min-width: 140px;
}


/* =========================================================
   ROW HOVER
========================================================= */

.customer-order-table-row {
    transition: background-color 0.18s ease;
}

.customer-order-table-row:hover td {
    background: #f8fcfa !important;
}

.customer-order-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.customer-order-sr-badge {
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
   CUSTOMER
========================================================= */

.customer-order-customer-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.customer-order-customer-icon {
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

.customer-order-customer-name {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-weight: 600;
}


/* =========================================================
   ADDRESS
========================================================= */

.customer-order-address-wrapper {
    display: flex;
    align-items: flex-start;

    gap: 9px;
}

.customer-order-address-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-top: 2px;

    border-radius: 9px;

    background: #eef7f2;

    color: #198754;
}

.customer-order-address-text {
    color: #68746e;

    font-size: 12px;
    line-height: 1.55;
}

.customer-order-phone {
    display: flex;
    align-items: center;
    gap: 5px;

    margin-top: 3px;

    color: #198754;

    font-weight: 600;
}


/* =========================================================
   ORDER NUMBER
========================================================= */

.customer-order-number-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.customer-order-number-icon {
    width: 31px;
    height: 31px;
    flex: 0 0 31px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef4ff;

    color: #0d6efd;
}

.customer-order-number {
    color: #3f4c46;

    font-size: 12px;
    font-weight: 700;

    white-space: nowrap;
}


/* =========================================================
   DELIVERY AGENT
========================================================= */

.customer-order-agent-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.customer-order-agent-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #eef4ff;

    color: #0d6efd;
}

.customer-order-agent-name {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-weight: 600;
}

.customer-order-no-agent {
    color: #9aa49f;

    font-size: 12px;

    font-style: italic;
}


/* =========================================================
   PRODUCT LIST
========================================================= */

.customer-order-product-list {
    display: flex;
    flex-direction: column;

    gap: 6px;
}

.customer-order-product-item {
    display: flex;
    align-items: center;

    gap: 7px;

    min-width: 0;

    color: #4f5b56;

    font-size: 12px;
}

.customer-order-product-item > span:last-child {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.customer-order-product-icon {
    width: 27px;
    height: 27px;
    flex: 0 0 27px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: #eef7f2;

    color: #198754;

    font-size: 11px;
}


/* =========================================================
   QUANTITY / PRICE LIST
========================================================= */

.customer-order-item-list {
    display: flex;
    flex-direction: column;

    gap: 6px;
}

.customer-order-item-row {
    display: flex;
    align-items: center;

    gap: 7px;

    font-weight: 600;
}

.customer-order-small-icon {
    width: 27px;
    height: 27px;
    flex: 0 0 27px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: #eef7f2;

    color: #198754;

    font-size: 11px;
}

.customer-order-small-icon.price {
    background: #eaf7ef;
    color: #198754;
}


/* =========================================================
   TOTAL
========================================================= */

.customer-order-total-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 95px;

    padding: 8px 12px;

    border-radius: 9px;

    background: #eaf7ef;

    color: #198754;

    font-size: 13px;
    font-weight: 800;
}


/* =========================================================
   ORDER TYPE
========================================================= */

.customer-order-channel-badge {
    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding: 7px 10px;

    border-radius: 18px;

    background: #eef4ff;

    color: #0d6efd;

    font-size: 10px;
    font-weight: 800;

    text-transform: capitalize;
}


/* =========================================================
   PAYMENT METHOD
========================================================= */

.customer-order-payment-badge {
    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding: 7px 10px;

    border-radius: 18px;

    background: #f3efff;

    color: #6f42c1;

    font-size: 10px;
    font-weight: 800;

    text-transform: capitalize;
}


/* =========================================================
   PAYMENT STATUS
========================================================= */

.customer-order-payment-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    min-width: 80px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 0.3px;
}

.customer-order-status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: currentColor;
}

.customer-order-payment-status.paid {
    color: #198754;
    background: #eaf7ef;
}

.customer-order-payment-status.pending {
    color: #a66a00;
    background: #fff5dc;
}


/* =========================================================
   ORDER STATUS
========================================================= */

.customer-order-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 85px;

    padding: 7px 10px;

    border-radius: 18px;

    background: #eef7f2;

    color: #198754;

    font-size: 10px;
    font-weight: 800;

    text-transform: capitalize;
}


/* =========================================================
   ACTIONS
========================================================= */

.customer-order-actions {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 6px;
}

.customer-order-action-form {
    display: inline-flex;

    margin: 0 !important;
}

.customer-order-action-btn {
    width: 34px;
    height: 34px;

    padding: 0 !important;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px !important;

    border: 1px solid transparent !important;

    font-size: 14px;

    transition: all 0.18s ease;
}


/* Assign */

.customer-order-assign-btn {
    color: #198754 !important;

    background: #eaf7ef !important;

    border-color: #b8dfc9 !important;
}

.customer-order-assign-btn:hover {
    color: #fff !important;

    background: #198754 !important;

    transform: translateY(-1px);
}


/* Delivered */

.customer-order-delivered-btn {
    color: #0d6efd !important;

    background: #eaf2ff !important;

    border-color: #b8d3ff !important;
}

.customer-order-delivered-btn:hover {
    color: #fff !important;

    background: #0d6efd !important;

    transform: translateY(-1px);
}


/* Reject */

.customer-order-reject-btn {
    color: #dc3545 !important;

    background: #fff1f2 !important;

    border-color: #f5c2c7 !important;
}

.customer-order-reject-btn:hover {
    color: #fff !important;

    background: #dc3545 !important;

    transform: translateY(-1px);
}

.customer-order-no-action {
    color: #adb5b0;

    font-size: 18px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.customer-order-empty-cell {
    padding: 60px 20px !important;

    text-align: center !important;
}

.customer-order-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-direction: column;
}

.customer-order-empty-icon {
    width: 62px;
    height: 62px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 17px;

    background: #f0f7f3;

    color: #198754;

    font-size: 27px;
}

.customer-order-empty-title {
    color: #44514b;

    font-size: 15px;
    font-weight: 700;
}

.customer-order-empty-text {
    margin-top: 4px;

    color: #98a29d;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .customer-order-header {
        padding: 18px 15px 15px;
    }

    .customer-order-table-wrapper {
        margin: 8px 12px 15px;
    }
}


@media (max-width: 575.98px) {

    .customer-order-header {
        padding: 18px 15px 15px;
    }

    .customer-order-title-row {
        gap: 10px;
    }

    .customer-order-title-icon {
        width: 40px;
        height: 40px;

        flex-basis: 40px;

        font-size: 18px;

        border-radius: 11px;
    }

    .customer-order-header .card-title {
        font-size: 17px;
    }

    .customer-order-subtitle {
        font-size: 11px;
    }

    .customer-order-table-wrapper {
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

            <!-- ================= HEADER ================= -->
            <div class="customer-order-header">

                <div class="customer-order-header-title">
                    <div class="customer-order-title-row">

                        <div class="customer-order-title-icon">
                            <i class="bi bi-bag-check-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Customer Orders
                            </h4>

                            <span class="customer-order-subtitle">
                                Manage customer orders, payments and delivery status
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

            <!-- ================= TABLE ================= -->
            <div class="customer-order-table-wrapper">

                <div class="customer-order-table-responsive">

                    <table id="driverVehicleTable"
                        class="table customer-order-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center customer-order-sr-column">
                                    Sr No
                                </th>

                                <th class="customer-order-customer-column">
                                    Customer Name
                                </th>

                                <th class="customer-order-address-column">
                                    Customer Address
                                </th>

                                <th class="customer-order-number-column">
                                    Order Number
                                </th>

                                <th class="customer-order-agent-column">
                                    Delivery Agent Name
                                </th>

                                <th class="customer-order-product-column">
                                    Product
                                </th>

                                <th class="customer-order-quantity-column">
                                    Quantity
                                </th>

                                <th class="customer-order-price-column">
                                    Price
                                </th>

                                <th class="customer-order-total-column">
                                    Total
                                </th>

                                <th class="customer-order-type-column">
                                    Order Type
                                </th>

                                <th class="customer-order-payment-column">
                                    Payment Method
                                </th>

                                <th class="customer-order-payment-status-column">
                                    Payment Status
                                </th>

                                <th class="customer-order-status-column">
                                    Status
                                </th>

                                <th class="text-center customer-order-action-column">
                                    Actions
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse($orders as $index => $order)

                                <tr class="customer-order-table-row">

                                    <!-- Sr No -->
                                    <td class="text-center">

                                        <span class="customer-order-sr-badge">
                                            {{ $loop->iteration }}
                                        </span>

                                    </td>


                                    <!-- Customer -->
                                    <td>

                                        <div class="customer-order-customer-wrapper">

                                            <div class="customer-order-customer-icon">
                                                <i class="bi bi-person"></i>
                                            </div>

                                            <span class="customer-order-customer-name"
                                                title="{{ $order->user->first_name ?? '-' }} {{ $order->user->last_name ?? '' }}">

                                                {{ $order->user->first_name ?? '-' }}
                                                {{ $order->user->last_name ?? '' }}

                                            </span>

                                        </div>

                                    </td>


                                    <!-- Address -->
                                    <td>

                                        <div class="customer-order-address-wrapper">

                                            <div class="customer-order-address-icon">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>

                                            <div class="customer-order-address-text">

                                                <div>
                                                    {{ $order->address->landmark ?? '' }},
                                                    {{ $order->address->flat_house ?? '' }}
                                                </div>

                                                <div>
                                                    {{ $order->address->area ?? '' }}
                                                </div>

                                                <div>
                                                    {{ $order->address->city ?? '' }}
                                                    {{ $order->address->postcode ?? '' }}
                                                </div>

                                                <div class="customer-order-phone">
                                                    <i class="bi bi-telephone"></i>
                                                    {{ $order->address->phone ?? '' }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- Order Number -->
                                    <td>

                                        <div class="customer-order-number-wrapper">

                                            <span class="customer-order-number-icon">
                                                <i class="bi bi-receipt"></i>
                                            </span>

                                            <span class="customer-order-number">
                                                {{ $order->order_number }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Delivery Agent -->
                                    <td>

                                        <div class="customer-order-agent-wrapper">

                                            <div class="customer-order-agent-icon">
                                                <i class="bi bi-person-badge"></i>
                                            </div>

                                            @if($order->deliveryAgent && $order->deliveryAgent->user)

                                                <span class="customer-order-agent-name"
                                                    title="{{ $order->deliveryAgent->user->first_name }} {{ $order->deliveryAgent->user->last_name }}">

                                                    {{ $order->deliveryAgent->user->first_name }}
                                                    {{ $order->deliveryAgent->user->last_name }}

                                                </span>

                                            @else

                                                <span class="customer-order-no-agent">
                                                    Not Assigned
                                                </span>

                                            @endif

                                        </div>

                                    </td>


                                    <!-- Products -->
                                    <td>

                                        <div class="customer-order-product-list">

                                            @foreach($order->orderItems as $item)

                                                <div class="customer-order-product-item">

                                                    <span class="customer-order-product-icon">
                                                        <i class="bi bi-box-seam"></i>
                                                    </span>

                                                    <span title="{{ $item->product->name ?? '-' }}">
                                                        {{ $item->product->name ?? '-' }}
                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    </td>


                                    <!-- Quantity -->
                                    <td>

                                        <div class="customer-order-item-list">

                                            @foreach($order->orderItems as $item)

                                                <div class="customer-order-item-row">

                                                    <span class="customer-order-small-icon">
                                                        <i class="bi bi-stack"></i>
                                                    </span>

                                                    <span>
                                                        {{ $item->quantity }}
                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    </td>


                                    <!-- Price -->
                                    <td>

                                        <div class="customer-order-item-list">

                                            @foreach($order->orderItems as $item)

                                                <div class="customer-order-item-row">

                                                    <span class="customer-order-small-icon price">
                                                        <i class="bi bi-currency-rupee"></i>
                                                    </span>

                                                    <span>
                                                        ₹{{ number_format($item->price, 2) }}
                                                    </span>

                                                </div>

                                            @endforeach

                                        </div>

                                    </td>


                                    <!-- Total -->
                                    <td>

                                        <span class="customer-order-total-badge">

                                            ₹{{ number_format($order->orderItems->sum('total'), 2) }}

                                        </span>

                                    </td>


                                    <!-- Order Type -->
                                    <td>

                                        <span class="customer-order-channel-badge">

                                            <i class="bi bi-shop"></i>

                                            {{ ucfirst($order->channel) }}

                                        </span>

                                    </td>


                                    <!-- Payment Method -->
                                    <td>

                                        <span class="customer-order-payment-badge">

                                            <i class="bi bi-wallet2"></i>

                                            {{ ucfirst($order->payment_method) }}

                                        </span>

                                    </td>


                                    <!-- Payment Status -->
                                    <td class="text-center">

                                        @if($order->payment_status == 'paid')

                                            <span class="customer-order-payment-status paid">

                                                <span class="customer-order-status-dot"></span>

                                                Paid

                                            </span>

                                        @else

                                            <span class="customer-order-payment-status pending">

                                                <span class="customer-order-status-dot"></span>

                                                Pending

                                            </span>

                                        @endif

                                    </td>


                                    <!-- Order Status -->
                                    <td class="text-center">

                                        <span class="customer-order-status-badge">

                                            {{ ucfirst($order->status) }}

                                        </span>

                                    </td>


                                    <!-- Actions -->
                                    <td class="text-center">

                                        <div class="customer-order-actions">

                                            @if(
                                                in_array($order->status, ['pending', 'confirmed']) &&
                                                (
                                                    $order->payment_method == 'cash'
                                                    || $order->payment_status == 'paid'
                                                )
                                            )

                                                <button class="customer-order-action-btn customer-order-assign-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#assignAgentModal"
                                                        data-order-id="{{ $order->id }}"
                                                        title="Assign Delivery Agent">

                                                    <i class="bi bi-person-plus"></i>

                                                </button>

                                            @elseif($order->status === 'assigned')

                                                <form method="POST"
                                                    action="{{ route('admin.status.update') }}"
                                                    class="customer-order-action-form">

                                                    @csrf

                                                    <input type="hidden"
                                                        name="order_id"
                                                        value="{{ $order->id }}">

                                                    <input type="hidden"
                                                        name="status"
                                                        value="delivered">

                                                    <button type="submit"
                                                            class="customer-order-action-btn customer-order-delivered-btn"
                                                            title="Mark as Delivered">

                                                        <i class="bi bi-check-lg"></i>

                                                    </button>

                                                </form>


                                                <form method="POST"
                                                    action="{{ route('admin.status.update') }}"
                                                    class="customer-order-action-form">

                                                    @csrf

                                                    <input type="hidden"
                                                        name="order_id"
                                                        value="{{ $order->id }}">

                                                    <input type="hidden"
                                                        name="status"
                                                        value="rejected">

                                                    <button type="submit"
                                                            class="customer-order-action-btn customer-order-reject-btn"
                                                            title="Reject Order">

                                                        <i class="bi bi-x-lg"></i>

                                                    </button>

                                                </form>

                                            @else

                                                <span class="customer-order-no-action">
                                                    —
                                                </span>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="14"
                                        class="customer-order-empty-cell">

                                        <div class="customer-order-empty-state">

                                            <div class="customer-order-empty-icon">
                                                <i class="bi bi-bag-x"></i>
                                            </div>

                                            <div class="customer-order-empty-title">
                                                No Orders Found
                                            </div>

                                            <div class="customer-order-empty-text">
                                                There are no customer orders available at the moment.
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

    <div class="modal fade" id="assignAgentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.assign.delivery') }}">
                @csrf

                <input type="hidden" name="order_id" id="modal_order_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Delivery Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Agent</label>
                            <select name="delivery_agent_id" class="form-select" required>
                                <option value="">Select Agent</option>
                                @foreach($deliveryAgents as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->user->first_name }} {{ $agent->user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Assign
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('assignAgentModal');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');

            document.getElementById('modal_order_id').value = orderId;
        });
    });
</script>
@endpush