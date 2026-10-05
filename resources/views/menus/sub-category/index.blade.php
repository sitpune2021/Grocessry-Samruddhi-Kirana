@extends('layouts.app')

@section('content')

<style>

/* =========================================================
   CATEGORY / SUB CATEGORY TABLE
========================================================= */

.category-table-wrapper {
    width: 100%;
    margin-top: 20px;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* =========================================================
   RESPONSIVE SCROLL
========================================================= */

.category-table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.category-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.category-table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;
}

.category-table-responsive::-webkit-scrollbar-thumb {
    background: #cbd3d8;
    border-radius: 10px;
}

/* =========================================================
   TABLE
========================================================= */

.category-responsive-table {
    width: 100%;
    min-width: 850px;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

/* =========================================================
   TABLE HEADER
========================================================= */

.category-responsive-table thead th {
    height: 52px;
    padding: 14px 16px !important;

    background: #f7f9f8 !important;
    color: #596168 !important;

    border: none !important;
    border-bottom: 1px solid #e7ebed !important;

    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;

    white-space: nowrap;
    vertical-align: middle;
}

/* =========================================================
   TABLE BODY
========================================================= */

.category-responsive-table tbody td {
    height: 62px;
    padding: 13px 16px !important;

    background: #ffffff;

    border: none !important;
    border-bottom: 1px solid #edf0f2 !important;

    vertical-align: middle !important;

    color: #555e65;
    font-size: 13px;
}

.category-responsive-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* =========================================================
   ROW HOVER
========================================================= */

.category-table-row {
    transition: all .2s ease;
}

.category-table-row:hover td {
    background: #f9fcfa !important;
}

.category-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}

/* =========================================================
   SR NUMBER
========================================================= */

.category-sr-badge {
    width: 34px;
    height: 34px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #edf8f1;
    color: #198754;

    font-size: 12px;
    font-weight: 700;
}

/* =========================================================
   CATEGORY NAME
========================================================= */

.category-main-name {
    display: flex;
    align-items: center;
    gap: 10px;

    color: #212529;
    font-size: 14px;
    font-weight: 650;
}

.category-icon {
    width: 34px;
    height: 34px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

    background: #edf8f1;
    color: #198754;

    font-size: 16px;
}

/* =========================================================
   SUB CATEGORY
========================================================= */

.sub-category-name {
    display: inline-flex;
    align-items: center;

    padding: 6px 10px;

    background: #f5f7f8;
    color: #495057;

    border-radius: 8px;

    font-size: 12px;
    font-weight: 600;

    max-width: 250px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* =========================================================
   SLUG
========================================================= */

.category-slug {
    display: inline-block;

    padding: 6px 10px;

    background: #f8f9fa;
    border: 1px solid #e9ecef;

    color: #6c757d;

    border-radius: 7px;

    font-family: monospace;
    font-size: 12px;

    max-width: 300px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* =========================================================
   ACTIONS
========================================================= */

.category-actions {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    flex-wrap: nowrap;
    white-space: nowrap;
}

.category-delete-form {
    display: inline-flex;
    margin: 0;
    padding: 0;
}

.category-action-btn {
    width: 35px !important;
    height: 35px !important;
    min-width: 35px !important;

    padding: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    border: none !important;
    border-radius: 9px !important;

    font-size: 14px;

    text-decoration: none !important;

    transition: all .2s ease;
}

.category-action-btn:hover {
    transform: translateY(-2px);
}

/* VIEW */

.category-view-btn {
    background: #e9f2ff !important;
    color: #0d6efd !important;
}

.category-view-btn:hover {
    background: #0d6efd !important;
    color: #ffffff !important;
}

/* EDIT */

.category-edit-btn {
    background: #fff3dc !important;
    color: #f59f00 !important;
}

.category-edit-btn:hover {
    background: #f59f00 !important;
    color: #ffffff !important;
}

/* DELETE */

.category-delete-btn {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
}

.category-delete-btn:hover {
    background: #dc3545 !important;
    color: #ffffff !important;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.category-empty {
    padding: 55px 20px !important;
}

.category-empty-icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 17px;

    background: #f1f8f3;
    color: #198754;

    font-size: 27px;
}

.category-empty h6 {
    margin-bottom: 5px;

    color: #343a40;
    font-weight: 700;
}

.category-empty p {
    margin: 0;

    color: #adb5bd;
    font-size: 13px;
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .category-table-wrapper {
        border-radius: 12px;
    }

    .category-responsive-table {
        min-width: 850px;
    }

    .category-responsive-table thead th {
        height: 48px;
        padding: 12px !important;
        font-size: 10px;
    }

    .category-responsive-table tbody td {
        height: 58px;
        padding: 10px 12px !important;
    }

    .category-main-name {
        font-size: 13px;
    }

    .category-icon {
        width: 31px;
        height: 31px;
        font-size: 14px;
    }

    .category-action-btn {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
    }

}

/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 480px) {

    .category-responsive-table {
        min-width: 820px;
    }

    .category-table-responsive::after {
        content: "← Swipe left / right to view more →";

        display: block;

        padding: 8px 10px;

        text-align: center;

        background: #fafbfb;

        border-top: 1px solid #edf0f2;

        color: #8a9299;

        font-size: 10px;
    }

}

</style>

<style>

/* ========================================
   CATEGORY / SUB CATEGORY HEADER
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

    box-sizing: border-box;

}


/* ========================================
   HEADER TITLE
======================================== */

.category-header-title {

    flex: 1;

    min-width: 0;

}


.category-header-title .card-title {

    margin: 0;

    font-size: 21px;

    font-weight: 700;

    line-height: 1.3;

    color: #212529;

}


.category-subtitle {

    display: block;

    margin-top: 4px;

    color: #8a9299;

    font-size: 12px;

    line-height: 1.4;

}


/* ========================================
   HEADER ACTIONS
======================================== */

.category-header-actions {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    flex-shrink: 0;

    flex-wrap: wrap;

}


/* ========================================
   HEADER BUTTON
======================================== */

.header-action-btn {

    height: 40px;

    min-width: 130px;

    padding: 0 15px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 600;

    line-height: 1;

    text-decoration: none;

    white-space: nowrap;

    cursor: pointer;

    transition: all 0.2s ease;

}


/* Button Icon */

.header-action-btn i {

    font-size: 15px;

    line-height: 1;

}


/* ========================================
   ADD BUTTON
======================================== */

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


/* ========================================
   UPLOAD BUTTON
======================================== */

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


/* ========================================
   SAMPLE BUTTON
======================================== */

.btn-sample {

    background: #ffffff;

    color: #495057;

    border: 1px solid #ced4da;

}


.btn-sample:hover {

    background: #f8f9fa;

    color: #212529;

    border-color: #adb5bd;

    transform: translateY(-1px);

}


/* ========================================
   SEARCH AREA
======================================== */

.category-search {

    width: 100%;

    padding: 14px 20px 0;

    box-sizing: border-box;

}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .category-header {

        align-items: flex-start;

        flex-direction: column;

        gap: 14px;

        padding: 17px 18px;

    }


    .category-header-title {

        width: 100%;

    }


    .category-header-actions {

        width: 100%;

        justify-content: flex-start;

        align-items: center;

    }


    .header-action-btn {

        min-width: 125px;

    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .category-header {

        padding: 15px;

        gap: 14px;

    }


    .category-header-title .card-title {

        font-size: 19px;

    }


    .category-subtitle {

        margin-top: 3px;

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

        min-width: 0;

        height: 40px;

        padding: 0 8px;

        font-size: 12px;

    }


    /* Sample Download Full Width */

    .btn-sample {

        grid-column: 1 / -1;

    }


    .category-search {

        padding: 12px 15px 0;

    }

}


/* ========================================
   VERY SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .category-header {

        padding: 13px;

    }


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
   SUB CATEGORY HEADER ICON
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
   HEADER BUTTONS
========================================================= */

.header-action-btn {
    min-height: 40px;
    height: 40px;

    padding: 7px 13px !important;

    border-radius: 9px;

    font-size: 12.5px;
    font-weight: 600;

    box-shadow: 0 3px 9px rgba(0, 0, 0, .04);

    transition: all .2s ease;
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

    background: rgba(255,255,255,.14);
}

.category-btn-icon i {
    font-size: 12px;
}


/* =========================================================
   ADD
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
   UPLOAD
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
   SAMPLE
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
   COLUMN WIDTHS
========================================================= */

.sub-sr-column {
    width: 75px !important;
    min-width: 75px !important;
}

.sub-category-column {
    width: 24% !important;
    min-width: 180px !important;
}

.sub-name-column {
    width: 26% !important;
    min-width: 190px !important;
}

.sub-slug-column {
    width: 30% !important;
    min-width: 220px !important;
}

.sub-action-column {
    width: 160px !important;
    min-width: 160px !important;
    max-width: 160px !important;
}


/* =========================================================
   PARENT CATEGORY
========================================================= */

.parent-category-name {
    display: flex;
    align-items: center;
    gap: 9px;

    min-width: 0;

    color: #212529;
    font-size: 13.5px;
    font-weight: 600;
}

.parent-category-icon {
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

    transition: all .2s ease;
}

.category-table-row:hover .parent-category-icon {
    background: #198754;
    color: #ffffff;
    transform: scale(1.05);
}


/* =========================================================
   SUB CATEGORY NAME
========================================================= */

.sub-category-name {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    max-width: 260px;

    padding: 5px 9px;

    background: #f5f7f8;
    border: 1px solid #e7eaec;

    color: #495057;

    border-radius: 8px;

    font-size: 12px;
    font-weight: 600;

    transition: all .2s ease;
}

.category-table-row:hover .sub-category-name {
    background: #edf8f1;
    border-color: #dceee2;
    color: #198754;
}

.sub-category-icon {
    width: 22px;
    height: 22px;
    min-width: 22px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: #ffffff;

    color: #198754;

    font-size: 11px;
}


/* =========================================================
   SLUG
========================================================= */

.category-slug {
    max-width: 300px;

    padding: 6px 10px;

    background: #f7f9f8;
    border: 1px solid #e4e8ea;

    color: #667078;

    border-radius: 8px;

    font-family: monospace;
    font-size: 11.5px;
    font-weight: 500;

    box-shadow: inset 0 1px 1px rgba(0,0,0,.02);
}


/* =========================================================
   ACTION AREA
========================================================= */

.sub-action-cell {
    white-space: nowrap;
}

.category-actions {
    min-height: 35px;
}

.category-action-btn {
    box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
}

.category-action-btn:hover {
    box-shadow: 0 5px 12px rgba(0, 0, 0, .10);
}


/* =========================================================
   TABLE IMPROVEMENT
========================================================= */

.category-responsive-table thead th {
    font-size: 10.5px;
    letter-spacing: .6px;
}

.category-responsive-table tbody td {
    font-size: 13px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.category-empty-icon {
    border: 1px solid #e3eee7;
    box-shadow: 0 4px 12px rgba(25, 135, 84, .05);
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 768px) {

    .category-title-row {
        gap: 10px;
    }

    .category-title-icon {
        width: 39px;
        height: 39px;
        min-width: 39px;
        font-size: 17px;
    }

    .parent-category-name {
        font-size: 13px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

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
        font-size: 11.5px;
    }

}
</style>

<style>
    /* =========================================================
   PARENT CATEGORY IMAGE
========================================================= */

.parent-category-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    max-width: 32px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;
    border-radius: 9px;

    background: #f1f8f3;
    border: 1px solid #e3eee7;

    flex-shrink: 0;
}

.parent-category-icon .category-img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
    object-position: center;

    border-radius: 8px;
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
                    $canView = hasPermission('sub_category.view');
                    $canEdit = hasPermission('sub_category.edit');
                    $canDelete = hasPermission('sub_category.delete');
                @endphp

                <!-- ========================================
                    SUB CATEGORY HEADER
                ======================================== -->

                <div class="category-header">

                    <div class="category-header-title">

                        <div class="category-title-row">

                            <div class="category-title-icon">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>

                            <div>

                                <h4 class="card-title">
                                    Sub Category
                                </h4>

                                <span class="category-subtitle">
                                    Manage your categories and sub categories
                                </span>

                            </div>

                        </div>

                    </div>

                    @if (hasPermission('sub_category.create'))

                        <div class="category-header-actions">

                            <!-- ADD -->
                            <a href="{{ route('sub-category.create') }}"
                            class="header-action-btn btn-add">

                                <span class="category-btn-icon">
                                    <i class="bi bi-plus-lg"></i>
                                </span>

                                <span>Add Sub Category</span>

                            </a>

                            <!-- UPLOAD -->
                            <button type="button"
                                    class="header-action-btn btn-upload"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bulkUploadModal">

                                <span class="category-btn-icon">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </span>

                                <span>Upload Excel</span>

                            </button>

                            <!-- SAMPLE -->
                            <a href="{{ route('sub-category.sample-excel') }}"
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

                <!-- =====================================================
                    CATEGORY / SUB CATEGORY TABLE
                ===================================================== -->

                <div class="category-table-wrapper mt-4">

                    <div class="category-table-responsive">

                        <table id="batchTable"
                            class="table category-responsive-table">

                            <thead>

                                <tr>

                                    <th class="text-center sub-sr-column">
                                        Sr No
                                    </th>

                                    <th class="sub-category-column">
                                        Category
                                    </th>

                                    <th class="sub-name-column">
                                        Sub Category
                                    </th>

                                    <th class="sub-slug-column">
                                        Slug
                                    </th>

                                    @if ($canView || $canEdit || $canDelete)

                                        <th class="text-center sub-action-column">
                                            Actions
                                        </th>

                                    @endif

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($subCategories as $key => $item)

                                    <tr class="category-table-row">

                                        <!-- =========================
                                            SR NO
                                        ========================== -->

                                        <td class="text-center">

                                            <span class="category-sr-badge">

                                                {{ $key + 1 }}

                                            </span>

                                        </td>

                                        <!-- =========================
                                            CATEGORY
                                        ========================== -->

                                        <td>

                                            <div class="parent-category-name">

                                                <div class="parent-category-icon">
                                                    <img
                                                        src="{{ $item->category->image }}"
                                                        alt="{{ $item->category->name }}"
                                                        class="category-img"
                                                    >
                                                </div>

                                                <span>
                                                    {{ $item->category->name ?? '-' }}
                                                </span>

                                            </div>

                                        </td>

                                        <!-- =========================
                                            SUB CATEGORY
                                        ========================== -->

                                        <td>

                                            <div class="sub-category-name">

                                                <div class="sub-category-icon">
                                                    <i class="bi bi-diagram-3"></i>
                                                </div>

                                                <span>
                                                    {{ $item->name ?? '-' }}
                                                </span>

                                            </div>

                                        </td>

                                        <!-- =========================
                                            SLUG
                                        ========================== -->

                                        <td>

                                            <span class="category-slug">

                                                {{ $item->slug ?? '-' }}

                                            </span>

                                        </td>

                                        <!-- ACTIONS -->

                                        @if ($canView || $canEdit || $canDelete)

                                            <td class="text-center sub-action-cell">

                                                <div class="category-actions">

                                                    @if (hasPermission('sub_category.view'))

                                                        <a href="{{ route('sub-category.show', $item->id) }}"
                                                        class="category-action-btn category-view-btn"
                                                        title="View Sub Category">

                                                            <i class="bi bi-eye"></i>

                                                        </a>

                                                    @endif


                                                    @if (hasPermission('sub_category.edit'))

                                                        <a href="{{ route('sub-category.edit', $item->id) }}"
                                                        class="category-action-btn category-edit-btn"
                                                        title="Edit Sub Category">

                                                            <i class="bi bi-pencil"></i>

                                                        </a>

                                                    @endif


                                                    @if (hasPermission('sub_category.delete'))

                                                        <form action="{{ route('sub-category.destroy', $item->id) }}"
                                                            method="POST"
                                                            class="category-delete-form">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    onclick="return confirm('Delete subcategory?')"
                                                                    class="category-action-btn category-delete-btn"
                                                                    title="Delete Sub Category">

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

                                        <td colspan="{{ ($canView || $canEdit || $canDelete) ? 5 : 4 }}"
                                            class="text-center category-empty">

                                            <div class="category-empty">

                                                <div class="category-empty-icon">

                                                    <i class="bi bi-folder-x"></i>

                                                </div>

                                                <h6>
                                                    No Sub Categories Found
                                                </h6>

                                                <p>
                                                    There are no sub categories available at the moment.
                                                </p>

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
                    {{ $subCategories->onEachSide(0)->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>

    </div>


    <div class="modal fade" id="bulkUploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Bulk Upload Sub Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('sub-category.bulk-upload') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">

                        <div class="alert alert-info">

                            CSV Format
                            Column A → category_name
                            Column B → sub_category_name
                            Column C → slug (optional)

                        </div>

                        <input type="file" name="excel_file" class="form-control" required>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit" class="btn btn-primary">

                            Upload

                        </button>

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
