@extends('layouts.app')

@section('content')

<style>
    /* =====================================================
   BRAND TABLE WRAPPER
===================================================== */

.brand-table-wrapper {
    width: 100%;
    margin-top: 15px;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}


/* =====================================================
   RESPONSIVE SCROLL
===================================================== */

.brand-table-wrapper .brand-table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}


/* =====================================================
   TABLE
===================================================== */

/* =====================================================
   TABLE
===================================================== */

.brand-responsive-table {
    width: 100% !important;
    min-width: 850px;
    margin: 0 !important;

    table-layout: fixed;

    border-collapse: separate !important;
    border-spacing: 0 !important;
}


/* =====================================================
   COLUMN WIDTHS
===================================================== */

.brand-sr-column {
    width: 75px !important;
    min-width: 75px !important;
    max-width: 75px !important;
    text-align: center !important;
}


.brand-logo-column {
    width: 105px !important;
    min-width: 105px !important;
    max-width: 105px !important;
    text-align: center !important;
}


.brand-name-column {
    width: 230px !important;
    min-width: 190px !important;
}


.brand-slug-column {
    width: 230px !important;
    min-width: 180px !important;
}


.brand-status-column {
    width: 100px !important;
    min-width: 100px !important;
    max-width: 100px !important;
    text-align: center !important;
}


.brand-action-column {
    width: 155px !important;
    min-width: 155px !important;
    max-width: 155px !important;
    text-align: center !important;
}

/* =====================================================
   TABLE HEADER
===================================================== */

.brand-responsive-table thead th {
    height: 52px;
    padding: 14px 15px !important;
    background: #f5f8f6 !important;
    color: #495057 !important;
    border: none !important;
    border-bottom: 1px solid #e5e9eb !important;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    white-space: nowrap;
    vertical-align: middle !important;
}


.brand-responsive-table thead th:first-child {
    border-radius: 15px 0 0 0;
}


.brand-responsive-table thead th:last-child {
    border-radius: 0 15px 0 0;
}


/* =====================================================
   TABLE BODY
===================================================== */

.brand-responsive-table tbody td {
    height: 70px;
    padding: 13px 15px !important;
    background: #ffffff;
    border: none !important;
    border-bottom: 1px solid #edf0f2 !important;
    vertical-align: middle !important;
}


.brand-responsive-table tbody tr:last-child td {
    border-bottom: none !important;
}


/* =====================================================
   ROW HOVER
===================================================== */

.brand-table-row {
    transition: all 0.2s ease;
}


.brand-table-row:hover td {
    background: #f8fcf9 !important;
}


.brand-table-row:hover td:first-child {
    box-shadow: inset 4px 0 0 #198754;
}


/* =====================================================
   SR NUMBER
===================================================== */

.brand-sr-badge {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eaf7ef;
    color: #198754;
    font-size: 13px;
    font-weight: 700;
}


/* =====================================================
   LOGO
===================================================== */

.brand-logo-image {
    width: 52px;
    height: 52px;
    object-fit: contain;
    padding: 5px;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 9px;
    display: inline-block;
    box-sizing: border-box;
    transition: all 0.2s ease;
}


.brand-logo-image:hover {
    transform: translateY(-2px);
    border-color: #ced4da;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}


/* =====================================================
   NO LOGO
===================================================== */

.brand-no-logo {
    width: 52px;
    height: 52px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f5f7f8;
    border: 1px dashed #ced4da;
    color: #adb5bd;
    font-size: 20px;
}


/* =====================================================
   BRAND NAME
===================================================== */

.brand-name-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}


.brand-name-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f1f8f3;
    color: #198754;
    border: 1px solid #e3eee7;
    font-size: 14px;
    transition: all 0.2s ease;
}


.brand-table-row:hover .brand-name-icon {
    background: #198754;
    color: #ffffff;
    transform: scale(1.05);
}


.brand-name {
    color: #212529;
    font-size: 13.5px;
    font-weight: 600;
    line-height: 1.4;
    white-space: normal;
    overflow-wrap: break-word;
}


/* =====================================================
   SLUG
===================================================== */

.brand-slug {
    display: inline-block;

    max-width: 100%;

    padding: 5px 9px;

    background: #f7f9f8;

    border: 1px solid #e4e8ea;

    color: #667078;

    border-radius: 8px;

    font-family: monospace;

    font-size: 11.5px;

    font-weight: 500;

    line-height: 1.4;

    white-space: normal;

    overflow-wrap: anywhere;

    word-break: break-word;

    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.02);

    transition: all 0.2s ease;
}


.brand-table-row:hover .brand-slug {
    background: #edf8f1;
    border-color: #dceee2;
    color: #198754;
}


/* =====================================================
   STATUS
   SAME STYLE AS OTHER PAGES
===================================================== */

.brand-status-wrapper {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
}


.brand-status-switch {
    width: 38px !important;
    height: 20px !important;
    margin: 0 !important;
    cursor: pointer;
    box-shadow: none;
}


.brand-status-switch:checked {
    background-color: #198754;
    border-color: #198754;
}


.brand-status-switch:focus {
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.10);
}


/* =====================================================
   ACTIONS
===================================================== */

.brand-action-cell {
    white-space: nowrap;
    text-align: center !important;
}


.brand-actions {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 7px !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    min-height: 35px;
}


.brand-delete-form {
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 0 !important;
}


/* =====================================================
   ACTION BUTTON
===================================================== */

.brand-action-btn {
    width: 35px !important;
    height: 35px !important;
    min-width: 35px !important;
    max-width: 35px !important;
    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    flex: 0 0 35px !important;

    border: none !important;
    border-radius: 8px !important;

    font-size: 14px;
    text-decoration: none !important;

    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);

    transition: all 0.2s ease;
}


.brand-action-btn i {
    font-size: 14px;
}


/* =====================================================
   VIEW - BLUE
===================================================== */

.brand-view-btn {
    background: #e8f1ff !important;
    color: #0d6efd !important;
}


.brand-view-btn:hover {
    background: #0d6efd !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(13, 110, 253, 0.18);
}


/* =====================================================
   EDIT - ORANGE
===================================================== */

.brand-edit-btn {
    background: #fff4df !important;
    color: #f59f00 !important;
}


.brand-edit-btn:hover {
    background: #f59f00 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(245, 159, 0, 0.18);
}


/* =====================================================
   DELETE - RED
===================================================== */

.brand-delete-btn {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
}


.brand-delete-btn:hover {
    background: #dc3545 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(220, 53, 69, 0.18);
}


/* =====================================================
   BRAND HEADER
===================================================== */

.brand-header {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;
    padding: 16px 20px;

    background: #ffffff;
    border-bottom: 1px solid #edf0f2;
}


/* =====================================================
   HEADER TITLE
===================================================== */

.brand-header-title {
    flex: 1;
    min-width: 0;
}


.brand-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}


.brand-title-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: #edf8f1;
    color: #198754;

    border: 1px solid #e1f0e6;

    font-size: 19px;

    transition: all 0.2s ease;
}


.brand-title-row:hover .brand-title-icon {
    background: #198754;
    color: #ffffff;
    transform: scale(1.04);
}


.brand-header-title .card-title {
    margin: 0 !important;
    color: #212529;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
}


.brand-subtitle {
    display: block;
    margin-top: 2px;
    color: #8f969c;
    font-size: 11px;
    font-weight: 400;
    line-height: 1.4;
}


/* =====================================================
   HEADER ACTIONS
===================================================== */

.brand-header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    flex-shrink: 0;
    gap: 8px;
}


/* =====================================================
   HEADER BUTTON
===================================================== */

.brand-header-btn {
    min-height: 40px;
    height: 40px;

    padding: 7px 13px !important;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border-radius: 9px;

    font-size: 12.5px;
    font-weight: 600;

    white-space: nowrap;
    text-decoration: none !important;

    box-shadow: 0 3px 9px rgba(0, 0, 0, 0.04);

    transition: all 0.2s ease;
}


.brand-header-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
}


/* =====================================================
   HEADER BUTTON ICON
===================================================== */

.brand-btn-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: rgba(255, 255, 255, 0.14);
}


.brand-btn-icon i {
    font-size: 12px;
}


/* =====================================================
   ADD BRAND - GREEN
===================================================== */

.brand-add-btn {
    background: #198754 !important;
    border: 1px solid #198754 !important;
    color: #ffffff !important;
}


.brand-add-btn:hover {
    background: #157347 !important;
    border-color: #157347 !important;
    color: #ffffff !important;
}


/* =====================================================
   UPLOAD - BLUE
===================================================== */

.brand-upload-btn {
    background: #0d6efd !important;
    border: 1px solid #0d6efd !important;
    color: #ffffff !important;
}


.brand-upload-btn:hover {
    background: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    color: #ffffff !important;
}


/* =====================================================
   DOWNLOAD - WHITE
===================================================== */

.brand-download-btn {
    background: #ffffff !important;
    border: 1px solid #dce1e4 !important;
    color: #596168 !important;
}


.brand-download-btn:hover {
    background: #f7f9f8 !important;
    border-color: #c8d0d4 !important;
    color: #343a40 !important;
}


/* =====================================================
   EMPTY STATE
===================================================== */

.brand-empty-cell {
    padding: 50px 20px !important;
}


.brand-empty-state {
    color: #6c757d;
    text-align: center;
}


.brand-empty-icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 18px;

    background: #f1f8f3;
    color: #198754;

    border: 1px solid #e3eee7;

    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.05);

    font-size: 27px;
}


.brand-empty-title {
    margin-bottom: 5px;
    color: #343a40;
    font-size: 14px;
    font-weight: 600;
}


.brand-empty-text {
    margin: 0;
    color: #adb5bd;
    font-size: 13px;
}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 768px) {

    .brand-table-wrapper {
        border-radius: 12px;
    }

    .brand-responsive-table {
        min-width: 850px !important;
    }

    .brand-responsive-table thead th {
        height: 48px;
        padding: 12px !important;
        font-size: 10.5px;
    }

    .brand-responsive-table tbody td {
        height: 62px;
        padding: 11px 12px !important;
    }

    .brand-action-column {
        width: 145px !important;
        min-width: 145px !important;
        max-width: 145px !important;
    }

    .brand-actions {
        gap: 6px !important;
    }

    .brand-action-btn {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        max-width: 32px !important;
        flex-basis: 32px !important;
    }

    .brand-name {
        font-size: 13px;
    }

    .brand-slug {
        font-size: 11px;
    }

    .brand-title-row {
        gap: 10px;
    }

    .brand-title-icon {
        width: 39px;
        height: 39px;
        min-width: 39px;
        font-size: 17px;
    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 576px) {

    .brand-header {
        padding: 14px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .brand-header-title {
        width: 100%;
    }

    .brand-title-icon {
        width: 37px;
        height: 37px;
        min-width: 37px;
        border-radius: 9px;
        font-size: 16px;
    }

    .brand-header-title .card-title {
        font-size: 18px;
    }

    .brand-subtitle {
        font-size: 11px;
    }

    .brand-header-actions {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .brand-header-btn {
        width: 100%;
        min-height: 40px;
        height: 40px;
        font-size: 11.5px;
    }

    .brand-download-btn {
        grid-column: 1 / -1;
    }

}


/* =====================================================
   SMALL MOBILE
===================================================== */

@media (max-width: 400px) {

    .brand-title-row {
        gap: 9px;
    }

    .brand-title-icon {
        width: 35px;
        height: 35px;
        min-width: 35px;
        font-size: 15px;
    }

    .brand-header-title .card-title {
        font-size: 17px;
    }

    .brand-header-btn {
        font-size: 11px;
        padding: 7px 9px !important;
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
                $canView = hasPermission('brands.view');
                $canEdit = hasPermission('brands.edit');
                $canDelete = hasPermission('brands.delete');
            @endphp

            <!-- ========================================
                BRAND HEADER
            ======================================== -->

            <div class="brand-header">

                <div class="brand-header-title">

                    <div class="brand-title-row">

                        <div class="brand-title-icon">
                            <i class="bi bi-tags-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Brands
                            </h4>

                            <span class="brand-subtitle">
                                Manage your brands and brand details
                            </span>
                        </div>

                    </div>

                </div>


                @if (hasPermission('brands.create'))

                    <div class="brand-header-actions">

                        <!-- ADD BRAND -->
                        <a href="{{ route('brands.create') }}"
                        class="brand-header-btn brand-add-btn">

                            <span class="brand-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Brand</span>

                        </a>


                        <!-- UPLOAD CSV -->
                        <button type="button"
                                class="brand-header-btn brand-upload-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#bulkUploadModal">

                            <span class="brand-btn-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </span>

                            <span>Upload CSV</span>

                        </button>


                        <!-- DOWNLOAD CSV -->
                        <a href="javascript:void(0);"
                        class="brand-header-btn brand-download-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#csvModal">

                            <span class="brand-btn-icon">
                                <i class="bi bi-download"></i>
                            </span>

                            <span>Download CSV</span>

                        </a>

                    </div>

                @endif

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

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

            <!-- ========================================
                BRAND TABLE
            ======================================== -->

            <div class="brand-table-wrapper">

                <div class="brand-table-responsive">

                    <table id="batchTable"
                        class="table brand-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center brand-sr-column">
                                    Sr No
                                </th>

                                <th class="text-center brand-logo-column">
                                    Logo
                                </th>

                                <th class="brand-name-column">
                                    Brand Name
                                </th>

                                <th class="brand-slug-column">
                                    Slug
                                </th>

                                <th class="text-center brand-status-column">
                                    Status
                                </th>

                                @if ($canView || $canEdit || $canDelete)

                                    <th class="text-center brand-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($brands as $index => $brand)

                                <tr class="brand-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="brand-sr-badge">
                                            {{ $brands->firstItem() + $index }}
                                        </span>

                                    </td>

                                    {{-- Logo --}}
                                    <td class="text-center">

                                        @if ($brand->logo)

                                            <img src="{{ asset('storage/brands/' . $brand->logo) }}"
                                                alt="{{ $brand->name }}"
                                                class="brand-logo-image">

                                        @else

                                            <div class="brand-no-logo">
                                                <i class="bi bi-image"></i>
                                            </div>

                                        @endif

                                    </td>

                                    {{-- Brand Name --}}
                                    <td>

    <div class="brand-name-wrapper">

        <div class="brand-name-icon">
            <i class="bi bi-tag"></i>
        </div>

        <span class="brand-name">
            {{ $brand->name }}
        </span>

    </div>

</td>

                                    {{-- Slug --}}
                                    <td>

                                        <span class="brand-slug">
                                            {{ $brand->slug }}
                                        </span>

                                    </td>

                                    {{-- Status --}}
                                    <td>
    <form action="{{ route('updateStatus') }}" method="POST">
        @csrf

        <input type="hidden"
               name="id"
               value="{{ $brand->id }}">

        <div class="form-check form-switch d-flex justify-content-center">

            <input class="form-check-input"
                   type="checkbox"
                   role="switch"
                   onchange="this.form.submit()"
                   {{ $brand->status ? 'checked' : '' }}>

        </div>
    </form>
</td>
                                    {{-- Actions --}}
                                   @if ($canView || $canEdit || $canDelete)

    <td class="text-center brand-action-cell">

        <div class="brand-actions">

            @if ($canView)

                <a href="{{ route('brands.show', $brand->id) }}"
                   class="brand-action-btn brand-view-btn"
                   title="View Brand">

                    <i class="bi bi-eye"></i>

                </a>

            @endif


            @if ($canEdit)

                <a href="{{ route('brands.edit', $brand->id) }}"
                   class="brand-action-btn brand-edit-btn"
                   title="Edit Brand">

                    <i class="bi bi-pencil"></i>

                </a>

            @endif


            @if ($canDelete)

                <form action="{{ route('brands.destroy', $brand->id) }}"
                      method="POST"
                      class="brand-delete-form">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            onclick="return confirm('Delete brand?')"
                            class="brand-action-btn brand-delete-btn"
                            title="Delete Brand">

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

                                    <td colspan="{{ ($canView || $canEdit || $canDelete) ? 6 : 5 }}"
                                        class="brand-empty-cell">

                                        <div class="brand-empty-state">

                                            <div class="brand-empty-icon">
                                                <i class="bi bi-tags"></i>
                                            </div>

                                            <div class="brand-empty-title">
                                                No Brands Found
                                            </div>

                                            <div class="brand-empty-text">
                                                There are no brands available at the moment.
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
                {{ $brands->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="csvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="csvForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Download CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Category -->
                    <div class="mb-3">
                        <label>Category</label>
                        <select id="category" name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- SubCategory Multi Select Dropdown -->
                    <div class="mb-3">
                        <label>SubCategory</label>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                type="button" id="subDropdown" data-bs-toggle="dropdown">
                                Select SubCategory
                            </button>

                            <div class="dropdown-menu w-100 p-2"
                                style="max-height: 200px; overflow-y: auto;"
                                id="subDropdownMenu">
                                <p class="text-muted">Select SubCategory</p>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="downloadBtn">Download</button>
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
                <h5 class="modal-title">Upload Brands Csv</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('brands.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">CSV File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="excel_file" class="form-control" accept=".csv" required>
                        <!-- <small class="text-muted">Only .xlsx, .xls, .csv allowed. Max 5MB.</small> -->
                    </div>

                    <!-- <div class="alert alert-info py-2 mb-0">
                        <small>
                            <strong>Format:</strong> Category Name | Sub Category Name | Brand Name | Logo URL<br>
                            <a href="{{ route('brands.sample-excel') }}" class="text-decoration-underline">Download
                                Sample</a>
                        </small>
                    </div> -->

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

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

<script>
    const categories = @json($categories);

    // ✅ Load SubCategories as checkbox
    document.getElementById('category').addEventListener('change', function() {

        let cat = categories.find(c => c.id == this.value);
        let dropdown = document.getElementById('subDropdownMenu');
        let btn = document.getElementById('subDropdown');

        dropdown.innerHTML = '';
        btn.innerText = 'Select SubCategory';

        if (cat && cat.sub_categories.length > 0) {

            dropdown.innerHTML += `
            <input type="text" class="form-control mb-2" placeholder="Search..." id="subSearch">

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllSub">
                <label class="form-check-label fw-bold">Select All</label>
            </div>
            <hr>
        `;

            cat.sub_categories.forEach(s => {
                dropdown.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input sub-checkbox"
                        type="checkbox"
                        name="subcategory_id[]"
                        value="${s.id}"
                        id="sub_${s.id}">
                    <label class="form-check-label">${s.name}</label>
                </div>
            `;
            });

        } else {
            dropdown.innerHTML = `<p class="text-danger">No subcategories found</p>`;
        }
    });


    // ✅ Select All + Count
    document.addEventListener('change', function(e) {

        // Select All
        if (e.target.id === 'selectAllSub') {
            let isChecked = e.target.checked;

            let allCheckboxes = document.querySelectorAll('.sub-checkbox');
            let dropdownBtn = document.getElementById('subDropdown');

            // Check / Uncheck all
            allCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });

            // ✅ Update dropdown text
            if (isChecked) {
                dropdownBtn.innerText = "All Selected";
            } else {
                dropdownBtn.innerText = "Select SubCategory";
            }
        }

        // Update count
        if (e.target.classList.contains('sub-checkbox')) {

            let selected = document.querySelectorAll('.sub-checkbox:checked');
            let btn = document.getElementById('subDropdown');

            btn.innerText = selected.length > 0 ?
                selected.length + " selected" :
                "Select SubCategory";
        }
    });


    // ✅ Search filter
    document.addEventListener('keyup', function(e) {
        if (e.target.id === 'subSearch') {

            let value = e.target.value.toLowerCase();

            document.querySelectorAll('#subDropdownMenu .form-check').forEach(div => {
                div.style.display = div.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        }
    });

    // ✅ Download Brand CSV
    document.getElementById('downloadBtn').addEventListener('click', function() {

        let category = document.getElementById('category').value;
        let subcategories = document.querySelectorAll('.sub-checkbox:checked');

        // ✅ Validation
        if (!category || subcategories.length === 0) {
            alert('Please select category and subcategory');
            return;
        }

        let form = document.getElementById('csvForm');
        let formData = new FormData(form);

        // ✅ Disable button
        let btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        btn.innerText = "Downloading...";

        fetch("{{ route('brands.sample-excel') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error("Failed to download");
                }
                return res.blob();
            })
            .then(blob => {

                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = "brand_sample.csv";
                a.click();

                // ✅ Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('csvModal'));
                modal.hide();

                // ✅ Reset form
                form.reset();

                // Reset dropdown text (if custom UI)
                document.getElementById('subDropdown').innerText = "Select SubCategory";
            })
            .catch(err => {
                console.error(err);
                alert("Something went wrong!");
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = "Download";
            });

    });
</script>

@endpush