@extends('layouts.app')

@section('content')

<style>
    /* ========================================
   SUPPLIER HEADER
======================================== */

.supplier-header {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 16px 20px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2;

    box-sizing: border-box;
}


/* ========================================
   TITLE
======================================== */

.supplier-header-title {
    flex: 1;
    min-width: 0;
}

.supplier-title-row {
    display: flex;

    align-items: center;

    gap: 12px;
}

.supplier-title-icon {
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

.supplier-title-row:hover .supplier-title-icon {
    background: #198754;

    color: #ffffff;

    transform: scale(1.04);
}


.supplier-header-title .card-title {
    margin: 0 !important;

    color: #212529;

    font-size: 20px;

    font-weight: 700;

    line-height: 1.3;
}

.supplier-subtitle {
    display: block;

    margin-top: 2px;

    color: #8f969c;

    font-size: 11px;

    font-weight: 400;

    line-height: 1.4;
}


/* ========================================
   HEADER ACTION
======================================== */

.supplier-header-actions {
    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    flex-shrink: 0;
}

.supplier-header-btn {
    min-width: 135px;

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

.supplier-header-btn:hover {
    transform: translateY(-2px);

    box-shadow: 0 6px 14px rgba(0,0,0,.08);
}

.supplier-btn-icon {
    width: 22px;
    height: 22px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 6px;

    background: rgba(255,255,255,.14);
}

.supplier-btn-icon i {
    font-size: 12px;
}


/* ========================================
   ADD SUPPLIER
======================================== */

.supplier-add-btn {
    background: #198754 !important;

    border: 1px solid #198754 !important;

    color: #ffffff !important;
}

.supplier-add-btn:hover {
    background: #157347 !important;

    border-color: #157347 !important;

    color: #ffffff !important;
}


/* ========================================
   TABLE WRAPPER
======================================== */

.supplier-table-wrapper {
    width: 100%;

    margin-top: 15px;

    padding: 0 15px 20px;

    box-sizing: border-box;
}


/* ========================================
   TABLE RESPONSIVE
======================================== */

.supplier-table-responsive {
    width: 100%;

    overflow-x: auto;

    overflow-y: hidden;

    -webkit-overflow-scrolling: touch;

    border: 1px solid #e9ecef;

    border-radius: 14px;

    background: #ffffff;

    box-shadow: 0 5px 25px rgba(0,0,0,.06);

    scrollbar-width: thin;
}


/* ========================================
   SCROLLBAR
======================================== */

.supplier-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.supplier-table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;

    border-radius: 10px;
}

.supplier-table-responsive::-webkit-scrollbar-thumb {
    background: #cfd4da;

    border-radius: 10px;
}

.supplier-table-responsive::-webkit-scrollbar-thumb:hover {
    background: #adb5bd;
}


/* ========================================
   MAIN TABLE
======================================== */

.supplier-responsive-table {
    width: 100% !important;

    min-width: 700px;

    margin: 0 !important;

    border-collapse: separate !important;

    border-spacing: 0 !important;

    table-layout: fixed;
}


/* ========================================
   COLUMN WIDTH
======================================== */

.supplier-sr-column {
    width: 75px !important;

    min-width: 75px !important;

    max-width: 75px !important;

    text-align: center !important;
}

.supplier-name-column {
    width: 42% !important;

    min-width: 250px;
}

.supplier-phone-column {
    width: 30% !important;

    min-width: 180px;
}

.supplier-action-column {
    width: 155px !important;

    min-width: 155px !important;

    max-width: 155px !important;

    text-align: center !important;
}


/* ========================================
   TABLE HEADER
======================================== */

.supplier-responsive-table thead th {
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

.supplier-responsive-table tbody td {
    height: 70px;

    padding: 13px 15px !important;

    background: #ffffff;

    color: #495057;

    border: none !important;

    border-bottom: 1px solid #edf0f2 !important;

    font-size: 13px;

    vertical-align: middle !important;
}

.supplier-responsive-table tbody tr:last-child td {
    border-bottom: none !important;
}


/* ========================================
   ROW
======================================== */

.supplier-table-row {
    transition: all .2s ease;
}

.supplier-table-row:hover td {
    background: #f8fcf9 !important;
}

.supplier-table-row:hover td:first-child {
    box-shadow: inset 4px 0 0 #198754;
}


/* ========================================
   SR BADGE
======================================== */

.supplier-sr-badge {
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

.supplier-table-row:hover .supplier-sr-badge {
    background: #198754;

    color: #ffffff;

    transform: scale(1.04);
}


/* ========================================
   SUPPLIER NAME
======================================== */

.supplier-name-wrapper {
    display: flex;

    align-items: center;

    gap: 9px;

    min-width: 0;
}

.supplier-name-icon {
    width: 34px;
    height: 34px;

    min-width: 34px;

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

.supplier-table-row:hover .supplier-name-icon {
    background: #198754;

    color: #ffffff;

    transform: scale(1.05);
}

.supplier-name {
    display: block;

    max-width: 100%;

    color: #212529;

    font-size: 13.5px;

    font-weight: 600;

    line-height: 1.4;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;
}


/* ========================================
   PHONE
======================================== */

.supplier-phone-wrapper {
    display: flex;

    align-items: center;

    gap: 8px;
}

.supplier-phone-icon {
    width: 30px;
    height: 30px;

    min-width: 30px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #f7f9f8;

    color: #6c757d;

    border: 1px solid #e4e8ea;

    font-size: 12px;

    transition: all .2s ease;
}

.supplier-table-row:hover .supplier-phone-icon {
    background: #edf8f1;

    color: #198754;

    border-color: #dceee2;
}

.supplier-phone {
    color: #495057;

    font-size: 13px;

    font-weight: 600;

    white-space: nowrap;
}


/* ========================================
   ACTION CELL
======================================== */

.supplier-action-cell {
    text-align: center !important;

    white-space: nowrap;
}

.supplier-actions {
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

.supplier-delete-form {
    display: inline-flex !important;

    align-items: center !important;

    margin: 0 !important;

    padding: 0 !important;
}


/* ========================================
   ACTION BUTTON
======================================== */

.supplier-action-btn {
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

    box-shadow: 0 2px 6px rgba(0,0,0,.04);

    transition: all .2s ease;
}

.supplier-action-btn i {
    font-size: 14px;
}


/* ========================================
   VIEW
======================================== */

.supplier-view-btn {
    background: #e8f1ff !important;

    color: #0d6efd !important;

    border: 1px solid #cfe2ff !important;
}

.supplier-view-btn:hover {
    background: #0d6efd !important;

    color: #ffffff !important;

    border-color: #0d6efd !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(13,110,253,.18);
}


/* ========================================
   EDIT
======================================== */

.supplier-edit-btn {
    background: #fff4df !important;

    color: #f59f00 !important;

    border: 1px solid #ffe8b3 !important;
}

.supplier-edit-btn:hover {
    background: #f59f00 !important;

    color: #ffffff !important;

    border-color: #f59f00 !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(245,159,0,.18);
}


/* ========================================
   DELETE
======================================== */

.supplier-delete-btn {
    background: #ffe9e9 !important;

    color: #dc3545 !important;

    border: 1px solid #f5c2c7 !important;
}

.supplier-delete-btn:hover {
    background: #dc3545 !important;

    color: #ffffff !important;

    border-color: #dc3545 !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(220,53,69,.18);
}


/* ========================================
   EMPTY STATE
======================================== */

.supplier-empty-cell {
    padding: 55px 20px !important;

    text-align: center;

    background: #ffffff !important;
}

.supplier-empty-state {
    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;
}

.supplier-empty-icon {
    width: 58px;
    height: 58px;

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

.supplier-empty-title {
    color: #495057;

    font-size: 14px;

    font-weight: 700;
}

.supplier-empty-text {
    margin-top: 4px;

    color: #9aa0a6;

    font-size: 12px;
}


/* ========================================
   MOBILE SWIPE
======================================== */

.supplier-table-responsive::after {
    display: none;
}


/* ========================================
   TABLET
======================================== */

@media (max-width: 991px) {

    .supplier-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 14px;

        padding: 17px 18px;
    }

    .supplier-header-title {
        width: 100%;
    }

    .supplier-header-actions {
        width: 100%;

        justify-content: flex-start;
    }

    .supplier-table-wrapper {
        padding: 0 12px 18px;
    }

    .supplier-responsive-table {
        min-width: 700px;
    }

}


/* ========================================
   MOBILE
======================================== */

@media (max-width: 576px) {

    .supplier-header {
        padding: 15px;

        gap: 14px;
    }

    .supplier-title-row {
        gap: 10px;
    }

    .supplier-title-icon {
        width: 38px;
        height: 38px;

        min-width: 38px;

        font-size: 17px;
    }

    .supplier-header-title .card-title {
        font-size: 19px;
    }

    .supplier-subtitle {
        font-size: 11px;
    }

    .supplier-header-actions {
        width: 100%;
    }

    .supplier-header-btn {
        width: 100%;

        min-width: 0;

        height: 40px;
    }

    .supplier-table-wrapper {
        margin-top: 15px;

        padding: 0 10px 15px;
    }

    .supplier-table-responsive {
        border-radius: 10px;
    }

    .supplier-responsive-table {
        min-width: 700px;
    }

    .supplier-responsive-table thead th {
        padding: 12px !important;

        font-size: 10.5px;
    }

    .supplier-responsive-table tbody td {
        padding: 12px !important;

        height: 60px;
    }

    .supplier-name {
        font-size: 13px;
    }

    .supplier-phone {
        font-size: 12px;
    }

    .supplier-action-btn {
        width: 32px !important;

        height: 32px !important;

        min-width: 32px !important;

        max-width: 32px !important;

        flex-basis: 32px !important;
    }

    .supplier-action-btn i {
        font-size: 13px;
    }

    .supplier-table-responsive::after {
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

    .supplier-header {
        padding: 13px;
    }

    .supplier-title-icon {
        width: 36px;
        height: 36px;

        min-width: 36px;
    }

    .supplier-header-title .card-title {
        font-size: 18px;
    }

    .supplier-subtitle {
        font-size: 10.5px;
    }

    .supplier-responsive-table {
        min-width: 650px;
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
            $canView = hasPermission('supplier.view');
            $canEdit = hasPermission('supplier.edit');
            $canDelete = hasPermission('supplier.delete');
            @endphp

            <!-- ========================================
                SUPPLIER HEADER
            ======================================== -->

            <div class="supplier-header">

                <div class="supplier-header-title">

                    <div class="supplier-title-row">

                        <div class="supplier-title-icon">
                            <i class="bi bi-truck"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Supplier
                            </h4>

                            <span class="supplier-subtitle">
                                Manage your suppliers and supplier details
                            </span>
                        </div>

                    </div>

                </div>

                @if(hasPermission('supplier.create'))

                    <div class="supplier-header-actions">

                        <a href="{{ route('supplier.create') }}"
                        class="supplier-header-btn supplier-add-btn">

                            <span class="supplier-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Supplier</span>

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
                SUPPLIER TABLE
            ======================================== -->

            <div class="supplier-table-wrapper">

                <div class="supplier-table-responsive">

                    <table id="batchTable"
                        class="table supplier-responsive-table mb-0">

                        <thead>

                            <tr>

                                <th class="text-center supplier-sr-column">
                                    Sr No
                                </th>

                                <th class="supplier-name-column">
                                    Supplier Name
                                </th>

                                <th class="supplier-phone-column">
                                    Phone
                                </th>

                                @if($canView || $canEdit || $canDelete)

                                    <th class="text-center supplier-action-column">
                                        Actions
                                    </th>

                                @endif

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($suppliers as $index => $item)

                                <tr class="supplier-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="supplier-sr-badge">

                                            {{ $suppliers->firstItem() + $index }}

                                        </span>

                                    </td>


                                    {{-- Supplier Name --}}
                                    <td>

                                        <div class="supplier-name-wrapper">

                                            <div class="supplier-name-icon">
                                                <i class="bi bi-person-badge"></i>
                                            </div>

                                            <span class="supplier-name"
                                                title="{{ $item->supplier_name }}">

                                                {{ $item->supplier_name }}

                                            </span>

                                        </div>

                                    </td>


                                    {{-- Phone --}}
                                    <td>

                                        <div class="supplier-phone-wrapper">

                                            <span class="supplier-phone-icon">
                                                <i class="bi bi-telephone"></i>
                                            </span>

                                            <span class="supplier-phone">
                                                {{ $item->mobile }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- Actions --}}
                                    @if($canView || $canEdit || $canDelete)

                                        <td class="text-center supplier-action-cell">

                                            <div class="supplier-actions">

                                                @if(hasPermission('supplier.view'))

                                                    <a href="{{ route('supplier.show', $item->id) }}"
                                                    class="supplier-action-btn supplier-view-btn"
                                                    title="View Supplier">

                                                        <i class="bi bi-eye"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('supplier.edit'))

                                                    <a href="{{ route('supplier.edit', $item->id) }}"
                                                    class="supplier-action-btn supplier-edit-btn"
                                                    title="Edit Supplier">

                                                        <i class="bi bi-pencil"></i>

                                                    </a>

                                                @endif


                                                @if(hasPermission('supplier.delete'))

                                                    <form action="{{ route('supplier.destroy', $item->id) }}"
                                                        method="POST"
                                                        class="supplier-delete-form">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                onclick="return confirm('Delete supplier?')"
                                                                class="supplier-action-btn supplier-delete-btn"
                                                                title="Delete Supplier">

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
                                        class="supplier-empty-cell">

                                        <div class="supplier-empty-state">

                                            <div class="supplier-empty-icon">

                                                <i class="bi bi-truck"></i>

                                            </div>

                                            <div class="supplier-empty-title">
                                                No Suppliers Found
                                            </div>

                                            <div class="supplier-empty-text">
                                                There are no suppliers available at the moment.
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
                {{ $suppliers->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

@endsection

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
                if (row.cells.length === 1) return;
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });
    });
</script>
@endpush