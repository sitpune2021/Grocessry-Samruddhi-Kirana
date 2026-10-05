@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   BATCH LIST
========================================================= */

.batch-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 20px 14px;
    background: #ffffff;
    border-bottom: 1px solid #edf1ef;
}

.batch-header-title {
    flex: 1;
}

.batch-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.batch-title-icon {
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

.batch-title-row .card-title {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #212529;
}

.batch-subtitle {
    display: block;
    margin-top: 3px;
    font-size: 13px;
    color: #8a939b;
}

/* Header Buttons */

.batch-header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
}

.batch-header-btn {
    min-height: 40px;
    padding: 8px 14px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    border: 1px solid transparent;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s ease;
}

.batch-btn-icon {
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

/* Add */
.batch-add-btn {
    background: #198754;
    border-color: #198754;
    color: #ffffff;
}

.batch-add-btn:hover {
    background: #157347;
    border-color: #157347;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(25, 135, 84, .18);
}

/* Upload */
.batch-upload-btn {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
}

.batch-upload-btn:hover {
    background: #0b5ed7;
    border-color: #0b5ed7;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(13, 110, 253, .18);
}

/* Download */
.batch-download-btn {
    background: #ffffff;
    border-color: #dfe5e2;
    color: #495057;
}

.batch-download-btn:hover {
    background: #f7faf8;
    border-color: #198754;
    color: #198754;
    transform: translateY(-1px);
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.batch-table-wrapper {
    padding: 16px;
    background: #ffffff;
}

.batch-table-responsive {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e9efeb;
    border-radius: 12px;
    background: #ffffff;
}

.batch-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.batch-table-responsive::-webkit-scrollbar-track {
    background: #f4f6f5;
}

.batch-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd8d3;
    border-radius: 10px;
}

.batch-responsive-table {
    width: 100%;
    min-width: 1050px;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.batch-sr-column {
    width: 75px;
    min-width: 75px;
    max-width: 75px;
}

.batch-product-column {
    width: 220px;
    min-width: 190px;
}

.batch-unit-column {
    width: 145px;
    min-width: 130px;
}

.batch-warehouse-column {
    width: 200px;
    min-width: 180px;
}

.batch-number-column {
    width: 160px;
    min-width: 140px;
}

.batch-qty-column {
    width: 105px;
    min-width: 100px;
}

.batch-date-column {
    width: 145px;
    min-width: 135px;
}

.batch-action-column {
    width: 145px;
    min-width: 135px;
    max-width: 145px;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.batch-responsive-table thead th {
    background: #f7faf8;
    color: #4f5b55;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    padding: 13px 12px;
    border-top: 0;
    border-bottom: 1px solid #e4ebe7;
    border-left: 0;
    border-right: 0;
    white-space: nowrap;
    vertical-align: middle;
}

.batch-responsive-table tbody td {
    padding: 13px 12px;
    border-bottom: 1px solid #edf1ef;
    border-left: 0;
    border-right: 0;
    color: #495057;
    font-size: 13px;
    vertical-align: middle;
}

.batch-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.batch-table-row {
    position: relative;
    transition: background-color .2s ease;
}

.batch-table-row:hover {
    background: #f8fcfa;
}

.batch-table-row td:first-child {
    position: relative;
}

.batch-table-row:hover td:first-child::before {
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

.batch-sr-badge {
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
   PRODUCT
========================================================= */

.batch-product-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.batch-product-icon {
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

.batch-product-name {
    display: block;
    min-width: 0;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #30363b;
    font-weight: 600;
}


/* =========================================================
   UNIT
========================================================= */

.batch-unit-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.batch-unit-icon {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 8px;
    background: #f1f6ff;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.batch-unit-name {
    color: #4b5550;
    font-weight: 500;
}


/* =========================================================
   WAREHOUSE
========================================================= */

.batch-warehouse-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.batch-warehouse-icon {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 8px;
    background: #fff7e8;
    color: #d88900;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.batch-warehouse-name {
    display: block;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #4b5550;
    font-weight: 500;
}


/* =========================================================
   BATCH NUMBER
========================================================= */

.batch-number-badge {
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

.batch-number-badge i {
    font-size: 13px;
}


/* =========================================================
   QUANTITY
========================================================= */

.batch-quantity-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 62px;
    padding: 6px 10px;
    border-radius: 7px;
    background: #f4f6f8;
    color: #495057;
    font-size: 12px;
    font-weight: 700;
}

.batch-quantity-badge i {
    color: #198754;
}


/* =========================================================
   DATES
========================================================= */

.batch-date-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

.batch-date-icon {
    width: 29px;
    height: 29px;
    min-width: 29px;
    border-radius: 7px;
    background: #f1f7ff;
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

.batch-date {
    color: #4b5550;
    font-size: 12px;
    font-weight: 500;
}

.batch-expiry-wrapper .batch-date-icon {
    background: #fff1f1;
    color: #dc3545;
}


/* =========================================================
   ACTIONS
========================================================= */

.batch-action-cell {
    white-space: nowrap;
}

.batch-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.batch-delete-form {
    margin: 0;
    padding: 0;
}

.batch-action-btn {
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

.batch-view-btn {
    background: #eef6ff;
    border-color: #d9eaff;
    color: #0d6efd;
}

.batch-view-btn:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #ffffff;
    transform: translateY(-1px);
}

.batch-edit-btn {
    background: #fff7e8;
    border-color: #ffe7b3;
    color: #d88900;
}

.batch-edit-btn:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #ffffff;
    transform: translateY(-1px);
}

.batch-delete-btn {
    background: #fff1f1;
    border-color: #ffdada;
    color: #dc3545;
}

.batch-delete-btn:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
    transform: translateY(-1px);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.batch-empty-cell {
    padding: 50px 20px !important;
    text-align: center;
}

.batch-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.batch-empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: #eef8f2;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 14px;
}

.batch-empty-title {
    color: #343a40;
    font-size: 16px;
    font-weight: 700;
}

.batch-empty-text {
    margin-top: 5px;
    color: #929aa1;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 992px) {

    .batch-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .batch-header-actions {
        width: 100%;
        justify-content: flex-start;
    }

}

@media (max-width: 768px) {

    .batch-header {
        padding: 15px;
    }

    .batch-title-row {
        align-items: flex-start;
    }

    .batch-title-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        font-size: 18px;
    }

    .batch-title-row .card-title {
        font-size: 18px;
    }

    .batch-header-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 7px;
    }

    .batch-header-btn {
        width: 100%;
    }

    .batch-table-wrapper {
        padding: 10px;
    }

    .batch-responsive-table {
        min-width: 1050px;
    }

}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('batches.view');
            $canEdit = hasPermission('batches.edit');
            $canDelete = hasPermission('batches.delete');
            @endphp

            <!-- Batch Header -->
            <div class="batch-header">
                <div class="batch-header-title">
                    <div class="batch-title-row">
                        <div class="batch-title-icon">
                            <i class="bi bi-boxes"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Batch List
                            </h4>

                            <span class="batch-subtitle">
                                Manage your product batches and inventory details
                            </span>
                        </div>
                    </div>
                </div>

                @if(hasPermission('batches.create'))
                    <div class="batch-header-actions">

                        <a href="{{ route('batches.create') }}"
                        class="batch-header-btn batch-add-btn">
                            <span class="batch-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>
                            <span>Add New Batch</span>
                        </a>

                        <button type="button"
                                class="batch-header-btn batch-upload-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#bulkUploadModal">
                            <span class="batch-btn-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </span>
                            <span>Upload CSV</span>
                        </button>

                        <a class="batch-header-btn batch-download-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#csvModal">
                            <span class="batch-btn-icon">
                                <i class="bi bi-download"></i>
                            </span>
                            <span>Download CSV</span>
                        </a>

                    </div>
                @endif
            </div>

            <x-datatable-search />

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

            @php
            $isSuperAdmin = Auth::user()->role_id == 1;
            @endphp

            <div class="batch-table-wrapper">
                <div class="batch-table-responsive">

                    <table id="batchTable"
                        class="table batch-responsive-table mb-0">

                        <thead>
                            <tr>
                                <th class="text-center batch-sr-column">
                                    Sr. No
                                </th>

                                <th class="batch-product-column">
                                    Product
                                </th>

                                @if($isSuperAdmin)
                                    <th class="batch-unit-column">
                                        Unit
                                    </th>
                                @endif

                                @if($isSuperAdmin)
                                    <th class="batch-warehouse-column">
                                        Warehouse
                                    </th>
                                @endif

                                <th class="batch-number-column">
                                    Batch
                                </th>

                                <th class="text-center batch-qty-column">
                                    Qty
                                </th>

                                <th class="batch-date-column">
                                    MFG
                                </th>

                                <th class="batch-date-column">
                                    Expiry
                                </th>

                                @if($canView || $canEdit || $canDelete)
                                    <th class="text-center batch-action-column">
                                        Actions
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($batches as $batch)

                                <tr class="batch-table-row">

                                    {{-- SR NO --}}
                                    <td class="text-center">
                                        <span class="batch-sr-badge">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>

                                    {{-- PRODUCT --}}
                                    <td>
                                        <div class="batch-product-wrapper">

                                            <div class="batch-product-icon">
                                                <i class="bi bi-box-seam"></i>
                                            </div>

                                            <span class="batch-product-name"
                                                title="{{ $batch->product?->name }}">
                                                {{ $batch->product?->name ?? '-' }}
                                            </span>

                                        </div>
                                    </td>

                                    {{-- UNIT --}}
                                    @if($isSuperAdmin)
                                        <td>
                                            <div class="batch-unit-wrapper">

                                                <div class="batch-unit-icon">
                                                    <i class="bi bi-rulers"></i>
                                                </div>

                                                <span class="batch-unit-name">
                                                    {{ $batch->unit?->name ?? '-' }}
                                                </span>

                                            </div>
                                        </td>
                                    @endif

                                    {{-- WAREHOUSE --}}
                                    @if($isSuperAdmin)
                                        <td>
                                            <div class="batch-warehouse-wrapper">

                                                <div class="batch-warehouse-icon">
                                                    <i class="bi bi-building"></i>
                                                </div>

                                                <span class="batch-warehouse-name"
                                                    title="{{ $batch->warehouse?->name }}">
                                                    {{ $batch->warehouse?->name ?? '-' }}
                                                </span>

                                            </div>
                                        </td>
                                    @endif

                                    {{-- BATCH NUMBER --}}
                                    <td>
                                        <span class="batch-number-badge">
                                            <i class="bi bi-upc-scan"></i>
                                            {{ $batch->batch_no }}
                                        </span>
                                    </td>

                                    {{-- QUANTITY --}}
                                    <td class="text-center">
                                        <span class="batch-quantity-badge">
                                            <i class="bi bi-box-seam"></i>
                                            {{ $batch->quantity }}
                                        </span>
                                    </td>

                                    {{-- MFG DATE --}}
                                    <td>
                                        <div class="batch-date-wrapper">

                                            <span class="batch-date-icon">
                                                <i class="bi bi-calendar3"></i>
                                            </span>

                                            <span class="batch-date">
                                                {{ $batch->mfg_date }}
                                            </span>

                                        </div>
                                    </td>

                                    {{-- EXPIRY DATE --}}
                                    <td>
                                        <div class="batch-date-wrapper batch-expiry-wrapper">

                                            <span class="batch-date-icon">
                                                <i class="bi bi-calendar-x"></i>
                                            </span>

                                            <span class="batch-date">
                                                {{ $batch->expiry_date }}
                                            </span>

                                        </div>
                                    </td>

                                    {{-- ACTIONS --}}
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center batch-action-cell">

                                            <div class="batch-actions">

                                                @if(hasPermission('batches.view') && in_array(Auth::user()->role_id, [1,2]))

                                                    <a href="{{ route('batches.show', $batch->id) }}"
                                                    class="batch-action-btn batch-view-btn"
                                                    title="View Batch">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                @endif

                                                @if(hasPermission('batches.edit'))

                                                    <a href="{{ route('batches.edit', $batch->id) }}"
                                                    class="batch-action-btn batch-edit-btn"
                                                    title="Edit Batch">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                @endif

                                                @if(hasPermission('batches.delete'))

                                                    <form action="{{ route('batches.destroy', $batch->id) }}"
                                                        method="POST"
                                                        class="batch-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete batch?')"
                                                                class="batch-action-btn batch-delete-btn"
                                                                title="Delete Batch">
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
                                    <td colspan="{{ ($isSuperAdmin ? 8 : 6) + (($canView || $canEdit || $canDelete) ? 1 : 0) }}"
                                        class="batch-empty-cell">

                                        <div class="batch-empty-state">

                                            <div class="batch-empty-icon">
                                                <i class="bi bi-boxes"></i>
                                            </div>

                                            <div class="batch-empty-title">
                                                No Batches Found
                                            </div>

                                            <div class="batch-empty-text">
                                                There are no product batches available at the moment.
                                            </div>

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

            {{--<div class="px-3 py-2">
                {{ $batches->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>--}}

        </div>
    </div>

</div>

<div class="modal fade" id="csvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="csvForm"
                method="POST"
                action="{{ route('batches.download.csv') }}">
                @csrf

                <div id="hiddenInputs"></div>

                <div class="modal-header">
                    <h5 class="modal-title">Download CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Warehouse -->
                    <div class="mb-3">
                        <label>Warehouse</label>
                        <select id="warehouse_id" name="warehouse_id" class="form-select">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            This field is required.
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label>Category</label>
                        <select id="category_id" name="category_id" class="form-select">
                            <option value="">Select Category</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select Category
                        </div>
                    </div>

                    <!-- SubCategory Dropdown -->
                    <div class="dropdown mb-3">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                            Select SubCategory
                        </button>

                        <div class="dropdown-menu w-100 p-2 subcategory-scroll"
                            id="subcategoryDropdownMenu">

                            <p class="text-muted mb-2">Select SubCategory</p>

                        </div>

                        <div id="subcatError" class="text-danger mt-1 d-none">
                            Please select at least one SubCategory
                        </div>
                    </div>

                    <!-- Product Dropdown -->
                    <div class="dropdown mb-3">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                            Select Product
                        </button>

                        <div class="dropdown-menu w-100 p-2 product-scroll"
                            id="productDropdownMenu">

                            <p class="text-muted">Select Product</p>

                        </div>
                        <div id="productError" class="text-danger mt-1 d-none">
                            Please select at least one Product
                        </div>
                    </div>

                    <!-- Unit -->
                    <div class="mb-3">
                        <label>Unit</label>
                        <select id="unit_id" name="unit_id" class="form-select">
                            <option value="">Select Unit</option>

                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->short_name }} ({{ $unit->name }})
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please select Unit
                        </div>
                    </div>

                </div>

                <!-- <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Download CSV
                    </button>
                </div> -->

                <div class="modal-footer">
                    <button type="button" id="downloadCsvBtn" class="btn btn-success">
                        Download CSV
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Upload Product Batch CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('product-batches.bulk-upload') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-body">

                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            CSV File <span class="text-danger">*</span>
                        </label>

                        <input type="file"
                            name="csv_file"
                            class="form-control"
                            accept=".csv"
                            required>
                    </div>

                    <div class="alert alert-info">
                        <strong>CSV Format:</strong><br>

                        Warehouse, Category, SubCategory, Product,
                        Unit, Batch No, Quantity, MFG Date, Expiry Date
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        Upload
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<style>
    .subcategory-scroll {
        max-height: 180px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .subcategory-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .subcategory-scroll::-webkit-scrollbar-thumb {
        background: #bdbdbd;
        border-radius: 10px;
    }

    .subcategory-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .product-scroll {
         max-height: 180px; 
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>

@endsection

<!-- table search box script -->
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

<!-- table search box script -->
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- ////////////// -->
<script>
    $(document).ready(function() {

        let selectedSubCategories = [];
        let selectedProducts = [];

        // =========================
        // CATEGORY LOAD
        // =========================
        $('#warehouse_id').on('change', function() {

            let warehouseId = $(this).val();

            $.ajax({
                url: '/ws/categories/' + warehouseId,
                type: 'GET',
                success: function(data) {

                    let options = '<option value="">Select Category</option>';

                    $.each(data, function(i, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });

                    $('#category_id').html(options);
                }
            });
        });

        // =========================
        // SUBCATEGORY LOAD
        // =========================
        $('#category_id').on('change', function() {

            let warehouseId = $('#warehouse_id').val();
            let categoryId = $(this).val();

            $.ajax({
                url: '/ws/subcategories/' + warehouseId + '/' + categoryId,
                type: 'GET',
                success: function(data) {

                    let html = `
                <div class="form-check">
                    <input type="checkbox" id="selectAllSubcat">
                    <label><b>Select All</b></label>
                </div><hr>`;

                    $.each(data, function(i, item) {
                        html += `
                    <label class="d-block">
                        <input type="checkbox" class="subcat" value="${item.id}">
                        ${item.name}
                    </label>`;
                    });

                    $('#subcategoryDropdownMenu').html(html);

                    selectedSubCategories = [];
                    selectedProducts = [];
                    $('#productDropdownMenu').html('Select SubCategory first');
                }
            });
        });

        // =========================
        // SUBCATEGORY SELECT
        // =========================
        $(document).on('change', '.subcat', function() {

            selectedSubCategories = [];

            $('.subcat:checked').each(function() {
                selectedSubCategories.push($(this).val());
            });

            updateHidden('sub_category_id[]', selectedSubCategories);

            loadProducts();
        });

        // =========================
        // SELECT ALL SUBCATEGORY
        // =========================
        $(document).on('change', '#selectAllSubcat', function() {

            $('.subcat').prop('checked', this.checked).trigger('change');
        });

        // =========================
        // LOAD PRODUCTS
        // =========================
        function loadProducts() {

            let warehouseId = $('#warehouse_id').val();

            if (selectedSubCategories.length === 0) return;

            $.ajax({
                url: '/ws/products-by-sub/' + warehouseId + '/' + selectedSubCategories.join(','),
                type: 'GET',
                success: function(data) {

                    let html = `
                <div class="form-check">
                    <input type="checkbox" id="selectAllProducts">
                    <label><b>Select All</b></label>
                </div><hr>`;

                    $.each(data, function(i, item) {
                        html += `
                    <label class="d-block">
                        <input type="checkbox" class="product" value="${item.id}">
                        ${item.name}
                    </label>`;
                    });

                    $('#productDropdownMenu').html(html);

                    selectedProducts = [];
                }
            });
        }

        // =========================
        // PRODUCT SELECT
        // =========================
        $(document).on('change', '.product', function() {

            selectedProducts = [];

            $('.product:checked').each(function() {
                selectedProducts.push($(this).val());
            });
            console.log(selectedProducts);
            updateHidden('product_id[]', selectedProducts);
        });

        // =========================
        // SELECT ALL PRODUCTS
        // =========================
        $(document).on('change', '#selectAllProducts', function() {

            $('.product').prop('checked', this.checked).trigger('change');
        });

        // =========================
        // UPDATE HIDDEN INPUTS
        // =========================
        function updateHidden(name, values) {

            $('#hiddenInputs').find('input[name="' + name + '"]').remove();

            values.forEach(id => {
                $('#hiddenInputs').append(
                    `<input type="hidden" name="${name}" value="${id}">`
                );
            });
        }
    });
</script>

<script>
    $(document).ready(function() {

        $('#downloadCsvBtn').on('click', function() {

            let isValid = true;

            $('.is-invalid').removeClass('is-invalid');
            $('#subcatError').addClass('d-none');
            $('#productError').addClass('d-none');

            if ($('#warehouse_id').val() == '') {
                $('#warehouse_id').addClass('is-invalid');
                isValid = false;
            }

            if ($('#category_id').val() == '') {
                $('#category_id').addClass('is-invalid');
                isValid = false;
            }

            if ($('#unit_id').val() == '') {
                $('#unit_id').addClass('is-invalid');
                isValid = false;
            }

            if ($('.subcat:checked').length === 0) {
                $('#subcatError').removeClass('d-none');
                isValid = false;
            }

            if ($('.product:checked').length === 0) {
                $('#productError').removeClass('d-none');
                isValid = false;
            }

            if (isValid) {

                // Close Modal
                const modalEl = document.getElementById('csvModal');
                const modal = bootstrap.Modal.getInstance(modalEl);

                if (modal) {
                    modal.hide();
                }

                // Submit Form
                $('#csvForm')[0].submit();

                // Reset Form
                setTimeout(function() {
                    $('#csvForm')[0].reset();
                    $('#hiddenInputs').html('');
                }, 500);
            }
        });



        // Remove select validation
        $('#warehouse_id, #category_id, #unit_id').on('change', function() {
            $(this).removeClass('is-invalid');
        });

        // Remove subcategory error
        $(document).on('change', '.subcat', function() {
            if ($('.subcat:checked').length > 0) {
                $('#subcatError').addClass('d-none');
            }
        });

        // Remove product error
        $(document).on('change', '.product', function() {
            if ($('.product:checked').length > 0) {
                $('#productError').addClass('d-none');
            }
        });

    });
</script>