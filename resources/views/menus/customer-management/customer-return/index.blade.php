@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   CUSTOMER RETURN HEADER
========================================================= */

.customer-return-header {
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

.customer-return-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.customer-return-title-icon {
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

.customer-return-header .card-title {
    color: #263238;

    font-size: 20px;
    font-weight: 700;
}

.customer-return-subtitle {
    display: block;

    margin-top: 4px;

    color: #8b9690;

    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.customer-return-table-wrapper {
    margin: 8px 20px 20px;

    border: 1px solid #e7eee9;
    border-radius: 14px;

    overflow: hidden;

    background: #fff;
}

.customer-return-table-responsive {
    width: 100%;

    overflow-x: auto;

    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.customer-return-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.customer-return-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.customer-return-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}


/* =========================================================
   TABLE
========================================================= */

.customer-return-responsive-table {
    width: 100%;

    min-width: 1450px;

    table-layout: fixed;

    margin: 0 !important;
}

.customer-return-responsive-table thead th {
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

.customer-return-responsive-table tbody td {
    min-height: 66px;

    padding: 12px 13px;

    border-bottom: 1px solid #edf2ef !important;

    color: #4f5b56;

    font-size: 13px;
    font-weight: 500;

    vertical-align: middle;

    background: #fff;
}

.customer-return-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.customer-return-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.customer-return-customer-column {
    width: 190px;
    min-width: 175px;
}

.customer-return-order-column {
    width: 180px;
    min-width: 165px;
}

.customer-return-product-column {
    width: 230px;
    min-width: 210px;
}

.customer-return-image-column {
    width: 170px;
    min-width: 150px;
}

.customer-return-price-column {
    width: 145px;
    min-width: 130px;
}

.customer-return-total-column {
    width: 160px;
    min-width: 145px;
}

.customer-return-status-column {
    width: 145px;
    min-width: 130px;
}

.customer-return-action-column {
    width: 155px;
    min-width: 145px;
}


/* =========================================================
   ROW HOVER
========================================================= */

.customer-return-table-row {
    transition: background-color 0.18s ease;
}

.customer-return-table-row:hover td {
    background: #f8fcfa !important;
}

.customer-return-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.customer-return-sr-badge {
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

.customer-return-customer-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.customer-return-customer-icon {
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

.customer-return-customer-name {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-weight: 600;
}


/* =========================================================
   ORDER NUMBER
========================================================= */

.customer-return-order-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.customer-return-order-icon {
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

.customer-return-order-number {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   PRODUCT
========================================================= */

.customer-return-product-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;
}

.customer-return-product-icon {
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

.customer-return-product-name {
    overflow: hidden;

    text-overflow: ellipsis;
    white-space: nowrap;

    color: #3f4c46;

    font-weight: 600;
}


/* =========================================================
   PRODUCT IMAGES
========================================================= */

.customer-return-images {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    flex-wrap: wrap;
}

.customer-return-image-link {
    display: inline-flex;

    text-decoration: none;

    transition: transform 0.18s ease;
}

.customer-return-image-link:hover {
    transform: translateY(-2px);
}

.customer-return-product-image {
    width: 46px;
    height: 46px;

    object-fit: cover;

    border-radius: 9px;

    border: 2px solid #e3eee8;

    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
}

.customer-return-no-image {
    display: inline-flex;
    align-items: center;
    gap: 5px;

    color: #9aa49f;

    font-size: 11px;
}


/* =========================================================
   PRICE
========================================================= */

.customer-return-price-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;

    font-weight: 600;
}

.customer-return-price-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eaf7ef;

    color: #198754;

    font-size: 12px;
}


/* =========================================================
   TOTAL
========================================================= */

.customer-return-total-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 100px;

    padding: 8px 12px;

    border-radius: 9px;

    background: #eaf7ef;

    color: #198754;

    font-size: 13px;
    font-weight: 800;
}


/* =========================================================
   STATUS
========================================================= */

.customer-return-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    min-width: 85px;

    padding: 7px 11px;

    border-radius: 20px;

    background: #eef7f2;

    color: #198754;

    font-size: 10px;
    font-weight: 800;

    letter-spacing: 0.3px;

    text-transform: capitalize;
}

.customer-return-status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: currentColor;
}


/* =========================================================
   ACTIONS
========================================================= */

.customer-return-actions {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 6px;
}

.customer-return-action-btn {
    width: 34px;
    height: 34px;

    padding: 0 !important;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px !important;

    border: 1px solid transparent !important;

    font-size: 14px;

    text-decoration: none !important;

    transition: all 0.18s ease;
}


/* View */

.customer-return-view-btn {
    color: #0d6efd !important;

    background: #eaf2ff !important;

    border-color: #b8d3ff !important;
}

.customer-return-view-btn:hover {
    color: #fff !important;

    background: #0d6efd !important;

    transform: translateY(-1px);
}


/* QC */

.customer-return-qc-btn {
    color: #a66a00 !important;

    background: #fff5dc !important;

    border-color: #f2d48d !important;
}

.customer-return-qc-btn:hover {
    color: #fff !important;

    background: #f0ad00 !important;

    transform: translateY(-1px);
}

.customer-return-qc-completed {
    width: 34px;
    height: 34px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #eaf7ef;

    color: #198754;

    font-size: 16px;
}


/* =========================================================
   QC MODAL
========================================================= */

.customer-return-qc-modal {
    border: 0;

    border-radius: 16px;

    overflow: hidden;

    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.16);
}

.customer-return-qc-header {
    padding: 18px 20px;

    border-bottom: 1px solid #e8eee9;

    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #f8fcfa 100%
    );
}

.customer-return-qc-title-wrapper {
    display: flex;
    align-items: center;

    gap: 11px;
}

.customer-return-qc-title-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #eaf7ef;

    color: #198754;

    font-size: 18px;
}

.customer-return-qc-header .modal-title {
    color: #263238;

    font-size: 16px;
    font-weight: 700;
}

.customer-return-qc-header small {
    color: #8b9690;

    font-size: 11px;
}

.customer-return-qc-body {
    padding: 22px 20px;
}

.customer-return-form-group {
    margin-bottom: 18px;
}

.customer-return-form-group:last-child {
    margin-bottom: 0;
}

.customer-return-form-group .form-label {
    margin-bottom: 7px;

    color: #52605a;

    font-size: 12px;
    font-weight: 700;
}

.customer-return-select-wrapper {
    position: relative;
}

.customer-return-select-wrapper > i {
    position: absolute;

    top: 50%;
    left: 12px;

    z-index: 2;

    color: #198754;

    transform: translateY(-50%);
}

.customer-return-select {
    min-height: 43px;

    padding-left: 36px;

    border: 1px solid #dfe9e3;

    border-radius: 9px;

    color: #4f5b56;

    font-size: 13px;

    box-shadow: none !important;
}

.customer-return-select:focus {
    border-color: #8fc8a9;

    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.08) !important;
}

.customer-return-qc-footer {
    padding: 15px 20px;

    border-top: 1px solid #e8eee9;

    background: #fafcfb;
}

.customer-return-submit-btn,
.customer-return-cancel-btn {
    min-height: 38px;

    padding: 0 15px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border-radius: 9px;

    font-size: 12px;
    font-weight: 600;
}

.customer-return-submit-btn {
    border: 0;

    color: #fff;

    background: linear-gradient(
        135deg,
        #198754,
        #157347
    );

    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.16);
}

.customer-return-cancel-btn {
    border: 1px solid #dfe5e1;

    color: #65716b;

    background: #fff;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.customer-return-empty-cell {
    padding: 60px 20px !important;

    text-align: center !important;
}

.customer-return-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-direction: column;
}

.customer-return-empty-icon {
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

.customer-return-empty-title {
    color: #44514b;

    font-size: 15px;
    font-weight: 700;
}

.customer-return-empty-text {
    margin-top: 4px;

    color: #98a29d;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .customer-return-header {
        padding: 18px 15px 15px;
    }

    .customer-return-table-wrapper {
        margin: 8px 12px 15px;
    }
}


@media (max-width: 575.98px) {

    .customer-return-header {
        padding: 18px 15px 15px;
    }

    .customer-return-title-row {
        gap: 10px;
    }

    .customer-return-title-icon {
        width: 40px;
        height: 40px;

        flex-basis: 40px;

        font-size: 18px;

        border-radius: 11px;
    }

    .customer-return-header .card-title {
        font-size: 17px;
    }

    .customer-return-subtitle {
        font-size: 11px;
    }

    .customer-return-table-wrapper {
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
            <div class="customer-return-header">

                <div class="customer-return-header-title">
                    <div class="customer-return-title-row">

                        <div class="customer-return-title-icon">
                            <i class="bi bi-arrow-return-left"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Customer Order Returns
                            </h4>

                            <span class="customer-return-subtitle">
                                Manage customer returns, product details and quality checks
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- ================= TABLE ================= -->
            <div class="customer-return-table-wrapper">

                <div class="customer-return-table-responsive">

                    <table id="driverVehicleTable"
                        class="table customer-return-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center customer-return-sr-column">
                                    Sr No
                                </th>

                                <th class="customer-return-customer-column">
                                    Customer Name
                                </th>

                                <th class="customer-return-order-column">
                                    Order Number
                                </th>

                                <th class="customer-return-product-column">
                                    Product
                                </th>

                                <th class="text-center customer-return-image-column">
                                    Product Image
                                </th>

                                <th class="customer-return-price-column">
                                    Price
                                </th>

                                <th class="customer-return-total-column">
                                    Total
                                </th>

                                <th class="text-center customer-return-status-column">
                                    Status
                                </th>

                                <th class="text-center customer-return-action-column">
                                    Actions
                                </th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse($returns as $index => $return)

                                <tr class="customer-return-table-row">

                                    <!-- Sr No -->
                                    <td class="text-center">

                                        <span class="customer-return-sr-badge">
                                            {{ $index + 1 }}
                                        </span>

                                    </td>


                                    <!-- Customer -->
                                    <td>

                                        <div class="customer-return-customer-wrapper">

                                            <div class="customer-return-customer-icon">
                                                <i class="bi bi-person"></i>
                                            </div>

                                            <span class="customer-return-customer-name"
                                                title="{{ $return->customer->first_name ?? '-' }}">

                                                {{ $return->customer->first_name ?? '-' }}

                                            </span>

                                        </div>

                                    </td>


                                    <!-- Order Number -->
                                    <td>

                                        <div class="customer-return-order-wrapper">

                                            <span class="customer-return-order-icon">
                                                <i class="bi bi-receipt"></i>
                                            </span>

                                            <span class="customer-return-order-number">
                                                {{ $return->order->order_number ?? '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Product -->
                                    <td>

                                        <div class="customer-return-product-wrapper">

                                            <div class="customer-return-product-icon">
                                                <i class="bi bi-box-seam"></i>
                                            </div>

                                            <span class="customer-return-product-name"
                                                title="{{ $return->product->name ?? '-' }}">

                                                {{ $return->product->name ?? '-' }}

                                            </span>

                                        </div>

                                    </td>


                                    <!-- Product Images -->
                                    <td class="text-center">

                                        <div class="customer-return-images">

                                            @forelse($return->product_images ?? [] as $image)

                                                <a href="{{ asset('storage/customer_return_products/'.$image) }}"
                                                target="_blank"
                                                class="customer-return-image-link">

                                                    <img src="{{ asset('storage/'.$image) }}"
                                                        alt="Return Product"
                                                        class="customer-return-product-image">

                                                </a>

                                            @empty

                                                <span class="customer-return-no-image">
                                                    <i class="bi bi-image"></i>
                                                    No Images
                                                </span>

                                            @endforelse

                                        </div>

                                    </td>


                                    <!-- Price -->
                                    <td>

                                        <div class="customer-return-price-wrapper">

                                            <span class="customer-return-price-icon">
                                                <i class="bi bi-currency-rupee"></i>
                                            </span>

                                            <span>
                                                ₹{{ number_format($return->orderItem->price ?? 0, 2) }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- Total -->
                                    <td>

                                        <span class="customer-return-total-badge">

                                            ₹{{ number_format(($return->orderItem->price ?? 0) * $return->quantity, 2) }}

                                        </span>

                                    </td>


                                    <!-- Status -->
                                    <td class="text-center">

                                        <span class="customer-return-status">

                                            <span class="customer-return-status-dot"></span>

                                            {{ ucfirst($return->status) }}

                                        </span>

                                    </td>


                                    <!-- Actions -->
                                    <td class="text-center">

                                        <div class="customer-return-actions">

                                            <!-- View -->
                                            <a href="{{ route('customer-returns.show', $return->id) }}"
                                            class="customer-return-action-btn customer-return-view-btn"
                                            title="View Return">

                                                <i class="bi bi-eye"></i>

                                            </a>


                                            <!-- QC -->
                                            @if(in_array($return->qc_status, [null, 'pending']) && $return->status === 'requested')

                                                <button type="button"
                                                        class="customer-return-action-btn customer-return-qc-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#qcModal{{ $return->id }}"
                                                        title="Quality Check">

                                                    <i class="bi bi-clipboard-check"></i>

                                                </button>

                                            @else

                                                <span class="customer-return-qc-completed"
                                                    title="QC Completed">

                                                    <i class="bi bi-check-circle-fill"></i>

                                                </span>

                                            @endif

                                        </div>


                                        <!-- ================= QC MODAL ================= -->
                                        <div class="modal fade"
                                            id="qcModal{{ $return->id }}"
                                            tabindex="-1"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-md modal-dialog-centered">

                                                <div class="modal-content customer-return-qc-modal">

                                                    <div class="modal-header customer-return-qc-header">

                                                        <div class="customer-return-qc-title-wrapper">

                                                            <div class="customer-return-qc-title-icon">
                                                                <i class="bi bi-clipboard-check"></i>
                                                            </div>

                                                            <div>
                                                                <h5 class="modal-title">
                                                                    Quality Check (QC)
                                                                </h5>

                                                                <small>
                                                                    Review returned product
                                                                </small>
                                                            </div>

                                                        </div>

                                                        <button type="button"
                                                                class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>

                                                    </div>


                                                    <form method="POST"
                                                        action="{{ route('customer-returns.update', $return->id) }}">

                                                        @csrf
                                                        @method('PUT')

                                                        <div class="modal-body customer-return-qc-body">

                                                            <!-- QC Status -->
                                                            <div class="customer-return-form-group">

                                                                <label class="form-label">
                                                                    QC Status
                                                                </label>

                                                                <div class="customer-return-select-wrapper">

                                                                    <i class="bi bi-clipboard-check"></i>

                                                                    <select name="qc_status"
                                                                            class="form-select customer-return-select"
                                                                            required>

                                                                        <option value="">
                                                                            Select
                                                                        </option>

                                                                        <option value="passed">
                                                                            Passed
                                                                        </option>

                                                                        <option value="failed">
                                                                            Failed
                                                                        </option>

                                                                        <option value="partial">
                                                                            Partial
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                            </div>


                                                            <!-- Action -->
                                                            <div class="customer-return-form-group">

                                                                <label class="form-label">
                                                                    Action
                                                                </label>

                                                                <div class="customer-return-select-wrapper">

                                                                    <i class="bi bi-arrow-left-right"></i>

                                                                    <select name="return_type"
                                                                            class="form-select customer-return-select"
                                                                            required>

                                                                        <option value="refund">
                                                                            Refund
                                                                        </option>

                                                                        <option value="exchange">
                                                                            Exchange
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                            </div>


                                                            <!-- Return Status -->
                                                            <div class="customer-return-form-group">

                                                                <label class="form-label">
                                                                    Return Status
                                                                </label>

                                                                <div class="customer-return-select-wrapper">

                                                                    <i class="bi bi-check2-square"></i>

                                                                    <select name="status"
                                                                            class="form-select customer-return-select"
                                                                            required>

                                                                        <option value="approved">
                                                                            Approved
                                                                        </option>

                                                                        <option value="rejected">
                                                                            Rejected
                                                                        </option>

                                                                    </select>

                                                                </div>

                                                            </div>

                                                        </div>


                                                        <div class="modal-footer customer-return-qc-footer">

                                                            <button type="submit"
                                                                    class="customer-return-submit-btn">

                                                                <i class="bi bi-check-lg"></i>

                                                                Submit QC

                                                            </button>

                                                            <button type="button"
                                                                    class="customer-return-cancel-btn"
                                                                    data-bs-dismiss="modal">

                                                                Cancel

                                                            </button>

                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="9"
                                        class="customer-return-empty-cell">

                                        <div class="customer-return-empty-state">

                                            <div class="customer-return-empty-icon">
                                                <i class="bi bi-arrow-return-left"></i>
                                            </div>

                                            <div class="customer-return-empty-title">
                                                No Return Records Found
                                            </div>

                                            <div class="customer-return-empty-text">
                                                There are no customer return requests available at the moment.
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