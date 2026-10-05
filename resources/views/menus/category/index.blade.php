@extends('layouts.app')

@section('content')

<!-- =========================
    CATEGORY TABLE CSS
========================= -->

<style>

/* ========================================
MAIN TABLE WRAPPER
======================================== */

.category-table-wrapper {

    width: 100%;

    background: #ffffff;

    border: 1px solid #e9ecef;

    border-radius: 16px;

    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);

    overflow-x: auto;

    overflow-y: hidden;

}


/* ========================================
TABLE
======================================== */

.category-table {

    width: 100% !important;

    min-width: 850px;

    margin: 0 !important;

    border-collapse: separate;

    border-spacing: 0;

}


/* ========================================
COLUMN WIDTHS
======================================== */

.category-table .sr-column {

    width: 80px !important;

    min-width: 80px !important;

}

.category-table .image-column {

    width: 100px !important;

    min-width: 100px !important;

}

.category-table .name-column {

    width: 28% !important;

    min-width: 180px !important;

}

.category-table .slug-column {

    width: 32% !important;

    min-width: 200px !important;

}

.category-table .actions-column {

    width: 180px !important;

    min-width: 180px !important;

    max-width: 180px !important;

    white-space: nowrap !important;

}


/* ========================================
TABLE HEADER
======================================== */

.category-table thead th {

    background: #f5f8f6;

    color: #495057;

    font-size: 12px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 0.5px;

    padding: 17px 15px;

    border: none !important;

    white-space: nowrap;

}


.category-table thead th:first-child {

    border-radius: 15px 0 0 0;

}


.category-table thead th:last-child {

    border-radius: 0 15px 0 0;

}


/* ========================================
TABLE BODY
======================================== */

.category-table tbody td {

    padding: 15px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2 !important;

    border-top: none !important;

    vertical-align: middle;

}


/* Last Row */

.category-table tbody tr:last-child td {

    border-bottom: none !important;

}


/* ========================================
ROW HOVER
======================================== */

.category-row {

    transition: all 0.25s ease;

}


.category-row:hover td {

    background: #f8fcf9;

}


.category-row:hover td:first-child {

    box-shadow: inset 4px 0 0 #198754;

}


/* ========================================
SR NUMBER
======================================== */

.sr-badge {

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


/* ========================================
CATEGORY IMAGE
======================================== */

.category-img-box {

    width: 58px;

    height: 58px;

    border-radius: 12px;

    overflow: hidden;

    background: #f5f7f8;

    border: 1px solid #e5e9eb;

    display: flex;

    align-items: center;

    justify-content: center;

}


.category-img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition: transform 0.3s ease;

}


.category-row:hover .category-img {

    transform: scale(1.08);

}


/* No Image */

.no-image {

    color: #adb5bd;

    font-size: 22px;

}


/* ========================================
CATEGORY NAME
======================================== */

.category-name {

    color: #212529;

    font-size: 15px;

    font-weight: 600;

}


/* ========================================
SLUG
======================================== */

.category-slug {

    display: inline-block;

    max-width: 100%;

    padding: 6px 11px;

    background: #f8f9fa;

    border: 1px solid #e5e7e9;

    border-radius: 8px;

    color: #6c757d;

    font-size: 13px;

    word-break: break-word;

}


/* ========================================
ACTIONS
======================================== */

.category-actions {

    display: flex !important;

    align-items: center !important;

    justify-content: center !important;

    gap: 8px !important;

    flex-wrap: nowrap !important;

    white-space: nowrap !important;

}


.delete-form {

    display: inline-flex !important;

    margin: 0 !important;

    padding: 0 !important;

}


/* ========================================
ACTION BUTTON
======================================== */

.action-btn {

    width: 36px !important;

    min-width: 36px !important;

    max-width: 36px !important;

    height: 36px !important;

    min-height: 36px !important;

    padding: 0 !important;

    border: none !important;

    border-radius: 9px !important;

    display: inline-flex !important;

    align-items: center !important;

    justify-content: center !important;

    flex: 0 0 36px !important;

    font-size: 14px;

    text-decoration: none !important;

    transition: all 0.2s ease;

    cursor: pointer;

}


/* ========================================
VIEW BUTTON
======================================== */

.action-view {

    background: #e8f1ff !important;

    color: #0d6efd !important;

}


.action-view:hover {

    background: #0d6efd !important;

    color: #ffffff !important;

    transform: translateY(-2px);

}


/* ========================================
EDIT BUTTON
======================================== */

.action-edit {

    background: #fff4df !important;

    color: #f59f00 !important;

}


.action-edit:hover {

    background: #f59f00 !important;

    color: #ffffff !important;

    transform: translateY(-2px);

}


/* ========================================
DELETE BUTTON
======================================== */

.action-delete {

    background: #ffe9e9 !important;

    color: #dc3545 !important;

}


.action-delete:hover {

    background: #dc3545 !important;

    color: #ffffff !important;

    transform: translateY(-2px);

}


/* ========================================
EMPTY STATE
======================================== */

.empty-cell {

    padding: 60px 20px !important;

}


.empty-category {

    color: #6c757d;

}


.empty-icon {

    width: 65px;

    height: 65px;

    margin: 0 auto 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 18px;

    background: #f1f8f3;

    color: #198754;

    font-size: 28px;

}


.empty-category h6 {

    margin-bottom: 5px;

    color: #343a40;

    font-weight: 600;

}


.empty-category p {

    margin: 0;

    font-size: 13px;

    color: #adb5bd;

}


/* ========================================
MOBILE RESPONSIVE
======================================== */

@media (max-width: 768px) {

    .category-table-wrapper {

        border-radius: 12px;

    }


    .category-table {

        min-width: 850px !important;

    }


    .category-table thead th {

        padding: 13px 12px;

        font-size: 11px;

    }


    .category-table tbody td {

        padding: 12px;

    }


    .category-img-box {

        width: 48px;

        height: 48px;

        border-radius: 10px;

    }


    .category-name {

        font-size: 14px;

    }


    .category-slug {

        font-size: 12px;

        padding: 5px 8px;

    }


    .category-table .actions-column {

        width: 160px !important;

        min-width: 160px !important;

        max-width: 160px !important;

    }


    .category-actions {

        gap: 6px !important;

    }


    .action-btn {

        width: 32px !important;

        min-width: 32px !important;

        max-width: 32px !important;

        height: 32px !important;

        min-height: 32px !important;

        flex-basis: 32px !important;

    }

}


/* ========================================
SMALL MOBILE
======================================== */

@media (max-width: 480px) {

    .category-table {

        min-width: 800px !important;

    }

}

</style>

<style>

/* ========================================
   CATEGORY HEADER
======================================== */

.category-header {

    width: 100%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    padding: 18px 20px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2;

}


/* ========================================
   TITLE
======================================== */

.category-header-title {

    flex: 1;

    min-width: 180px;

}


.category-header-title .card-title {

    font-size: 21px;

    font-weight: 700;

    color: #212529;

}


.category-subtitle {

    display: block;

    margin-top: 3px;

    color: #8a9299;

    font-size: 12px;

}


/* ========================================
   HEADER ACTIONS
======================================== */

.category-header-actions {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    flex-wrap: wrap;

}


/* ========================================
   BUTTONS
======================================== */

.header-action-btn {

    height: 38px;

    padding: 0 14px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 500;

    text-decoration: none;

    white-space: nowrap;

    transition: all 0.2s ease;

}


.header-action-btn i {

    font-size: 14px;

}


/* Add */

.btn-add {

    background: #198754;

    color: #ffffff;

    border: 1px solid #198754;

}


.btn-add:hover {

    background: #157347;

    border-color: #157347;

    color: #ffffff;

    transform: translateY(-1px);

}


/* Upload */

.btn-upload {

    background: #0d6efd;

    color: #ffffff;

    border: 1px solid #0d6efd;

}


.btn-upload:hover {

    background: #0b5ed7;

    border-color: #0b5ed7;

    color: #ffffff;

    transform: translateY(-1px);

}


/* Sample */

.btn-sample {

    background: #ffffff;

    color: #6c757d;

    border: 1px solid #ced4da;

}


.btn-sample:hover {

    background: #f8f9fa;

    color: #343a40;

    border-color: #adb5bd;

}


/* ========================================
   SEARCH
======================================== */

.category-search {

    padding: 14px 20px 0;

}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .category-header {

        align-items: flex-start;

        flex-direction: column;

        gap: 14px;

    }


    .category-header-title {

        width: 100%;

    }


    .category-header-actions {

        width: 100%;

        justify-content: flex-start;

    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .category-header {

        padding: 15px;

        gap: 15px;

    }


    .category-header-title .card-title {

        font-size: 19px;

    }


    .category-subtitle {

        font-size: 11px;

    }


    .category-header-actions {

        display: grid;

        grid-template-columns: 1fr 1fr;

        width: 100%;

        gap: 8px;

    }


    .header-action-btn {

        width: 100%;

        height: 38px;

        padding: 0 8px;

        font-size: 12px;

    }


    /* Sample Download full width */

    .btn-sample {

        grid-column: 1 / -1;

    }


    .category-search {

        padding: 12px 15px 0;

    }

}


/* ========================================
   VERY SMALL SCREEN
======================================== */

@media (max-width: 360px) {

    .category-header-actions {

        grid-template-columns: 1fr;

    }


    .btn-sample {

        grid-column: auto;

    }


    .header-action-btn {

        width: 100%;

    }

}

/* =========================================================
   CATEGORY HEADER TITLE
========================================================= */

.category-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.category-title-icon {
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
    transition: all .2s ease;
}

.category-title-row:hover .category-title-icon {
    background: #198754;
    color: #ffffff;
    transform: scale(1.04);
}

.category-header-title .card-title {
    margin: 0 !important;
    color: #212529;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
}


/* =========================================================
   HEADER BUTTON IMPROVEMENT
========================================================= */

.header-action-btn {
    min-height: 40px;
    height: 40px;
    padding: 7px 13px !important;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 600;
    box-shadow: 0 3px 9px rgba(0, 0, 0, .04);
}

.header-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, .08);
}

.category-btn-icon {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: rgba(255, 255, 255, .14);
}

.category-btn-icon i {
    font-size: 12px;
}


/* =========================================================
   ADD BUTTON
========================================================= */

.btn-add {
    background: #198754 !important;
    border-color: #198754 !important;
    color: #ffffff !important;
}

.btn-add:hover {
    background: #157347 !important;
    border-color: #157347 !important;
    color: #ffffff !important;
}


/* =========================================================
   UPLOAD BUTTON
========================================================= */

.btn-upload {
    background: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #ffffff !important;
}

.btn-upload:hover {
    background: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    color: #ffffff !important;
}


/* =========================================================
   SAMPLE BUTTON
========================================================= */

.btn-sample {
    background: #ffffff !important;
    border: 1px solid #dce1e4 !important;
    color: #596168 !important;
}

.btn-sample:hover {
    background: #f7f9f8 !important;
    border-color: #c8d0d4 !important;
    color: #343a40 !important;
}


/* =========================================================
   CATEGORY NAME
========================================================= */

.category-name-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.category-name-icon {
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
    font-size: 13px;
    transition: all .2s ease;
}

.category-row:hover .category-name-icon {
    background: #198754;
    color: #ffffff;
    transform: scale(1.05);
}

.category-name {
    color: #212529;
    font-size: 14px;
    font-weight: 650;
    line-height: 1.3;
}


/* =========================================================
   CATEGORY IMAGE
========================================================= */

.category-img-box {
    box-shadow: 0 2px 7px rgba(0, 0, 0, .04);
    transition: all .2s ease;
}

.category-row:hover .category-img-box {
    border-color: #d8e9de;
    box-shadow: 0 5px 12px rgba(25, 135, 84, .10);
}


/* =========================================================
   SLUG
========================================================= */

.category-slug {
    background: #f7f9f8;
    border-color: #e4e8ea;
    color: #667078;
    font-size: 12px;
    font-weight: 500;
}


/* =========================================================
   ACTION CELL
========================================================= */

.category-action-cell {
    white-space: nowrap;
}

.category-actions {
    min-height: 36px;
}


/* =========================================================
   ACTION BUTTON SHADOW
========================================================= */

.action-btn {
    box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
}

.action-btn:hover {
    box-shadow: 0 5px 12px rgba(0, 0, 0, .10);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.empty-icon {
    border: 1px solid #e3eee7;
    box-shadow: 0 4px 12px rgba(25, 135, 84, .05);
}

.empty-category h6 {
    font-weight: 700;
}


/* =========================================================
   TABLE HEADER
========================================================= */

.category-table thead th {
    font-size: 10.5px;
    letter-spacing: .6px;
    font-weight: 700;
}


/* =========================================================
   TABLE BODY
========================================================= */

.category-table tbody td {
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE HEADER
========================================================= */

@media (max-width: 991px) {

    .category-title-row {
        gap: 10px;
    }

    .category-title-icon {
        width: 39px;
        height: 39px;
        min-width: 39px;
        font-size: 17px;
    }

}


@media (max-width: 576px) {

    .category-title-icon {
        width: 37px;
        height: 37px;
        min-width: 37px;
        border-radius: 9px;
        font-size: 16px;
    }

    .category-header-title .card-title {
        font-size: 18px;
    }

    .category-subtitle {
        font-size: 11px;
    }

    .header-action-btn {
        min-height: 39px;
        height: 39px;
        font-size: 11.5px;
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
            @php
                $canView = hasPermission('category.view');
                $canEdit = hasPermission('category.edit');
                $canDelete = hasPermission('category.delete');
            @endphp

            <!-- Header -->
            <div class="category-header">

                <div class="category-header-title">

                    <div class="category-title-row">

                        <div class="category-title-icon">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Category
                            </h4>

                            <span class="category-subtitle">
                                Manage your grocery categories
                            </span>
                        </div>

                    </div>

                </div>

                @if (hasPermission('category.create'))

                    <div class="category-header-actions">

                        <!-- Add Category -->
                        <a href="{{ route('category.create') }}"
                        class="header-action-btn btn-add">

                            <span class="category-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Category</span>

                        </a>


                        <!-- Upload Excel -->
                        <button type="button"
                                class="header-action-btn btn-upload"
                                data-bs-toggle="modal"
                                data-bs-target="#bulkUploadModal">

                            <span class="category-btn-icon">
                                <i class="bi bi-file-earmark-excel"></i>
                            </span>

                            <span>Upload Excel</span>

                        </button>


                        <!-- Sample Download -->
                        <a href="{{ route('category.sample-excel') }}"
                        class="header-action-btn btn-sample">

                            <span class="category-btn-icon">
                                <i class="bi bi-download"></i>
                            </span>

                            <span>Sample Download</span>

                        </a>

                    </div>

                @endif

            </div>

            <!-- Search -->
            <div class="category-search">
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

            <!-- =========================
                CATEGORY TABLE
            ========================= -->

            <div class="table-responsive mt-5 category-table-wrapper">

                <table id="batchTable"
                    class="table category-table align-middle w-100 mb-0">

                    <!-- TABLE HEADER -->
                    <thead>
                        <tr>

                            <th class="text-center sr-column">
                                Sr No
                            </th>

                            <th class="image-column">
                                Image
                            </th>

                            <th class="name-column">
                                Category Name
                            </th>

                            <th class="slug-column">
                                Slug
                            </th>

                            @if ($canView || $canEdit || $canDelete)
                                <th class="text-center actions-column">
                                    Actions
                                </th>
                            @endif

                        </tr>
                    </thead>

                    <!-- TABLE BODY -->
                    <tbody>

                        @forelse ($categories as $index => $category)

                            <tr class="category-row">

                                <!-- Sr No -->
                                <td class="text-center">

                                    <span class="sr-badge">
                                        {{ $categories->firstItem() + $index }}
                                    </span>

                                </td>

                                <!-- Category Image -->
                                <td>

                                    @if ($category->category_image_url)

                                        <div class="category-img-box">

                                            <img
                                                src="{{ $category->category_image_url }}"
                                                alt="{{ $category->name }}"
                                                class="category-img"
                                            >

                                        </div>

                                    @else

                                        <div class="category-img-box no-image">
                                            <i class="bi bi-image"></i>
                                        </div>

                                    @endif

                                </td>

                                <!-- Category Name -->
                                <td>

                                    <div class="category-name">
                                        {{ $category->name }}
                                    </div>

                                </td>

                                <!-- Slug -->
                                <td>

                                    <span class="category-slug">
                                        {{ $category->slug }}
                                    </span>

                                </td>

                                @if ($canView || $canEdit || $canDelete)

                                    <td class="text-center category-action-cell">

                                        <div class="category-actions">

                                            @if (hasPermission('category.view'))

                                                <a href="{{ route('category.show', $category->id) }}"
                                                    class="action-btn action-view"
                                                    title="View Category">

                                                    <i class="bi bi-eye"></i>

                                                </a>

                                            @endif


                                            @if (hasPermission('category.edit'))

                                                <a href="{{ route('category.edit', $category->id) }}"
                                                    class="action-btn action-edit"
                                                    title="Edit Category">

                                                    <i class="bi bi-pencil"></i>

                                                </a>

                                            @endif


                                            @if (hasPermission('category.delete'))

                                                <form action="{{ route('category.destroy', $category->id) }}"
                                                        method="POST"
                                                        class="delete-form">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            onclick="return confirm('Delete category?')"
                                                            class="action-btn action-delete"
                                                            title="Delete Category">

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

                                <td
                                    colspan="{{ ($canView || $canEdit || $canDelete) ? 5 : 4 }}"
                                    class="text-center empty-cell">

                                    <div class="empty-category">

                                        <div class="empty-icon">
                                            <i class="bi bi-grid-3x3-gap"></i>
                                        </div>

                                        <h6>
                                            No categories found
                                        </h6>

                                        <p>
                                            No grocery categories are available.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $categories->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>

</div>


<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload Categories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('category.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>CSV Format:</strong><br>
                        Column A: <code>name</code> &nbsp;|&nbsp;
                        Column B: <code>slug</code> (optional) &nbsp;|&nbsp;
                        Column C: <code>image_url</code> (optional)<br>
                        <small class="text-muted">Image URL = internet - direct image link</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Select <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Allowed: .xlsx, .xls, .csv — Max: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload & Import</button>
                </div>
            </form>
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
@endpush