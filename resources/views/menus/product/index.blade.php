@extends('layouts.app')

@section('content')

<style>

/* ========================================
   PRODUCT TABLE WRAPPER
======================================== */

.product-table-wrapper {

    width: 100%;

    margin-top: 20px;

    background: #ffffff;

    border: 1px solid #e9ecef;

    border-radius: 16px;

    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);

    overflow: hidden;

}


/* ========================================
   RESPONSIVE TABLE
======================================== */

.product-table-responsive {

    width: 100%;

    overflow-x: auto;

    overflow-y: hidden;

    -webkit-overflow-scrolling: touch;

    scrollbar-width: thin;

}


/* Scrollbar */

.product-table-responsive::-webkit-scrollbar {

    height: 7px;

}

.product-table-responsive::-webkit-scrollbar-track {

    background: #f1f3f5;

}

.product-table-responsive::-webkit-scrollbar-thumb {

    background: #cbd3d8;

    border-radius: 10px;

}


/* ========================================
   TABLE
======================================== */

.product-responsive-table {

    width: 100%;

    min-width: 1250px;

    margin: 0 !important;

    border-collapse: separate !important;

    border-spacing: 0 !important;

}


/* ========================================
   TABLE HEADER
======================================== */

.product-responsive-table thead th {

    height: 54px;

    padding: 14px 15px !important;

    background: #f7f9f8 !important;

    color: #596168 !important;

    border: none !important;

    border-bottom: 1px solid #e7ebed !important;

    font-size: 11px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .5px;

    white-space: nowrap;

    vertical-align: middle;

}


/* ========================================
   TABLE BODY
======================================== */

.product-responsive-table tbody td {

    height: 70px;

    padding: 12px 15px !important;

    background: #ffffff;

    border: none !important;

    border-bottom: 1px solid #edf0f2 !important;

    color: #555e65;

    font-size: 13px;

    vertical-align: middle !important;

}


/* Last row */

.product-responsive-table tbody tr:last-child td {

    border-bottom: none !important;

}


/* ========================================
   ROW HOVER
======================================== */

.product-table-row {

    transition: all .2s ease;

}


.product-table-row:hover td {

    background: #f9fcfa !important;

}


.product-table-row:hover td:first-child {

    box-shadow: inset 3px 0 0 #198754;

}


/* ========================================
   SR NO
======================================== */

.product-sr-badge {

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


/* ========================================
   PRODUCT IMAGE
======================================== */

.product-list-image {

    width: 52px;

    height: 52px;

    object-fit: cover;

    display: block;

    margin: auto;

    border-radius: 10px;

    border: 1px solid #e4e8eb;

    background: #ffffff;

    box-shadow: 0 2px 7px rgba(0, 0, 0, .05);

    transition: all .2s ease;

}


.product-list-image:hover {

    transform: scale(1.08);

    box-shadow: 0 5px 14px rgba(0, 0, 0, .10);

}


/* ========================================
   NO IMAGE
======================================== */

.product-no-image {

    width: 52px;

    height: 52px;

    margin: auto;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    background: #f5f6f7;

    color: #adb5bd;

    font-size: 18px;

}


/* ========================================
   CATEGORY
======================================== */

.product-category {

    display: inline-flex;

    align-items: center;

    padding: 6px 10px;

    border-radius: 8px;

    background: #f1f8f3;

    color: #198754;

    font-size: 12px;

    font-weight: 600;

    white-space: nowrap;

}


/* ========================================
   PRODUCT NAME
======================================== */

.product-name {

    display: block;

    max-width: 230px;

    color: #212529;

    font-size: 14px;

    font-weight: 650;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;

}


/* ========================================
   PRICE
======================================== */

.product-price {

    color: #495057;

    font-size: 13px;

    font-weight: 600;

    white-space: nowrap;

}


/* Base Price */

.product-base-price {

    color: #6c757d;

}


/* MRP */

.product-mrp {

    color: #dc3545;

}


/* Net Price */

.product-net-price {

    color: #198754;

    font-weight: 700;

}


/* ========================================
   GST
======================================== */

.product-gst {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    min-width: 48px;

    padding: 5px 9px;

    border-radius: 7px;

    background: #f5f6f7;

    color: #495057;

    font-size: 12px;

    font-weight: 600;

}


/* ========================================
   ACTIONS
======================================== */

.product-actions {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    flex-wrap: nowrap;

    white-space: nowrap;

}


.product-delete-form {

    display: inline-flex;

    margin: 0;

    padding: 0;

}


.product-action-btn {

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

    cursor: pointer;

    transition: all .2s ease;

}


.product-action-btn:hover {

    transform: translateY(-2px);

}


/* View */

.product-view-btn {

    background: #e9f2ff !important;

    color: #0d6efd !important;

}


.product-view-btn:hover {

    background: #0d6efd !important;

    color: #ffffff !important;

}


/* Edit */

.product-edit-btn {

    background: #fff3dc !important;

    color: #f59f00 !important;

}


.product-edit-btn:hover {

    background: #f59f00 !important;

    color: #ffffff !important;

}


/* Delete */

.product-delete-btn {

    background: #ffe9e9 !important;

    color: #dc3545 !important;

}


.product-delete-btn:hover {

    background: #dc3545 !important;

    color: #ffffff !important;

}


/* ========================================
   EMPTY STATE
======================================== */

.product-empty-cell {

    padding: 55px 20px !important;

}


.product-empty-icon {

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


.product-empty-cell h6 {

    margin-bottom: 5px;

    color: #343a40;

    font-weight: 700;

}


.product-empty-cell p {

    margin: 0;

    color: #adb5bd;

    font-size: 13px;

}


/* ========================================
   TABLET
======================================== */

@media (max-width: 768px) {

    .product-table-wrapper {

        border-radius: 12px;

    }


    .product-responsive-table {

        min-width: 1200px;

    }


    .product-responsive-table thead th {

        height: 48px;

        padding: 12px !important;

        font-size: 10px;

    }


    .product-responsive-table tbody td {

        height: 62px;

        padding: 10px 12px !important;

    }


    .product-list-image,
    .product-no-image {

        width: 46px;

        height: 46px;

    }


    .product-name {

        font-size: 13px;

    }


    .product-action-btn {

        width: 32px !important;

        height: 32px !important;

        min-width: 32px !important;

    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 480px) {

    .product-responsive-table {

        min-width: 1150px;

    }


    .product-table-responsive::after {

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
   PRODUCT HEADER
======================================== */

.product-header {

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
   TITLE
======================================== */

.product-header-title {

    flex: 1;

    min-width: 0;

}


.product-header-title .card-title {

    margin: 0;

    font-size: 21px;

    font-weight: 700;

    line-height: 1.3;

    color: #212529;

}


.product-subtitle {

    display: block;

    margin-top: 4px;

    color: #8a9299;

    font-size: 12px;

    line-height: 1.4;

}


/* ========================================
   ACTIONS
======================================== */

.product-header-actions {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    flex-shrink: 0;

    flex-wrap: wrap;

}


/* ========================================
   COMMON BUTTON
======================================== */

.product-header-btn {

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


.product-header-btn i {

    font-size: 15px;

    line-height: 1;

}


/* ========================================
   ADD PRODUCT
======================================== */

.product-add-btn {

    background: #198754;

    color: #ffffff;

    border: 1px solid #198754;

}


.product-add-btn:hover {

    background: #157347;

    border-color: #157347;

    color: #ffffff;

    transform: translateY(-1px);

}


/* ========================================
   UPLOAD CSV
======================================== */

.product-upload-btn {

    background: #0d6efd;

    color: #ffffff;

    border: 1px solid #0d6efd;

}


.product-upload-btn:hover {

    background: #0b5ed7;

    border-color: #0b5ed7;

    color: #ffffff;

    transform: translateY(-1px);

}


/* ========================================
   DOWNLOAD CSV
======================================== */

.product-download-btn {

    background: #ffffff;

    color: #495057;

    border: 1px solid #ced4da;

}


.product-download-btn:hover {

    background: #f8f9fa;

    color: #212529;

    border-color: #adb5bd;

    transform: translateY(-1px);

}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .product-header {

        align-items: flex-start;

        flex-direction: column;

        gap: 14px;

        padding: 17px 18px;

    }


    .product-header-title {

        width: 100%;

    }


    .product-header-actions {

        width: 100%;

        justify-content: flex-start;

        align-items: center;

    }


    .product-header-btn {

        min-width: 125px;

    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .product-header {

        padding: 15px;

        gap: 14px;

    }


    .product-header-title .card-title {

        font-size: 19px;

    }


    .product-subtitle {

        margin-top: 3px;

        font-size: 11px;

    }


    .product-header-actions {

        display: grid;

        grid-template-columns: 1fr 1fr;

        width: 100%;

        gap: 8px;

    }


    .product-header-btn {

        width: 100%;

        min-width: 0;

        height: 40px;

        padding: 0 8px;

        font-size: 12px;

    }


    /* Download CSV Full Width */

    .product-download-btn {

        grid-column: 1 / -1;

    }

}


/* ========================================
   VERY SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .product-header {

        padding: 13px;

    }


    .product-header-actions {

        grid-template-columns: 1fr;

    }


    .product-download-btn {

        grid-column: auto;

    }


    .product-header-btn {

        width: 100%;

    }

}
/* ========================================
   PRODUCT TITLE ROW
======================================== */

.product-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-title-icon {
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

.product-title-row:hover .product-title-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.04);
}


/* ========================================
   HEADER TITLE
======================================== */

.product-header-title .card-title {
    margin: 0 !important;

    color: #212529;

    font-size: 20px;
    font-weight: 700;

    line-height: 1.3;
}

.product-subtitle {
    display: block;

    margin-top: 2px;

    color: #8f969c;

    font-size: 11px;
    font-weight: 400;

    line-height: 1.4;
}


/* ========================================
   HEADER ACTIONS
======================================== */

.product-header-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    gap: 8px;

    flex-shrink: 0;
}


/* ========================================
   COMMON HEADER BUTTON
======================================== */

.product-header-btn {
    min-width: 130px;
    height: 40px;

    padding: 7px 13px !important;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    border-radius: 9px;

    font-size: 12.5px;
    font-weight: 600;

    line-height: 1;

    white-space: nowrap;

    text-decoration: none !important;

    cursor: pointer;

    box-shadow: 0 3px 9px rgba(0,0,0,.04);

    transition: all .2s ease;
}

.product-header-btn:hover {
    transform: translateY(-2px);

    box-shadow: 0 6px 14px rgba(0,0,0,.08);
}

.product-btn-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: rgba(255,255,255,.14);
}

.product-btn-icon i {
    font-size: 12px;
}


/* ========================================
   ADD PRODUCT
======================================== */

.product-add-btn {
    background: #198754 !important;

    border: 1px solid #198754 !important;

    color: #ffffff !important;
}

.product-add-btn:hover {
    background: #157347 !important;

    border-color: #157347 !important;

    color: #ffffff !important;
}


/* ========================================
   UPLOAD CSV
======================================== */

.product-upload-btn {
    background: #0d6efd !important;

    border: 1px solid #0d6efd !important;

    color: #ffffff !important;
}

.product-upload-btn:hover {
    background: #0b5ed7 !important;

    border-color: #0b5ed7 !important;

    color: #ffffff !important;
}


/* ========================================
   DOWNLOAD CSV
======================================== */

.product-download-btn {
    background: #ffffff !important;

    border: 1px solid #dce1e4 !important;

    color: #596168 !important;
}

.product-download-btn:hover {
    background: #f7f9f8 !important;

    border-color: #c8d0d4 !important;

    color: #343a40 !important;
}


/* ========================================
   PRODUCT TABLE
======================================== */

.product-responsive-table {
    width: 100% !important;

    min-width: 1250px;

    margin: 0 !important;

    border-collapse: separate !important;

    border-spacing: 0 !important;

    table-layout: fixed;
}


/* ========================================
   COLUMN WIDTH
======================================== */

.product-responsive-table th:nth-child(1),
.product-responsive-table td:nth-child(1) {
    width: 70px;

    text-align: center;
}

.product-responsive-table th:nth-child(2),
.product-responsive-table td:nth-child(2) {
    width: 95px;

    text-align: center;
}

.product-responsive-table th:nth-child(3),
.product-responsive-table td:nth-child(3) {
    width: 175px;
}

.product-responsive-table th:nth-child(4),
.product-responsive-table td:nth-child(4) {
    width: 240px;
}

.product-responsive-table th:nth-child(5),
.product-responsive-table td:nth-child(5) {
    width: 125px;
}

.product-responsive-table th:nth-child(6),
.product-responsive-table td:nth-child(6) {
    width: 115px;
}

.product-responsive-table th:nth-child(7),
.product-responsive-table td:nth-child(7) {
    width: 125px;
}

.product-responsive-table th:nth-child(8),
.product-responsive-table td:nth-child(8) {
    width: 90px;

    text-align: center;
}

.product-responsive-table th:nth-child(9),
.product-responsive-table td:nth-child(9) {
    width: 155px;

    text-align: center;
}


/* ========================================
   PRODUCT NAME
======================================== */

.product-name-wrapper {
    display: flex;

    align-items: center;

    gap: 9px;

    min-width: 0;
}

.product-name-icon {
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

.product-table-row:hover .product-name-icon {
    background: #198754;

    color: #ffffff;

    transform: scale(1.05);
}

.product-name {
    display: block;

    max-width: 190px;

    color: #212529;

    font-size: 13.5px;

    font-weight: 600;

    line-height: 1.4;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/* ========================================
   CATEGORY
======================================== */

.product-category {
    display: inline-flex;

    align-items: center;

    max-width: 150px;

    padding: 6px 10px;

    border-radius: 8px;

    background: #edf8f1;

    color: #198754;

    border: 1px solid #e1f0e6;

    font-size: 11.5px;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}

.product-category i {
    font-size: 12px;
}


/* ========================================
   IMAGE
======================================== */

.product-list-image,
.product-no-image {
    width: 52px;
    height: 52px;
}

.product-list-image {
    object-fit: cover;

    display: block;

    margin: auto;

    border-radius: 10px;

    border: 1px solid #e4e8eb;

    background: #ffffff;

    box-shadow: 0 2px 7px rgba(0,0,0,.05);

    transition: all .2s ease;
}

.product-list-image:hover {
    transform: scale(1.08);

    box-shadow: 0 5px 14px rgba(0,0,0,.10);
}

.product-no-image {
    margin: auto;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #f5f6f7;

    color: #adb5bd;

    font-size: 18px;
}


/* ========================================
   PRICE
======================================== */

.product-price {
    display: inline-block;

    font-size: 12.5px;

    font-weight: 600;

    white-space: nowrap;
}

.product-base-price {
    color: #6c757d;
}

.product-mrp {
    color: #dc3545;
}

.product-net-price {
    color: #198754;

    font-weight: 700;
}


/* ========================================
   GST
======================================== */

.product-gst {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 48px;

    padding: 5px 9px;

    border-radius: 7px;

    background: #f5f6f7;

    border: 1px solid #e4e8ea;

    color: #495057;

    font-size: 11.5px;

    font-weight: 600;
}

.product-table-row:hover .product-gst {
    background: #edf8f1;

    border-color: #dceee2;

    color: #198754;
}


/* ========================================
   ACTION CELL
======================================== */

.product-action-cell {
    white-space: nowrap;

    text-align: center !important;
}

.product-actions {
    display: flex !important;

    align-items: center !important;

    justify-content: center !important;

    gap: 7px !important;

    flex-wrap: nowrap !important;

    white-space: nowrap !important;

    min-height: 35px;
}


/* ========================================
   DELETE FORM
======================================== */

.product-delete-form {
    display: inline-flex !important;

    align-items: center !important;

    margin: 0 !important;

    padding: 0 !important;
}


/* ========================================
   ACTION BUTTON
======================================== */

.product-action-btn {
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

    box-shadow: 0 2px 6px rgba(0,0,0,.04);

    transition: all .2s ease;
}

.product-action-btn:hover {
    transform: translateY(-2px);
}


/* ========================================
   VIEW
======================================== */

.product-view-btn {
    background: #e8f1ff !important;

    color: #0d6efd !important;

    border: 1px solid #cfe2ff !important;
}

.product-view-btn:hover {
    background: #0d6efd !important;

    color: #ffffff !important;

    box-shadow: 0 5px 12px rgba(13,110,253,.18);
}


/* ========================================
   EDIT
======================================== */

.product-edit-btn {
    background: #fff4df !important;

    color: #f59f00 !important;

    border: 1px solid #ffe8b3 !important;
}

.product-edit-btn:hover {
    background: #f59f00 !important;

    color: #ffffff !important;

    box-shadow: 0 5px 12px rgba(245,159,0,.18);
}


/* ========================================
   DELETE
======================================== */

.product-delete-btn {
    background: #ffe9e9 !important;

    color: #dc3545 !important;

    border: 1px solid #f5c2c7 !important;
}

.product-delete-btn:hover {
    background: #dc3545 !important;

    color: #ffffff !important;

    box-shadow: 0 5px 12px rgba(220,53,69,.18);
}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .product-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 14px;

        padding: 17px 18px;
    }

    .product-header-title {
        width: 100%;
    }

    .product-header-actions {
        width: 100%;

        justify-content: flex-start;
    }

    .product-responsive-table {
        min-width: 1200px;
    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .product-header {
        padding: 15px;

        gap: 14px;
    }

    .product-title-row {
        gap: 10px;
    }

    .product-title-icon {
        width: 38px;
        height: 38px;

        min-width: 38px;

        font-size: 17px;
    }

    .product-header-title .card-title {
        font-size: 19px;
    }

    .product-subtitle {
        font-size: 11px;
    }

    .product-header-actions {
        display: grid;

        grid-template-columns: 1fr 1fr;

        width: 100%;

        gap: 8px;
    }

    .product-header-btn {
        width: 100%;

        min-width: 0;

        height: 40px;

        padding: 0 8px;

        font-size: 12px;
    }

    .product-download-btn {
        grid-column: 1 / -1;
    }

    .product-responsive-table {
        min-width: 1200px;
    }

    .product-table-wrapper {
        margin-top: 15px;
    }

}


/* ========================================
   SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .product-header {
        padding: 13px;
    }

    .product-header-actions {
        grid-template-columns: 1fr;
    }

    .product-download-btn {
        grid-column: auto;
    }

    .product-title-icon {
        width: 36px;
        height: 36px;

        min-width: 36px;
    }

    .product-header-title .card-title {
        font-size: 18px;
    }

    .product-subtitle {
        font-size: 10.5px;
    }

}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl flex-grow-1 container-p-y">

    @php
        $canView = hasPermission('product.view');
        $canEdit = hasPermission('product.edit');
        $canDelete = hasPermission('product.delete');
    @endphp

    <div class="card">
        <div class="card-datatable text-nowrap">

           <!-- ========================================
                PRODUCT HEADER
            ======================================== -->

            <div class="product-header">

                <!-- TITLE -->
                <div class="product-header-title">

                    <div class="product-title-row">

                        <div class="product-title-icon">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Product
                            </h4>

                            <span class="product-subtitle">
                                Manage your products and product details
                            </span>
                        </div>

                    </div>

                </div>

                <!-- ACTIONS -->
                @if (hasPermission('product.create'))

                    <div class="product-header-actions">

                        <!-- ADD PRODUCT -->
                        <a href="{{ route('product.create') }}"
                        class="product-header-btn product-add-btn">

                            <span class="product-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Product</span>

                        </a>

                        <!-- UPLOAD CSV -->
                        <button type="button"
                                class="product-header-btn product-upload-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#productBulkUploadModal">

                            <span class="product-btn-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </span>

                            <span>Upload CSV</span>

                        </button>

                        <!-- DOWNLOAD CSV -->
                        <a href="javascript:void(0);"
                        class="product-header-btn product-download-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#csvModal">

                            <span class="product-btn-icon">
                                <i class="bi bi-download"></i>
                            </span>

                            <span>Download CSV</span>

                        </a>

                    </div>

                @endif

            </div>

            <!-- Search -->
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

            <!-- =====================================================
                PRODUCT LIST TABLE
            ===================================================== -->

            <div class="product-table-wrapper mt-4">

                <div class="product-table-responsive">

                    <table id="batchTable"
                        class="table product-responsive-table">

                        <thead>

                            <tr>

                                <th class="text-center">
                                    Sr No
                                </th>

                                <th class="text-center">
                                    Image
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Product Name
                                </th>

                                <th>
                                    Base Price
                                </th>

                                <th>
                                    MRP
                                </th>

                                <th>
                                    Net Price
                                </th>

                                <th class="text-center">
                                    GST (%)
                                </th>

                                @if ($canView || $canEdit || $canDelete)

                                    <th class="text-center">
                                        Actions
                                    </th>

                                @endif

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($products as $index => $product)

                                <tr class="product-table-row">

                                    {{-- =========================
                                        SR NO
                                    ========================== --}}

                                    <td class="text-center">

                                        <span class="product-sr-badge">

                                            {{ $loop->iteration }}

                                        </span>

                                    </td>

                                    {{-- =========================
                                        PRODUCT IMAGE
                                    ========================== --}}

                                    <td class="text-center">

                                        @if (!empty($product->product_images))

                                            @php

                                                $images = $product->product_images;

                                                $image = $images[0] ?? null;

                                            @endphp


                                            @if ($image)

                                                <img src="{{ asset('storage/products/' . $image) }}"
                                                    alt="Product Image"
                                                    class="product-list-image">

                                            @else

                                                <div class="product-no-image">

                                                    <i class="bi bi-image"></i>

                                                </div>

                                            @endif

                                        @else

                                            <div class="product-no-image">

                                                <i class="bi bi-image"></i>

                                            </div>

                                        @endif

                                    </td>

                                    {{-- =========================
                                        CATEGORY
                                    ========================== --}}

                                    <td>

                                        <span class="product-category">

                                            <i class="bi bi-folder me-2"></i>

                                            {{ $product->category->name ?? '-' }}

                                        </span>

                                    </td>

                                    {{-- =========================
                                        PRODUCT NAME
                                    ========================== --}}

                                    <td>

                                        <div class="product-name-wrapper">

                                            <div class="product-name-icon">
                                                <i class="bi bi-box"></i>
                                            </div>

                                            <span class="product-name"
                                                title="{{ $product->name }}">

                                                {{ $product->name }}

                                            </span>

                                        </div>

                                    </td>

                                    {{-- =========================
                                        BASE PRICE
                                    ========================== --}}

                                    <td>

                                        <span class="product-price product-base-price">

                                            ₹ {{ number_format($product->base_price, 2) }}

                                        </span>

                                    </td>

                                    {{-- =========================
                                        MRP
                                    ========================== --}}

                                    <td>

                                        <span class="product-price product-mrp">

                                            ₹ {{ number_format($product->mrp, 2) }}

                                        </span>

                                    </td>

                                    {{-- =========================
                                        NET PRICE
                                    ========================== --}}

                                    <td>

                                        <span class="product-price product-net-price">

                                            ₹ {{ number_format($product->final_price, 2) }}

                                        </span>

                                    </td>

                                    {{-- =========================
                                        GST
                                    ========================== --}}

                                    <td class="text-center">

                                        <span class="product-gst">

                                            {{ $product?->tax?->gst ?? '-' }}%

                                        </span>

                                    </td>


                                   {{-- =========================
                                        ACTIONS
                                    ========================= --}}

                                    @if ($canView || $canEdit || $canDelete)

                                        <td class="text-center product-action-cell">

                                            <div class="product-actions">

                                                @if (hasPermission('product.view'))

                                                    <a href="{{ route('product.show', $product->id) }}"
                                                    class="product-action-btn product-view-btn"
                                                    title="View Product">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif

                                                @if (hasPermission('product.edit'))

                                                    <a href="{{ route('product.edit', $product->id) }}"
                                                    class="product-action-btn product-edit-btn"
                                                    title="Edit Product">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif

                                                @if (hasPermission('product.delete'))

                                                    <form action="{{ route('product.destroy', $product->id) }}"
                                                        method="POST"
                                                        class="product-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete product?')"
                                                                class="product-action-btn product-delete-btn"
                                                                title="Delete Product">

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

                                    <td colspan="{{ ($canView || $canEdit || $canDelete) ? 9 : 8 }}"
                                        class="text-center product-empty-cell">

                                        <div>

                                            <div class="product-empty-icon">

                                                <i class="bi bi-box-seam"></i>

                                            </div>

                                            <h6>
                                                No Products Found
                                            </h6>

                                            <p>
                                                There are no products available at the moment.
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
                {{ $products->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

<!-- bulk download model -->
<div class="modal fade" id="csvModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="csvForm">
                @csrf

                <div class="modal-header">
                    <h5>Download Product CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Category -->
                    <select id="category" name="category_id" class="form-control mb-2" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <!-- SubCategory (Single) -->
                    <!-- <select id="subcategory" name="subcategory_id" class="form-control mb-2" required>
                        <option value="">Select SubCategory</option>
                    </select> -->

                    <!-- SubCategory Multi Select -->
                    <div class="dropdown mb-2">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button" id="subcategoryDropdown" data-bs-toggle="dropdown">
                            Select SubCategory
                        </button>

                        <div class="dropdown-menu w-100 p-2"
                            style="max-height: 200px; overflow-y: auto;"
                            id="subcategoryDropdownMenu">
                            <p class="text-muted">Select SubCategory</p>
                        </div>
                    </div>

                    <!-- Brand Dropdown with Checkbox -->
                    <div class="dropdown mb-2">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button" id="brandDropdown" data-bs-toggle="dropdown">
                            Select Brand
                        </button>

                        <div class="dropdown-menu w-100 p-2"
                            style="max-height: 200px; overflow-y: auto;"
                            id="brandDropdownMenu">
                            <p class="text-muted">Select Brand</p>
                        </div>
                    </div>

                    <!-- UNIT -->
                    <select id="unit" name="unit" class="form-control mb-2" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>

                    <!-- GST -->
                    <select id="gst" name="gst" class="form-control mb-2" required>
                        <option value="">Select GST</option>
                        @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->percentage }}%)</option>
                        @endforeach
                    </select>

                </div>

                <div class="modal-footer">
                    <button type="button" id="downloadBtn" class="btn btn-success">
                        Download CSV
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Product Bulk Upload Modal -->
<div class="modal fade" id="productBulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Products Csv</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('product.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">CSV File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <!-- <small class="text-muted">Only .xlsx, .xls, .csv allowed. Max 5MB.</small> -->
                    </div>
                    <!-- <div class="alert alert-info py-2 mb-0">
                        <small>
                            <strong>Format:</strong> Category | Sub Category | Brand | Product Name | Barcode |
                            Description | Unit | Unit Value | Base Price | Selling Price | MRP | GST | Image URL<br>
                            <a href="{{ route('product.sample-excel') }}" class="text-decoration-underline">Download
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

<script>
    const categories = @json($categories);

    // =========================
    // ✅ LOAD SUBCATEGORIES
    // =========================
    document.getElementById('category').addEventListener('change', function () {

        let categoryId = this.value;

        fetch(`/get-sub-categories/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                let menu = document.getElementById('subcategoryDropdownMenu');
                menu.innerHTML = '';

                // Select All
                menu.innerHTML += `
                    <div class="form-check">
                        <input type="checkbox" id="selectAllSubcategory" class="form-check-input">
                        <label class="form-check-label fw-bold">Select All</label>
                    </div>
                    <hr>
                `;

                data.forEach(sub => {
                    menu.innerHTML += `
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input subcategory-checkbox"
                                value="${sub.id}">
                            <label class="form-check-label">${sub.name}</label>
                        </div>
                    `;
                });

                // Reset brand dropdown
                document.getElementById('brandDropdownMenu').innerHTML =
                    '<p class="text-muted">Select SubCategory first</p>';
            });
    });


    // =========================
    // ✅ HANDLE SUBCATEGORY + BRAND LOAD
    // =========================
    document.addEventListener('change', function (e) {

        // ✅ Select All Subcategory
        if (e.target.id === 'selectAllSubcategory') {
            document.querySelectorAll('.subcategory-checkbox').forEach(cb => {
                cb.checked = e.target.checked;
            });
        }

        // ✅ When subcategory changes → load brands
        if (e.target.classList.contains('subcategory-checkbox') || e.target.id === 'selectAllSubcategory') {

            let selectedSubIds = [];

            document.querySelectorAll('.subcategory-checkbox:checked').forEach(cb => {
                selectedSubIds.push(cb.value);
            });

            // Update button text
            let subBtn = document.getElementById('subcategoryDropdown');
            subBtn.innerText = selectedSubIds.length > 0
                ? selectedSubIds.length + " subcategory(s) selected"
                : "Select SubCategory";

            let cat = categories.find(c => c.id == document.getElementById('category').value);
            let dropdown = document.getElementById('brandDropdownMenu');

            dropdown.innerHTML = '';

            if (!cat || selectedSubIds.length === 0) {
                dropdown.innerHTML = '<p class="text-muted">Select SubCategory first</p>';
                return;
            }

            let brandsMap = {};

            selectedSubIds.forEach(subId => {

                let sub = cat.sub_categories.find(s => s.id == subId);

                if (sub && sub.brands) {
                    sub.brands.forEach(b => {
                        brandsMap[b.id] = b; // remove duplicates
                    });
                }
            });

            let brands = Object.values(brandsMap);

            if (brands.length > 0) {

                dropdown.innerHTML += `
                    <input type="text" class="form-control mb-2" placeholder="Search..." id="brandSearch">

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllBrands">
                        <label class="form-check-label fw-bold">Select All</label>
                    </div>
                    <hr>
                `;

                brands.forEach(b => {
                    dropdown.innerHTML += `
                        <div class="form-check">
                            <input class="form-check-input brand-checkbox" type="checkbox"
                                name="brand_id[]" value="${b.id}">
                            <label class="form-check-label">${b.name}</label>
                        </div>
                    `;
                });

            } else {
                dropdown.innerHTML = '<p class="text-danger">No brands found</p>';
            }
        }

        // =========================
        // ✅ SELECT ALL BRANDS
        // =========================
        if (e.target.id === 'selectAllBrands') {

            let isChecked = e.target.checked;

            document.querySelectorAll('.brand-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });

            let btn = document.getElementById('brandDropdown');
            btn.innerText = isChecked ? "All Brands Selected" : "Select Brand";
        }

        // =========================
        // ✅ BRAND COUNT
        // =========================
        if (e.target.classList.contains('brand-checkbox')) {

            let selected = document.querySelectorAll('.brand-checkbox:checked');
            let btn = document.getElementById('brandDropdown');

            btn.innerText = selected.length > 0
                ? selected.length + " brand(s) selected"
                : "Select Brand";
        }
    });


    // =========================
    // ✅ SEARCH BRAND
    // =========================
    document.addEventListener('keyup', function (e) {

        if (e.target.id === 'brandSearch') {

            let value = e.target.value.toLowerCase();

            document.querySelectorAll('#brandDropdownMenu .form-check').forEach(div => {
                div.style.display = div.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        }
    });


    // =========================
    // ✅ DOWNLOAD CSV
    // =========================
    document.getElementById('downloadBtn').addEventListener('click', function () {

        let category = document.getElementById('category').value;
        let unit = document.getElementById('unit').value;
        let gst = document.getElementById('gst').value;

        // ✅ Multiple subcategories
        let subcategories = [];
        document.querySelectorAll('.subcategory-checkbox:checked').forEach(cb => {
            subcategories.push(cb.value);
        });

        // ✅ Brands
        let brands = [];
        document.querySelectorAll('.brand-checkbox:checked').forEach(cb => {
            brands.push(cb.value);
        });

        if (!category || subcategories.length === 0 || brands.length === 0 || !unit || !gst) {
            alert('Please select all fields');
            return;
        }

        let form = document.getElementById('csvForm');
        let formData = new FormData(form);

        // ✅ Append multiple subcategories
        formData.delete('subcategory_id');

        subcategories.forEach(id => {
            formData.append('subcategory_ids[]', id);
        });

        fetch("{{ route('product.sample-excel') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(res => res.blob())
        .then(blob => {

            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = "product_sample.csv";
            a.click();

            let modal = bootstrap.Modal.getInstance(document.getElementById('csvModal'));
            modal.hide();
        });
    });
</script>

@endpush