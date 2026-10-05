@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   TAX PAGE
========================================================= */

.tax-table-wrapper {
    width: 100%;
    margin-top: 18px;

    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 15px;

    box-shadow: 0 5px 24px rgba(0, 0, 0, 0.055);

    overflow: hidden;
}


/* =========================================================
   RESPONSIVE TABLE
========================================================= */

.tax-table-wrapper .table-responsive {
    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    -webkit-overflow-scrolling: touch;

    scrollbar-width: thin;
}


/* =========================================================
   TABLE
========================================================= */

.tax-responsive-table {
    width: 100% !important;

    min-width: 900px;

    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

.tax-sr-column {
    width: 75px !important;
    min-width: 75px !important;

    text-align: center;
}

.tax-name-column {
    width: 23% !important;
    min-width: 180px !important;
}

.tax-rate-column {
    width: 75px !important;
    min-width: 75px !important;

    text-align: center;
}

.tax-status-column {
    width: 90px !important;
    min-width: 90px !important;

    text-align: center;
}

.tax-action-column {
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;

    text-align: center;
}


/* =========================================================
   HEADER
========================================================= */

.tax-responsive-table thead th {
    height: 52px;

    padding: 14px 13px !important;

    background: #f7f9f8 !important;

    color: #6c757d !important;

    border: none !important;

    border-bottom: 1px solid #e5e9eb !important;

    font-size: 10.5px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 0.6px;

    white-space: nowrap;

    vertical-align: middle;
}

.tax-responsive-table thead th:first-child {
    border-radius: 14px 0 0 0;
}

.tax-responsive-table thead th:last-child {
    border-radius: 0 14px 0 0;
}


/* =========================================================
   BODY
========================================================= */

.tax-responsive-table tbody td {
    height: 62px;

    padding: 13px 13px !important;

    background: #ffffff;

    border: none !important;

    border-bottom: 1px solid #edf0f2 !important;

    vertical-align: middle !important;
}

.tax-responsive-table tbody tr:last-child td {
    border-bottom: none !important;
}


/* =========================================================
   ROW HOVER
========================================================= */

.tax-row {
    transition:
        background-color 0.2s ease;
}

.tax-row:hover td {
    background: #f8fcf9 !important;
}

.tax-row:hover td:first-child {
    box-shadow: inset 4px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.tax-sr-badge {
    width: 32px;
    height: 32px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #edf8f1;

    border: 1px solid #dcefe3;

    color: #198754;

    font-size: 12px;
    font-weight: 700;

    transition:
        transform 0.2s ease,
        background-color 0.2s ease;
}

.tax-row:hover .tax-sr-badge {
    background: #dff3e7;

    transform: scale(1.05);
}


/* =========================================================
   TAX NAME
========================================================= */

.tax-name-wrapper {
    display: flex;

    align-items: center;

    gap: 9px;

    min-width: 0;
}

.tax-name-icon {
    width: 32px;
    height: 32px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #f1f8f3;

    color: #198754;

    font-size: 14px;

    transition:
        transform 0.2s ease,
        background-color 0.2s ease,
        color 0.2s ease;
}

.tax-row:hover .tax-name-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.06);
}

.tax-name {
    color: #212529;

    font-size: 13.5px;
    font-weight: 600;

    line-height: 1.4;

    word-break: break-word;
}


/* =========================================================
   TAX RATE
========================================================= */

.tax-rate {
    min-width: 45px;

    padding: 5px 7px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 7px;

    font-size: 11px;

    font-weight: 700;

    line-height: 1.2;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.tax-row:hover .tax-rate {
    transform: translateY(-1px);
}


/* CGST */

.tax-rate-cgst {
    background: #eef4ff;
    color: #0d6efd;
}


/* SGST */

.tax-rate-sgst {
    background: #f3edff;
    color: #6f42c1;
}


/* GST */

.tax-rate-gst {
    background: #eaf8f0;
    color: #198754;
}


/* IGST */

.tax-rate-igst {
    background: #fff4df;
    color: #d88900;
}


/* =========================================================
   STATUS SWITCH
========================================================= */

.tax-status-switch {
    display: flex !important;

    align-items: center;
    justify-content: center;

    margin: 0 !important;
    padding: 0 !important;
}

.tax-status-switch .form-check-input {
    width: 38px;
    height: 20px;

    margin: 0 !important;

    cursor: pointer;

    box-shadow: none;

    transition:
        background-color 0.2s ease,
        border-color 0.2s ease;
}

.tax-status-switch .form-check-input:checked {
    background-color: #198754;

    border-color: #198754;
}

.tax-status-switch .form-check-input:focus {
    box-shadow:
        0 0 0 3px rgba(25, 135, 84, 0.10);
}


/* =========================================================
   ACTION CELL
========================================================= */

.tax-action-cell {
    width: 150px !important;
    min-width: 150px !important;

    padding-left: 7px !important;
    padding-right: 7px !important;

    text-align: center !important;

    white-space: nowrap !important;
}


/* =========================================================
   ACTIONS
========================================================= */

.tax-actions {
    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 7px !important;

    flex-wrap: nowrap !important;

    white-space: nowrap !important;
}

.tax-delete-form {
    display: inline-flex !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* =========================================================
   ACTION BUTTON
========================================================= */

.tax-action-btn {
    width: 35px !important;
    height: 35px !important;

    min-width: 35px !important;
    min-height: 35px !important;

    padding: 0 !important;

    display: inline-flex !important;

    align-items: center;
    justify-content: center;

    flex: 0 0 35px !important;

    border: none !important;

    border-radius: 9px !important;

    font-size: 14px;

    text-decoration: none !important;

    cursor: pointer;

    transition:
        transform 0.2s ease,
        background-color 0.2s ease,
        color 0.2s ease,
        box-shadow 0.2s ease;
}


/* =========================================================
   VIEW
========================================================= */

.tax-view-btn {
    background: #eaf2ff !important;
    color: #0d6efd !important;
}

.tax-view-btn:hover {
    background: #0d6efd !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 5px 12px rgba(13, 110, 253, 0.20);
}


/* =========================================================
   EDIT
========================================================= */

.tax-edit-btn {
    background: #fff4df !important;
    color: #f59f00 !important;
}

.tax-edit-btn:hover {
    background: #f59f00 !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 5px 12px rgba(245, 159, 0, 0.20);
}


/* =========================================================
   DELETE
========================================================= */

.tax-delete-btn {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
}

.tax-delete-btn:hover {
    background: #dc3545 !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 5px 12px rgba(220, 53, 69, 0.20);
}


/* =========================================================
   TAX HEADER
========================================================= */

.tax-header {
    width: 100%;

    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 17px 20px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2;

    box-sizing: border-box;
}


/* =========================================================
   HEADER TITLE
========================================================= */

.tax-header-title {
    flex: 1;

    min-width: 0;
}

.tax-title-row {
    display: flex;

    align-items: center;

    gap: 12px;
}

.tax-title-icon {
    width: 40px;
    height: 40px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: #eaf7ef;

    color: #198754;

    font-size: 18px;

    transition:
        transform 0.2s ease,
        background-color 0.2s ease;
}

.tax-header:hover .tax-title-icon {
    background: #198754;

    color: #ffffff;

    transform: scale(1.04);
}

.tax-header-title .card-title {
    margin: 0;

    color: #212529;

    font-size: 20px;

    font-weight: 700;

    line-height: 1.25;
}

.tax-header-subtitle {
    display: block;

    margin-top: 3px;

    color: #9aa1a7;

    font-size: 11.5px;

    font-weight: 400;

    line-height: 1.4;
}


/* =========================================================
   HEADER ACTION
========================================================= */

.tax-header-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    flex-shrink: 0;
}


/* =========================================================
   ADD TAX BUTTON
========================================================= */

.tax-add-btn {
    min-width: 120px;

    height: 39px;

    padding: 0 15px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    border-radius: 8px;

    background: #198754;

    border: 1px solid #198754;

    color: #ffffff !important;

    font-size: 12.5px;

    font-weight: 600;

    text-decoration: none !important;

    white-space: nowrap;

    transition:
        transform 0.2s ease,
        background-color 0.2s ease,
        box-shadow 0.2s ease;
}

.tax-add-btn:hover {
    background: #157347;

    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 6px 14px rgba(25, 135, 84, 0.22);
}

.tax-add-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    font-size: 14px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.tax-empty-cell {
    padding: 55px 20px !important;
}

.tax-empty-state {
    color: #6c757d;
}

.tax-empty-icon {
    width: 62px;
    height: 62px;

    margin: 0 auto 14px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 17px;

    background: #f1f8f3;

    color: #198754;

    font-size: 27px;
}

.tax-empty-state h6 {
    margin: 0 0 5px;

    color: #343a40;

    font-size: 14px;

    font-weight: 700;
}

.tax-empty-state p {
    margin: 0;

    color: #adb5bd;

    font-size: 12px;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 768px) {

    .tax-header {
        flex-direction: column;

        align-items: stretch;

        padding: 15px;

        gap: 13px;
    }

    .tax-header-title {
        width: 100%;
    }

    .tax-header-actions {
        width: 100%;
    }

    .tax-add-btn {
        width: 100%;
    }

    .tax-table-wrapper {
        border-radius: 12px;
    }

    .tax-responsive-table {
        min-width: 900px !important;
    }

    .tax-responsive-table thead th {
        height: 48px;

        padding: 12px !important;

        font-size: 10px;
    }

    .tax-responsive-table tbody td {
        height: 58px;

        padding: 11px 12px !important;
    }

    .tax-action-column,
    .tax-action-cell {
        width: 145px !important;
        min-width: 145px !important;
    }

    .tax-actions {
        gap: 6px !important;
    }

    .tax-action-btn {
        width: 32px !important;
        height: 32px !important;

        min-width: 32px !important;
        min-height: 32px !important;

        flex-basis: 32px !important;

        font-size: 13px;
    }

    .tax-name {
        font-size: 13px;
    }

    .tax-rate {
        font-size: 10.5px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 480px) {

    .tax-header {
        padding: 14px;
    }

    .tax-title-icon {
        width: 37px;
        height: 37px;

        border-radius: 10px;

        font-size: 16px;
    }

    .tax-header-title .card-title {
        font-size: 18px;
    }

    .tax-header-subtitle {
        font-size: 10.5px;
    }

    .tax-add-btn {
        height: 40px;

        font-size: 12px;
    }

    .tax-responsive-table {
        min-width: 850px !important;
    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 360px) {

    .tax-responsive-table {
        min-width: 820px !important;
    }

    .tax-title-row {
        gap: 9px;
    }

    .tax-title-icon {
        width: 34px;
        height: 34px;

        border-radius: 9px;

        font-size: 15px;
    }

    .tax-header-title .card-title {
        font-size: 17px;
    }

    .tax-header-subtitle {
        font-size: 10px;
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
        
            <!-- =========================================================
                TAX HEADER
            ========================================================= -->

            <div class="tax-header">

                <div class="tax-header-title">

                    <div class="tax-title-row">

                        <div class="tax-title-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>

                        <div>

                            <h4 class="card-title">
                                Taxes
                            </h4>

                            <span class="tax-header-subtitle">
                                Manage your tax rates and configurations
                            </span>

                        </div>

                    </div>

                </div>

                <div class="tax-header-actions">

                    <a href="{{ route('taxes.create') }}"
                    class="tax-add-btn">

                        <span class="tax-add-icon">
                            <i class="bi bi-plus-lg"></i>
                        </span>

                        <span>
                            Add Tax
                        </span>

                    </a>

                </div>

            </div>

            <!-- Search -->
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

            <!-- =========================================================
                TAX MANAGEMENT TABLE
            ========================================================= -->

            <div class="tax-table-wrapper">

                <div class="table-responsive">

                    <table id="taxTable"
                        class="table tax-responsive-table align-middle w-100 mb-0">

                        <thead>

                            <tr>

                                <th class="text-center tax-sr-column">
                                    Sr No
                                </th>

                                <th class="tax-name-column">
                                    Tax Name
                                </th>

                                <th class="text-center tax-rate-column">
                                    CGST
                                </th>

                                <th class="text-center tax-rate-column">
                                    SGST
                                </th>

                                <th class="text-center tax-rate-column">
                                    GST
                                </th>

                                <th class="text-center tax-rate-column">
                                    IGST
                                </th>

                                <th class="text-center tax-status-column">
                                    Status
                                </th>

                                <th class="text-center tax-action-column">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($taxes as $key => $tax)

                                <tr class="tax-row">

                                    <!-- SR NO -->
                                    <td class="text-center">

                                        <span class="tax-sr-badge">
                                            {{ $key + 1 }}
                                        </span>

                                    </td>


                                    <!-- TAX NAME -->
                                    <td>

                                        <div class="tax-name-wrapper">

                                            <div class="tax-name-icon">
                                                <i class="bi bi-percent"></i>
                                            </div>

                                            <span class="tax-name">
                                                {{ $tax->name }}
                                            </span>

                                        </div>

                                    </td>


                                    <!-- CGST -->
                                    <td class="text-center">

                                        <span class="tax-rate tax-rate-cgst">
                                            {{ $tax->cgst }}%
                                        </span>

                                    </td>


                                    <!-- SGST -->
                                    <td class="text-center">

                                        <span class="tax-rate tax-rate-sgst">
                                            {{ $tax->sgst }}%
                                        </span>

                                    </td>


                                    <!-- GST -->
                                    <td class="text-center">

                                        <span class="tax-rate tax-rate-gst">
                                            {{ $tax->gst }}%
                                        </span>

                                    </td>


                                    <!-- IGST -->
                                    <td class="text-center">

                                        <span class="tax-rate tax-rate-igst">
                                            {{ $tax->igst }}%
                                        </span>

                                    </td>


                                    <!-- STATUS -->
                                    <td class="text-center">

                                        <form action="{{ route('updatestatus') }}"
                                            method="POST">

                                            @csrf

                                            <input type="hidden"
                                                name="id"
                                                value="{{ $tax->id }}">

                                            <div class="form-check form-switch tax-status-switch">

                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="statusSwitch{{ $tax->id }}"
                                                    name="status"
                                                    value="1"
                                                    onchange="this.form.submit()"
                                                    {{ $tax->is_active == 1 ? 'checked' : '' }}>

                                            </div>

                                        </form>

                                    </td>


                                    <!-- ACTIONS -->
                                    <td class="text-center tax-action-cell">

                                        <div class="tax-actions">

                                            <!-- VIEW -->
                                            <a href="{{ route('taxes.show', $tax->id) }}"
                                            class="tax-action-btn tax-view-btn"
                                            title="View Tax">

                                                <i class="bi bi-eye"></i>

                                            </a>


                                            <!-- EDIT -->
                                            <a href="{{ route('taxes.edit', $tax->id) }}"
                                            class="tax-action-btn tax-edit-btn"
                                            title="Edit Tax">

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            <!-- DELETE -->
                                            <form action="{{ route('taxes.destroy', $tax->id) }}"
                                                method="POST"
                                                class="tax-delete-form">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Delete tax?')"
                                                    class="tax-action-btn tax-delete-btn"
                                                    title="Delete Tax">

                                                    <i class="bi bi-trash3"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td colspan="8"
                                        class="text-center tax-empty-cell">

                                        <div class="tax-empty-state">

                                            <div class="tax-empty-icon">
                                                <i class="bi bi-receipt"></i>
                                            </div>

                                            <h6>
                                                No Tax Records Found
                                            </h6>

                                            <p>
                                                No tax records are available at the moment.
                                            </p>

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

    <!-- Pagination -->
    <div class="px-3 py-2">
        {{ $taxes->onEachSide(0)->links('pagination::bootstrap-5') }}
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
        const table = document.getElementById("taxTable");

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