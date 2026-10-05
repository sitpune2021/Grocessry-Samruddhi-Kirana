@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   STOCK IN WAREHOUSE HEADER
========================================================= */

.stock-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 18px 20px;

    background: #ffffff;

    border-bottom: 1px solid #edf1ee;
}

.stock-title-row {
    display: flex;
    align-items: center;

    gap: 13px;
}

.stock-title-icon {
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

.stock-title-row .card-title {
    margin: 0;

    color: #202a24;

    font-size: 19px;
    font-weight: 700;
}

.stock-subtitle {
    display: block;

    margin-top: 3px;

    color: #7a8580;

    font-size: 12.5px;
}


/* =========================================================
   HEADER BUTTON
========================================================= */

.stock-header-actions {
    display: flex;
    align-items: center;
}

.stock-header-btn {
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

.stock-add-btn {
    color: #ffffff !important;

    background: #198754;

    box-shadow:
        0 4px 12px rgba(25, 135, 84, 0.16);
}

.stock-add-btn:hover {
    color: #ffffff !important;

    background: #157347;

    transform: translateY(-1px);

    box-shadow:
        0 7px 16px rgba(25, 135, 84, 0.22);
}

.stock-btn-icon {
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

.stock-table-wrapper {
    width: 100%;

    margin-top: 14px;

    background: #ffffff;

    border: 1px solid #e9efeb;

    border-radius: 12px;

    overflow: hidden;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.035);
}

.stock-table-responsive {
    width: 100%;

    overflow-x: auto;

    -webkit-overflow-scrolling: touch;
}

.stock-responsive-table {
    width: 100%;

    min-width: 800px;

    margin: 0 !important;

    table-layout: fixed;

    border-collapse: separate;
    border-spacing: 0;
}


/* =========================================================
   COLUMNS
========================================================= */

.stock-sr-column {
    width: 80px;

    min-width: 80px;
    max-width: 80px;
}

.stock-warehouse-column {
    width: 260px;

    min-width: 220px;
}

.stock-category-column {
    width: 200px;

    min-width: 170px;
}

.stock-product-column {
    width: auto;

    min-width: 250px;
}

.stock-quantity-column {
    width: 130px;

    min-width: 120px;
    max-width: 130px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.stock-responsive-table thead th {
    padding: 13px 14px;

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
   NORMAL BODY
========================================================= */

.stock-responsive-table tbody td {
    padding: 12px 14px;

    border-top: 0;

    border-bottom: 1px solid #edf1ee;

    color: #414a45;

    font-size: 13px;

    vertical-align: middle;
}

.stock-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   WAREHOUSE GROUP ROW
========================================================= */

.stock-warehouse-row {
    position: relative;

    background: #f6faf7;

    transition:
        background 0.18s ease,
        box-shadow 0.18s ease;
}

.stock-warehouse-row:hover {
    background: #eef8f2;

    box-shadow:
        inset 3px 0 0 #198754;
}

.stock-warehouse-row td {
    padding-top: 13px !important;
    padding-bottom: 13px !important;

    border-bottom: 1px solid #dfeae3 !important;
}


/* =========================================================
   WAREHOUSE CONTENT
========================================================= */

.stock-warehouse-wrapper {
    display: flex;
    align-items: center;

    gap: 10px;
}

.stock-expand-icon {
    width: 25px;
    height: 25px;
    flex: 0 0 25px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: #ffffff;

    border: 1px solid #dce8e0;

    color: #198754;

    font-size: 12px;

    transition: transform 0.2s ease;
}

.stock-warehouse-icon {
    width: 35px;
    height: 35px;
    flex: 0 0 35px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #e7f5ed;

    color: #198754;

    font-size: 16px;
}

.stock-warehouse-name {
    color: #27332c;

    font-size: 13.5px;
    font-weight: 700;
}

.stock-warehouse-label {
    display: flex;
    align-items: center;

    gap: 5px;

    margin-top: 2px;

    color: #8a948f;

    font-size: 10.5px;
}

.stock-warehouse-label i {
    color: #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.stock-sr-badge {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 27px;

    padding: 0 8px;

    border-radius: 7px;

    background: #e9f7ef;

    color: #198754;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   CHILD ROWS
========================================================= */

.stock-child-row {
    background: #ffffff;

    transition: background 0.18s ease;
}

.stock-child-row:hover {
    background: #fbfefc;
}

.stock-child-row td {
    padding-top: 11px !important;
    padding-bottom: 11px !important;
}


/* =========================================================
   CHILD WAREHOUSE
========================================================= */

.stock-child-warehouse {
    display: inline-flex;

    padding-left: 12px;

    color: #7b8580;

    font-size: 12px;
}


/* =========================================================
   CATEGORY
========================================================= */

.stock-category-wrapper {
    display: flex;
    align-items: center;

    gap: 8px;

    min-width: 0;
}

.stock-category-icon {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 7px;

    background: #f2f6ff;

    color: #3977c8;

    font-size: 13px;
}

.stock-category-wrapper span {
    color: #4b5750;

    font-weight: 600;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* =========================================================
   PRODUCT
========================================================= */

.stock-product-wrapper {
    display: flex;
    align-items: center;

    gap: 9px;

    min-width: 0;
}

.stock-product-icon {
    width: 31px;
    height: 31px;
    flex: 0 0 31px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef8f2;

    color: #198754;

    font-size: 13px;
}

.stock-product-name {
    min-width: 0;

    color: #354039;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* =========================================================
   QUANTITY
========================================================= */

.stock-quantity-badge {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 6px;

    min-width: 70px;

    padding: 7px 10px;

    border-radius: 8px;

    background: #eef8f2;

    color: #198754;

    font-size: 12px;
    font-weight: 700;
}

.stock-quantity-badge i {
    font-size: 13px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.stock-empty-cell {
    padding: 45px 20px !important;

    text-align: center;
}

.stock-empty-state {
    display: flex;

    align-items: center;
    justify-content: center;

    flex-direction: column;
}

.stock-empty-icon {
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

.stock-empty-title {
    color: #39443e;

    font-size: 14px;
    font-weight: 700;
}

.stock-empty-text {
    margin-top: 4px;

    color: #89938e;

    font-size: 12px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    .stock-header {
        align-items: flex-start;

        flex-direction: column;

        padding: 16px;
    }

    .stock-header-actions {
        width: 100%;
    }

    .stock-header-btn {
        width: 100%;
    }

    .stock-title-row {
        align-items: flex-start;
    }

    .stock-title-icon {
        width: 40px;
        height: 40px;

        flex-basis: 40px;

        font-size: 18px;
    }

    .stock-title-row .card-title {
        font-size: 17px;
    }

    .stock-subtitle {
        font-size: 11.5px;
    }

    .stock-table-wrapper {
        border-radius: 10px;
    }

    .stock-responsive-table {
        min-width: 800px;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card p-2">
            <div class="card-datatable text-nowrap">
                @php
                    $canView = hasPermission('stock.view');
                    $canEdit = hasPermission('stock.edit');
                    $canDelete = hasPermission('stock.delete');
                @endphp

                <!-- Header -->
                <div class="stock-header">

                    <div class="stock-header-title">

                        <div class="stock-title-row">

                            <div class="stock-title-icon">
                                <i class="bi bi-box-arrow-in-down"></i>
                            </div>

                            <div>
                                <h4 class="card-title">
                                    Stock In Warehouse
                                </h4>

                                <span class="stock-subtitle">
                                    Manage warehouse stock and inventory quantities
                                </span>
                            </div>

                        </div>

                    </div>


                    @if (hasPermission('stock.create'))

                        <div class="stock-header-actions">

                            <a href="{{ route('warehouse.addStockForm') }}"
                            class="stock-header-btn stock-add-btn">

                                <span class="stock-btn-icon">
                                    <i class="bi bi-plus-lg"></i>
                                </span>

                                <span>Stock In Warehouse</span>

                            </a>

                        </div>

                    @endif

                </div>

                <!-- Search + Warehouse Filter -->
                <x-datatable-search />

                @if (session('success'))
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

                <div class=" justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-4">

                    @if (Auth::user()->role_id == 1)
                        <form method="GET" action="{{ route('index.addStock.warehouse') }}">
                            <!-- <label class="form-label mb-1">Select Warehouse</label> -->
                            <select name="warehouse_id" class="form-select" onchange="this.form.submit()"
                                style="min-width:220px">
                                <option value="">-- All Warehouses --</option>
                                @foreach ($warehouses as $w)
                                    <option value="{{ $w->id }}"
                                        {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                </div>

                <!-- Table -->
                <div class="stock-table-wrapper">

                    <div class="stock-table-responsive">

                        <table class="table stock-responsive-table mb-0">

                            <thead>

                                <tr>

                                    <th class="text-center stock-sr-column">
                                        SR NO
                                    </th>

                                    <th class="stock-warehouse-column">
                                        Warehouse
                                    </th>

                                    <th class="stock-category-column">
                                        Category
                                    </th>

                                    <th class="stock-product-column">
                                        Product
                                    </th>

                                    <th class="text-center stock-quantity-column">
                                        Quantity
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @php
                                    $sr = 1;
                                @endphp


                                @forelse ($stocks as $warehouseId => $warehouseStocks)

                                    {{-- =================================================
                                        WAREHOUSE HEADER ROW
                                    ================================================== --}}
                                    <tr class="stock-warehouse-row"
                                        onclick="toggleWarehouse({{ $warehouseId }})"
                                        style="cursor:pointer">

                                        <td class="text-center">

                                            <span class="stock-sr-badge">
                                                {{ $sr++ }}
                                            </span>

                                        </td>


                                        <td colspan="4">

                                            <div class="stock-warehouse-wrapper">

                                                <div class="stock-expand-icon"
                                                    id="icon-{{ $warehouseId }}">

                                                    <i class="bi bi-chevron-right"></i>

                                                </div>

                                                <div class="stock-warehouse-icon">

                                                    <i class="bi bi-building"></i>

                                                </div>

                                                <div>

                                                    <div class="stock-warehouse-name">

                                                        {{ $warehouseStocks->first()->warehouse->name }}

                                                    </div>

                                                    <div class="stock-warehouse-label">

                                                        <i class="bi bi-box-seam"></i>

                                                        Click to view warehouse stock

                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>


                                    {{-- =================================================
                                        CHILD STOCK ROWS
                                    ================================================== --}}
                                    @foreach ($warehouseStocks as $stock)

                                        <tr class="stock-child-row warehouse-child warehouse-{{ $warehouseId }}"
                                            style="display:none">

                                            <td></td>

                                            <td>

                                                <span class="stock-child-warehouse">
                                                    {{ $stock->warehouse->name ?? '' }}
                                                </span>

                                            </td>


                                            <td>

                                                <div class="stock-category-wrapper">

                                                    <div class="stock-category-icon">
                                                        <i class="bi bi-grid"></i>
                                                    </div>

                                                    <span>
                                                        {{ $stock->category->name ?? '' }}
                                                    </span>

                                                </div>

                                            </td>


                                            <td>

                                                <div class="stock-product-wrapper">

                                                    <div class="stock-product-icon">
                                                        <i class="bi bi-box"></i>
                                                    </div>

                                                    <span class="stock-product-name"
                                                        title="{{ $stock->product->name ?? '' }}">

                                                        {{ $stock->product->name ?? '' }}

                                                    </span>

                                                </div>

                                            </td>


                                            <td class="text-center">

                                                <span class="stock-quantity-badge">

                                                    <i class="bi bi-box-seam"></i>

                                                    {{ $stock->quantity }}

                                                </span>

                                            </td>

                                        </tr>

                                    @endforeach


                                @empty

                                    <tr>

                                        <td colspan="5"
                                            class="stock-empty-cell">

                                            <div class="stock-empty-state">

                                                <div class="stock-empty-icon">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>

                                                <div class="stock-empty-title">
                                                    No Stock Found
                                                </div>

                                                <div class="stock-empty-text">
                                                    There is no warehouse stock available at the moment.
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


<!-- table search box script -->
@push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const searchInput = document.getElementById("dt-search-1");
            const table = document.getElementById("stock");

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
    <script>
        function toggleWarehouse(id) {

            const rows = document.querySelectorAll('.warehouse-' + id);
            const icon = document.getElementById('icon-' + id);

            let isHidden = rows[0].style.display === 'none';

            rows.forEach(row => {
                row.style.display = isHidden ? 'table-row' : 'none';
            });

            icon.classList.toggle('bx-chevron-right');
            icon.classList.toggle('bx-chevron-down');
        }
    </script>
@endpush
