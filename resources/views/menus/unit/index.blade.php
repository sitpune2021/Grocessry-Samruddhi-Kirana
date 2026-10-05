@extends('layouts.app')

@section('content')

<style>
    /* ========================================
   UNIT TABLE WRAPPER
======================================== */

.unit-table-wrapper {
    width: 100%;
    margin-top: 30px;
    padding: 0 15px 20px;
    box-sizing: border-box;
}


/* ========================================
   TABLE RESPONSIVE
======================================== */

.unit-table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;

    border: 1px solid #edf0f2;
    border-radius: 10px;
    background: #ffffff;
}


/* ========================================
   SCROLLBAR
======================================== */

.unit-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.unit-table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;
    border-radius: 10px;
}

.unit-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd4da;
    border-radius: 10px;
}

.unit-table-responsive::-webkit-scrollbar-thumb:hover {
    background: #adb5bd;
}


/* ========================================
   MAIN TABLE
======================================== */

.unit-responsive-table {
    width: 100%;
    min-width: 650px;

    margin: 0;

    border-collapse: collapse;
    border-spacing: 0;

    table-layout: fixed;
}


/* ========================================
   COLUMN WIDTH
======================================== */

.unit-responsive-table .unit-sr-column {
    width: 80px;
}

.unit-responsive-table .unit-name-column {
    width: 40%;
}

.unit-responsive-table .unit-short-column {
    width: 35%;
}

.unit-responsive-table .unit-action-column {
    width: 150px;
}


/* ========================================
   TABLE HEADER
======================================== */

.unit-responsive-table thead th {
    position: relative;

    background: #f8f9fa;

    color: #495057;

    font-size: 12px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.3px;

    padding: 14px 16px;

    border: 0;
    border-bottom: 1px solid #e9ecef;

    white-space: nowrap;

    vertical-align: middle;
}


/* ========================================
   TABLE BODY
======================================== */

.unit-responsive-table tbody td {
    position: relative;

    padding: 14px 16px;

    font-size: 13px;
    color: #495057;

    border: 0;
    border-bottom: 1px solid #f0f2f4;

    vertical-align: middle;

    background: #ffffff;

    height: 62px;

    box-sizing: border-box;
}

.unit-responsive-table tbody tr:last-child td {
    border-bottom: 0;
}


/* ========================================
   ROW
======================================== */

.unit-table-row {
    transition: background-color 0.2s ease;
}


/* ========================================
   ROW HOVER
======================================== */

.unit-table-row:hover td {
    background: #fafbfc;
}


/* ========================================
   LEFT HOVER LINE
   IMPORTANT:
   Apply it on FIRST TD only
   so column alignment never breaks.
======================================== */

.unit-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}


/* ========================================
   SR NO
======================================== */

.unit-sr-badge {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 30px;

    padding: 0 8px;

    border-radius: 7px;

    background: #f1f3f5;
    color: #495057;

    font-size: 12px;
    font-weight: 700;

    box-sizing: border-box;
}


/* ========================================
   UNIT NAME
======================================== */

.unit-name {
    font-size: 14px;
    font-weight: 600;

    color: #212529;

    line-height: 1.4;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* ========================================
   SHORT NAME
======================================== */

.unit-short-name {
    display: inline-flex;

    align-items: center;

    padding: 5px 10px;

    border-radius: 6px;

    background: #f8f9fa;

    border: 1px solid #edf0f2;

    color: #6c757d;

    font-size: 12px;
    font-weight: 600;

    line-height: 1.3;

    white-space: nowrap;
}


/* ========================================
   ACTIONS
======================================== */

.unit-actions {
    display: flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    width: 100%;

    white-space: nowrap;
}


/* ========================================
   DELETE FORM
======================================== */

.unit-delete-form {
    margin: 0;
    padding: 0;

    display: inline-flex;
}


/* ========================================
   ACTION BUTTON
======================================== */

.unit-action-btn {
    width: 34px;
    height: 34px;

    padding: 0;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 7px;

    font-size: 15px;
    line-height: 1;

    text-decoration: none;

    cursor: pointer;

    box-sizing: border-box;

    transition:
        background-color 0.2s ease,
        color 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.unit-action-btn i {
    font-size: 15px;
}


/* ========================================
   VIEW BUTTON
======================================== */

.unit-view-btn {
    background: #e7f1ff;
    color: #0d6efd;

    border: 1px solid #cfe2ff;
}

.unit-view-btn:hover {
    background: #0d6efd;
    color: #ffffff;

    border-color: #0d6efd;

    transform: translateY(-1px);
}


/* ========================================
   EDIT BUTTON
======================================== */

.unit-edit-btn {
    background: #fff3cd;
    color: #997404;

    border: 1px solid #ffe69c;
}

.unit-edit-btn:hover {
    background: #ffc107;
    color: #212529;

    border-color: #ffc107;

    transform: translateY(-1px);
}


/* ========================================
   DELETE BUTTON
======================================== */

.unit-delete-btn {
    background: #f8d7da;
    color: #dc3545;

    border: 1px solid #f1aeb5;
}

.unit-delete-btn:hover {
    background: #dc3545;
    color: #ffffff;

    border-color: #dc3545;

    transform: translateY(-1px);
}


/* ========================================
   EMPTY STATE
======================================== */

.unit-empty-cell {
    padding: 50px 20px !important;

    text-align: center;

    background: #ffffff !important;
}

.unit-empty-state {
    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;
}

.unit-empty-icon {
    width: 55px;
    height: 55px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 50%;

    background: #f8f9fa;

    color: #adb5bd;

    font-size: 23px;
}

.unit-empty-title {
    color: #495057;

    font-size: 14px;
    font-weight: 700;
}

.unit-empty-text {
    margin-top: 4px;

    color: #9aa0a6;

    font-size: 12px;
}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .unit-table-wrapper {
        margin-top: 25px;

        padding: 0 12px 18px;
    }

    .unit-responsive-table {
        min-width: 620px;
    }

    .unit-responsive-table thead th {
        padding: 13px 14px;
    }

    .unit-responsive-table tbody td {
        padding: 13px 14px;
    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .unit-table-wrapper {
        margin-top: 20px;

        padding: 0 10px 15px;
    }

    .unit-table-responsive {
        border-radius: 8px;
    }

    .unit-responsive-table {
        min-width: 620px;
    }

    .unit-responsive-table thead th {
        padding: 12px;

        font-size: 11px;
    }

    .unit-responsive-table tbody td {
        padding: 12px;

        font-size: 12px;
    }

    .unit-responsive-table tbody td {
        height: 58px;
    }

    .unit-name {
        font-size: 13px;
    }

    .unit-short-name {
        font-size: 11px;

        padding: 5px 8px;
    }

    .unit-action-btn {
        width: 32px;
        height: 32px;
    }

    .unit-action-btn i {
        font-size: 14px;
    }


    /* ========================================
       MOBILE SWIPE MESSAGE
    ======================================== */

    .unit-table-responsive::after {
        content: "← Swipe left / right to view more →";

        display: block;

        padding: 8px 10px;

        text-align: center;

        color: #8a9299;

        background: #fafbfc;

        border-top: 1px solid #edf0f2;

        font-size: 11px;
        font-weight: 500;
    }

}


/* ========================================
   SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .unit-table-wrapper {
        padding-left: 7px;
        padding-right: 7px;
    }

    .unit-responsive-table {
        min-width: 600px;
    }

}

/* ========================================
   UNIT HEADER
======================================== */

.unit-header {
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

.unit-header-title {
    flex: 1;
    min-width: 0;
}

.unit-header-title .card-title {
    margin: 0;

    font-size: 21px;
    font-weight: 700;

    line-height: 1.3;

    color: #212529;
}

.unit-subtitle {
    display: block;

    margin-top: 4px;

    color: #8a9299;

    font-size: 12px;
    font-weight: 400;

    line-height: 1.4;
}


/* ========================================
   HEADER ACTIONS
======================================== */

.unit-header-actions {
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

.unit-header-btn {
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

    box-sizing: border-box;

    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}

.unit-header-btn i {
    font-size: 15px;

    line-height: 1;
}


/* ========================================
   ADD UNIT BUTTON
======================================== */

.unit-add-btn {
    background: #198754;

    color: #ffffff;

    border: 1px solid #198754;
}

.unit-add-btn:hover {
    background: #157347;

    border-color: #157347;

    color: #ffffff;

    transform: translateY(-1px);
}

.unit-add-btn:focus,
.unit-add-btn:active {
    background: #198754;

    border-color: #198754;

    color: #ffffff;

    box-shadow: none;
}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .unit-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 14px;

        padding: 17px 18px;
    }

    .unit-header-title {
        width: 100%;
    }

    .unit-header-actions {
        width: 100%;

        justify-content: flex-start;

        align-items: center;
    }

    .unit-header-btn {
        min-width: 125px;
    }
}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .unit-header {
        padding: 15px;

        gap: 14px;
    }

    .unit-header-title .card-title {
        font-size: 19px;
    }

    .unit-subtitle {
        margin-top: 3px;

        font-size: 11px;
    }

    .unit-header-actions {
        width: 100%;
    }

    .unit-header-btn {
        width: 100%;

        min-width: 0;

        height: 40px;

        padding: 0 10px;

        font-size: 12px;
    }
}


/* ========================================
   SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .unit-header {
        padding: 13px;
    }

    .unit-header-btn {
        width: 100%;

        height: 39px;
    }

    .unit-header-title .card-title {
        font-size: 18px;
    }

    .unit-subtitle {
        font-size: 10.5px;
    }
}
/* ========================================
   UNIT TITLE ROW
======================================== */

.unit-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.unit-title-icon {
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

.unit-title-row:hover .unit-title-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.04);
}


/* ========================================
   UNIT HEADER TITLE
======================================== */

.unit-header-title .card-title {
    margin: 0 !important;

    color: #212529;

    font-size: 20px;
    font-weight: 700;

    line-height: 1.3;
}

.unit-subtitle {
    display: block;

    margin-top: 2px;

    color: #8f969c;

    font-size: 11px;
    font-weight: 400;

    line-height: 1.4;
}


/* ========================================
   UNIT HEADER ACTION
======================================== */

.unit-header-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    flex-shrink: 0;

    gap: 8px;
}

.unit-header-btn {
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

    white-space: nowrap;

    text-decoration: none !important;

    box-shadow: 0 3px 9px rgba(0, 0, 0, .04);

    transition: all 0.2s ease;
}

.unit-header-btn:hover {
    transform: translateY(-2px);

    box-shadow: 0 6px 14px rgba(0, 0, 0, .08);
}

.unit-btn-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: rgba(255,255,255,.14);
}

.unit-btn-icon i {
    font-size: 12px;
}


/* ========================================
   ADD UNIT
======================================== */

.unit-add-btn {
    background: #198754 !important;
    border: 1px solid #198754 !important;

    color: #ffffff !important;
}

.unit-add-btn:hover {
    background: #157347 !important;
    border-color: #157347 !important;

    color: #ffffff !important;
}


/* ========================================
   TABLE CARD
======================================== */

.unit-table-wrapper {
    width: 100%;

    margin-top: 15px;

    padding: 0 15px 20px;

    box-sizing: border-box;
}


/* ========================================
   RESPONSIVE TABLE
======================================== */

.unit-table-responsive {
    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    -webkit-overflow-scrolling: touch;

    border: 1px solid #e9ecef;

    border-radius: 14px;

    background: #ffffff;

    box-shadow: 0 5px 25px rgba(0,0,0,.06);
}


/* ========================================
   MAIN TABLE
======================================== */

.unit-responsive-table {
    width: 100% !important;

    min-width: 650px;

    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    table-layout: fixed;
}


/* ========================================
   COLUMN ALIGNMENT
======================================== */

.unit-sr-column {
    width: 75px !important;
    min-width: 75px !important;
    max-width: 75px !important;

    text-align: center !important;
}

.unit-name-column {
    width: 250px !important;
    min-width: 220px !important;
}

.unit-short-column {
    width: 220px !important;
    min-width: 190px !important;
}

.unit-action-column {
    width: 155px !important;
    min-width: 155px !important;
    max-width: 155px !important;

    text-align: center !important;
}


/* ========================================
   TABLE HEADER
======================================== */

.unit-responsive-table thead th {
    height: 52px;

    padding: 14px 15px !important;

    background: #f5f8f6 !important;

    color: #495057 !important;

    border: none !important;

    border-bottom: 1px solid #e5e9eb !important;

    font-size: 10.5px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .6px;

    white-space: nowrap;

    vertical-align: middle !important;
}


/* ========================================
   TABLE BODY
======================================== */

.unit-responsive-table tbody td {
    height: 70px;

    padding: 13px 15px !important;

    background: #ffffff;

    color: #495057;

    border: none !important;

    border-bottom: 1px solid #edf0f2 !important;

    font-size: 13px;

    vertical-align: middle !important;
}

.unit-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}


/* ========================================
   ROW HOVER
======================================== */

.unit-table-row {
    transition: all .2s ease;
}

.unit-table-row:hover td {
    background: #f8fcf9 !important;
}

.unit-table-row:hover td:first-child {
    box-shadow: inset 4px 0 0 #198754;
}


/* ========================================
   SR BADGE
======================================== */

.unit-sr-badge {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 30px;

    padding: 0 8px;

    border-radius: 8px;

    background: #f1f8f3;

    color: #198754;

    border: 1px solid #e3eee7;

    font-size: 11.5px;
    font-weight: 700;

    transition: all .2s ease;
}

.unit-table-row:hover .unit-sr-badge {
    background: #198754;
    color: #ffffff;

    transform: scale(1.04);
}


/* ========================================
   UNIT NAME
======================================== */

.unit-name-wrapper {
    display: flex;

    align-items: center;

    gap: 9px;

    min-width: 0;
}

.unit-name-icon {
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

.unit-table-row:hover .unit-name-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.05);
}

.unit-name {
    color: #212529;

    font-size: 13.5px;
    font-weight: 600;

    line-height: 1.4;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* ========================================
   SHORT NAME
======================================== */

.unit-short-name {
    display: inline-flex;

    align-items: center;

    padding: 6px 10px;

    border-radius: 8px;

    background: #f7f9f8;

    border: 1px solid #e4e8ea;

    color: #667078;

    font-family: monospace;

    font-size: 11.5px;
    font-weight: 600;

    line-height: 1.4;

    white-space: nowrap;

    transition: all .2s ease;
}

.unit-table-row:hover .unit-short-name {
    background: #edf8f1;

    border-color: #dceee2;

    color: #198754;
}


/* ========================================
   ACTION CELL
======================================== */

.unit-action-cell {
    text-align: center !important;

    white-space: nowrap;
}

.unit-actions {
    display: flex !important;

    align-items: center !important;

    justify-content: center !important;

    gap: 7px !important;

    flex-wrap: nowrap !important;

    width: 100%;

    min-height: 35px;
}


/* ========================================
   DELETE FORM
======================================== */

.unit-delete-form {
    display: inline-flex !important;

    align-items: center !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* ========================================
   ACTION BUTTON
======================================== */

.unit-action-btn {
    width: 35px !important;
    height: 35px !important;

    min-width: 35px !important;
    max-width: 35px !important;

    padding: 0 !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    flex: 0 0 35px !important;

    border-radius: 8px !important;

    font-size: 14px;

    line-height: 1;

    text-decoration: none !important;

    cursor: pointer;

    box-sizing: border-box;

    box-shadow: 0 2px 6px rgba(0,0,0,.04);

    transition: all .2s ease;
}

.unit-action-btn i {
    font-size: 14px;
}


/* ========================================
   VIEW
======================================== */

.unit-view-btn {
    background: #e8f1ff !important;

    color: #0d6efd !important;

    border: 1px solid #cfe2ff !important;
}

.unit-view-btn:hover {
    background: #0d6efd !important;

    color: #ffffff !important;

    border-color: #0d6efd !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(13,110,253,.18);
}


/* ========================================
   EDIT
======================================== */

.unit-edit-btn {
    background: #fff4df !important;

    color: #f59f00 !important;

    border: 1px solid #ffe8b3 !important;
}

.unit-edit-btn:hover {
    background: #f59f00 !important;

    color: #ffffff !important;

    border-color: #f59f00 !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(245,159,0,.18);
}


/* ========================================
   DELETE
======================================== */

.unit-delete-btn {
    background: #ffe9e9 !important;

    color: #dc3545 !important;

    border: 1px solid #f5c2c7 !important;
}

.unit-delete-btn:hover {
    background: #dc3545 !important;

    color: #ffffff !important;

    border-color: #dc3545 !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(220,53,69,.18);
}


/* ========================================
   EMPTY STATE
======================================== */

.unit-empty-cell {
    padding: 55px 20px !important;

    text-align: center;

    background: #ffffff !important;
}

.unit-empty-state {
    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;
}

.unit-empty-icon {
    width: 55px;
    height: 55px;

    display: flex;

    align-items: center;
    justify-content: center;

    margin-bottom: 12px;

    border-radius: 50%;

    background: #edf8f1;

    color: #198754;

    border: 1px solid #e1f0e6;

    font-size: 23px;
}

.unit-empty-title {
    color: #495057;

    font-size: 14px;
    font-weight: 700;
}

.unit-empty-text {
    margin-top: 4px;

    color: #9aa0a6;

    font-size: 12px;
}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .unit-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 14px;

        padding: 17px 18px;
    }

    .unit-header-title {
        width: 100%;
    }

    .unit-header-actions {
        width: 100%;

        justify-content: flex-start;
    }

    .unit-table-wrapper {
        padding: 0 12px 18px;
    }

    .unit-responsive-table {
        min-width: 650px;
    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .unit-header {
        padding: 15px;

        gap: 14px;
    }

    .unit-title-row {
        gap: 10px;
    }

    .unit-title-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;

        font-size: 17px;
    }

    .unit-header-title .card-title {
        font-size: 19px;
    }

    .unit-subtitle {
        font-size: 11px;
    }

    .unit-header-actions {
        width: 100%;
    }

    .unit-header-btn {
        width: 100%;

        min-width: 0;

        height: 40px;
    }

    .unit-table-wrapper {
        margin-top: 15px;

        padding: 0 10px 15px;
    }

    .unit-table-responsive {
        border-radius: 10px;
    }

    .unit-responsive-table {
        min-width: 650px;
    }

    .unit-responsive-table thead th {
        padding: 12px !important;

        font-size: 10.5px;
    }

    .unit-responsive-table tbody td {
        padding: 12px !important;

        height: 60px;
    }

    .unit-name {
        font-size: 13px;
    }

    .unit-short-name {
        font-size: 11px;
    }

    .unit-action-btn {
        width: 32px !important;
        height: 32px !important;

        min-width: 32px !important;
        max-width: 32px !important;

        flex-basis: 32px !important;
    }

    .unit-action-btn i {
        font-size: 13px;
    }

    .unit-table-responsive::after {
        content: "← Swipe left / right to view more →";

        display: block;

        padding: 8px 10px;

        text-align: center;

        color: #8a9299;

        background: #fafbfc;

        border-top: 1px solid #edf0f2;

        font-size: 11px;
        font-weight: 500;
    }

}


/* ========================================
   SMALL MOBILE
======================================== */

@media (max-width: 360px) {

    .unit-title-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
    }

    .unit-header {
        padding: 13px;
    }

    .unit-header-title .card-title {
        font-size: 18px;
    }

    .unit-subtitle {
        font-size: 10.5px;
    }

    .unit-responsive-table {
        min-width: 620px;
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
            $canView = hasPermission('unit.view');
            $canEdit = hasPermission('unit.edit');
            $canDelete = hasPermission('unit.delete');
            @endphp

            <!-- ========================================
                UNIT HEADER
            ======================================== -->

            <div class="unit-header">

                <!-- TITLE -->
                <div class="unit-header-title">

                    <div class="unit-title-row">

                        <div class="unit-title-icon">
                            <i class="bi bi-rulers"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Unit
                            </h4>

                            <span class="unit-subtitle">
                                Manage your units and measurement details
                            </span>
                        </div>

                    </div>

                </div>

                <!-- ACTIONS -->
                @if(hasPermission('unit.create'))

                    <div class="unit-header-actions">

                        <a href="{{ route('units.create') }}"
                        class="unit-header-btn unit-add-btn">

                            <span class="unit-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Unit</span>

                        </a>

                    </div>

                @endif

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

            <!-- ========================================
                UNIT TABLE
            ======================================== -->

            <div class="unit-table-wrapper">

                <div class="unit-table-responsive">

                    <table id="batchTable"
                        class="table unit-responsive-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center unit-sr-column">
                                    Sr No
                                </th>

                                <th class="unit-name-column">
                                    Unit Name
                                </th>

                                <th class="unit-short-column">
                                    Short Name
                                </th>

                                @if($canView || $canEdit || $canDelete)

                                    <th class="text-center unit-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>
                        </thead>

                        <tbody>

                            @forelse($units as $unit)

                                <tr class="unit-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="unit-sr-badge">
                                            {{ $loop->iteration }}
                                        </span>

                                    </td>

                                    {{-- Unit Name --}}
                                    <td>

                                        <div class="unit-name-wrapper">

                                            <div class="unit-name-icon">
                                                <i class="bi bi-rulers"></i>
                                            </div>

                                            <span class="unit-name">
                                                {{ $unit->name }}
                                            </span>

                                        </div>

                                    </td>

                                    {{-- Short Name --}}
                                    <td>

                                        <span class="unit-short-name">
                                            {{ $unit->short_name }}
                                        </span>

                                    </td>

                                    {{-- Actions --}}
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center unit-action-cell">

                                            <div class="unit-actions">

                                                @if(hasPermission('unit.view'))

                                                    <a href="{{ route('units.show', $unit->id) }}"
                                                    class="unit-action-btn unit-view-btn"
                                                    title="View Unit">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif

                                                @if(hasPermission('unit.edit'))

                                                    <a href="{{ route('units.edit', $unit->id) }}"
                                                    class="unit-action-btn unit-edit-btn"
                                                    title="Edit Unit">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif

                                                @if(hasPermission('unit.delete'))

                                                    <form action="{{ route('units.destroy', $unit->id) }}"
                                                        method="POST"
                                                        class="unit-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete unit?')"
                                                                class="unit-action-btn unit-delete-btn"
                                                                title="Delete Unit">

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

                                    <td colspan="{{ ($canView || $canEdit || $canDelete) ? 4 : 3 }}"
                                        class="unit-empty-cell">

                                        <div class="unit-empty-state">

                                            <div class="unit-empty-icon">
                                                <i class="bi bi-rulers"></i>
                                            </div>

                                            <div class="unit-empty-title">
                                                No Units Found
                                            </div>

                                            <div class="unit-empty-text">
                                                There are no units available at the moment.
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
                {{ $units->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

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