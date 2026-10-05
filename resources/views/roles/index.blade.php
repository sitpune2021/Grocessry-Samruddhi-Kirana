@extends('layouts.app')

@section('content')

@php
    $user = auth()->user();
@endphp

<style>
    /* =========================================================
   ROLE PAGE
========================================================= */

.role-table-wrapper {
    width: 100%;
    margin-top: 18px;

    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 15px;

    box-shadow: 0 5px 24px rgba(0, 0, 0, 0.055);

    overflow-x: auto;
    overflow-y: hidden;

    scrollbar-width: thin;
}


/* =========================================================
   TABLE
========================================================= */

.role-table {
    width: 100% !important;

    min-width: 750px;

    margin: 0 !important;

    border-collapse: separate;
    border-spacing: 0;
}


/* =========================================================
   COLUMN WIDTH
========================================================= */

.role-table .sr-column {
    width: 75px !important;
    min-width: 75px !important;

    text-align: center;
}

.role-table .role-name-column {
    width: 25% !important;
    min-width: 180px !important;
}

.role-table .description-column {
    width: auto !important;
    min-width: 260px !important;
}

.role-table .actions-column {
    width: 160px !important;
    min-width: 160px !important;
    max-width: 160px !important;

    text-align: center;
}


/* =========================================================
   HEADER
========================================================= */

.role-table thead th {
    padding: 15px 15px;

    background: #f7f9f8;

    color: #6c757d;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.6px;

    line-height: 1.4;

    border: none !important;
    border-bottom: 1px solid #e9ecef !important;

    white-space: nowrap;
}

.role-table thead th:first-child {
    border-radius: 14px 0 0 0;
}

.role-table thead th:last-child {
    border-radius: 0 14px 0 0;
}


/* =========================================================
   BODY
========================================================= */

.role-table tbody td {
    position: relative;

    padding: 14px 15px;

    background: #ffffff;

    color: #343a40;

    font-size: 13px;

    border-bottom: 1px solid #edf0f2 !important;
    border-top: none !important;

    vertical-align: middle;
}

.role-table tbody tr:last-child td {
    border-bottom: none !important;
}


/* =========================================================
   ROW HOVER
========================================================= */

.role-row {
    transition:
        background-color 0.2s ease,
        transform 0.2s ease;
}

.role-row:hover td {
    background: #f8fcf9;
}

.role-row:hover td:first-child {
    box-shadow: inset 4px 0 0 #198754;
}


/* =========================================================
   SR BADGE
========================================================= */

.sr-badge {
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

.role-row:hover .sr-badge {
    background: #dff3e7;
    transform: scale(1.05);
}


/* =========================================================
   ROLE NAME
========================================================= */

.role-name-wrapper {
    display: flex;

    align-items: center;

    gap: 10px;

    min-width: 0;
}

.role-name-icon {
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
        background-color 0.2s ease;
}

.role-row:hover .role-name-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.06);
}

.role-name {
    display: block;

    color: #212529;

    font-size: 14px;
    font-weight: 650;

    line-height: 1.4;

    word-break: break-word;
}


/* =========================================================
   DESCRIPTION
========================================================= */

.role-description {
    display: block;

    color: #6c757d;

    font-size: 12.5px;
    font-weight: 400;

    line-height: 1.55;

    word-break: break-word;
}


/* =========================================================
   ACTION CELL
========================================================= */

.role-action-cell {
    width: 160px !important;
    min-width: 160px !important;

    padding-left: 8px !important;
    padding-right: 8px !important;

    white-space: nowrap !important;

    text-align: center !important;
}


/* =========================================================
   ACTIONS
========================================================= */

.role-actions {
    display: flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 7px !important;

    flex-wrap: nowrap !important;

    white-space: nowrap !important;
}

.delete-form {
    display: inline-flex !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* =========================================================
   ACTION BUTTON
========================================================= */

.action-btn {
    width: 36px !important;
    height: 36px !important;

    min-width: 36px !important;
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

.action-view {
    background: #eaf2ff !important;
    color: #0d6efd !important;
}

.action-view:hover {
    background: #0d6efd !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(13, 110, 253, 0.22);
}


/* =========================================================
   EDIT
========================================================= */

.action-edit {
    background: #fff4df !important;
    color: #f59f00 !important;
}

.action-edit:hover {
    background: #f59f00 !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(245, 159, 0, 0.22);
}


/* =========================================================
   DELETE
========================================================= */

.action-delete {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
}

.action-delete:hover {
    background: #dc3545 !important;
    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow: 0 5px 12px rgba(220, 53, 69, 0.22);
}


/* =========================================================
   ROLE HEADER
========================================================= */

.role-header {
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
   TITLE
========================================================= */

.role-header-title {
    flex: 1;
    min-width: 0;
}

.role-title-row {
    display: flex;

    align-items: center;

    gap: 12px;
}

.role-title-icon {
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

.role-header:hover .role-title-icon {
    background: #198754;
    color: #ffffff;

    transform: scale(1.04);
}

.role-header-title .card-title {
    margin: 0;

    color: #212529;

    font-size: 20px;
    font-weight: 700;

    line-height: 1.25;
}

.role-header-subtitle {
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

.role-header-actions {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    flex-shrink: 0;
}


/* =========================================================
   ADD ROLE
========================================================= */

.role-add-btn {
    min-width: 125px;
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

.role-add-btn:hover {
    background: #157347;

    color: #ffffff !important;

    transform: translateY(-2px);

    box-shadow:
        0 6px 14px rgba(25, 135, 84, 0.22);
}

.role-add-icon {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    font-size: 14px;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.empty-cell {
    padding: 55px 20px !important;
}

.empty-role {
    color: #6c757d;
}

.empty-icon {
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

.empty-role h6 {
    margin: 0 0 5px;

    color: #343a40;

    font-size: 14px;
    font-weight: 700;
}

.empty-role p {
    margin: 0;

    color: #adb5bd;

    font-size: 12px;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 768px) {

    .role-header {
        flex-direction: column;

        align-items: stretch;

        padding: 15px;

        gap: 13px;
    }

    .role-header-title {
        width: 100%;
    }

    .role-header-actions {
        width: 100%;
    }

    .role-add-btn {
        width: 100%;
    }

    .role-table-wrapper {
        border-radius: 12px;
    }

    .role-table {
        min-width: 750px !important;
    }

    .role-table thead th {
        padding: 13px 12px;

        font-size: 10.5px;
    }

    .role-table tbody td {
        padding: 12px;
    }

    .role-action-cell {
        width: 150px !important;
        min-width: 150px !important;
    }

    .role-name {
        font-size: 13.5px;
    }

    .role-description {
        font-size: 12px;
    }

    .action-btn {
        width: 33px !important;
        height: 33px !important;

        min-width: 33px !important;
        min-height: 33px !important;

        flex-basis: 33px !important;

        font-size: 13px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 480px) {

    .role-header {
        padding: 14px;
    }

    .role-title-icon {
        width: 37px;
        height: 37px;

        font-size: 16px;
    }

    .role-header-title .card-title {
        font-size: 18px;
    }

    .role-header-subtitle {
        font-size: 10.5px;
    }

    .role-add-btn {
        height: 40px;

        font-size: 12px;
    }

    .role-table {
        min-width: 720px !important;
    }

    .role-table thead th {
        padding: 11px 10px;
    }

    .role-table tbody td {
        padding: 11px 10px;
    }

    .sr-badge {
        width: 29px;
        height: 29px;

        font-size: 11px;
    }

    .role-name-icon {
        width: 29px;
        height: 29px;

        font-size: 12px;
    }

    .role-name {
        font-size: 12.5px;
    }

    .role-description {
        font-size: 11.5px;
    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 360px) {

    .role-table {
        min-width: 700px !important;
    }

    .role-title-row {
        gap: 9px;
    }

    .role-title-icon {
        width: 34px;
        height: 34px;

        border-radius: 9px;

        font-size: 15px;
    }

    .role-header-title .card-title {
        font-size: 17px;
    }

    .role-header-subtitle {
        font-size: 10px;
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

            <!-- =========================================================
                ROLE HEADER
            ========================================================= -->

            <div class="role-header">

                <div class="role-header-title">

                    <div class="role-title-row">

                        <div class="role-title-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>
                            <h4 class="card-title">
                                Role
                            </h4>

                            <span class="role-header-subtitle">
                                Manage your roles and permissions
                            </span>
                        </div>

                    </div>

                </div>

                @if(hasPermission('roles.create'))

                    <div class="role-header-actions">

                        <a href="{{ route('roles.create') }}"
                        class="role-add-btn">

                            <span class="role-add-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>
                                Add Role
                            </span>

                        </a>

                    </div>

                @endif

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
                ROLE TABLE
            ========================================================= -->

            <div class="role-table-wrapper">

                <table id="batchTable"
                    class="table role-table align-middle w-100 mb-0">

                    <thead>

                        <tr>

                            <th class="text-center sr-column">
                                Sr No
                            </th>

                            <th class="role-name-column">
                                Role Name
                            </th>

                            <th class="description-column">
                                Description
                            </th>

                            @if(hasPermission('roles.edit'))

                                <th class="text-center actions-column">
                                    Actions
                                </th>

                            @endif

                        </tr>

                    </thead>

                    <tbody>

                        @php
                            $srNo = $roles->total() -
                                    ($roles->currentPage() - 1) *
                                    $roles->perPage();
                        @endphp


                        @forelse ($roles as $role)

                            <tr class="role-row">

                                <!-- SR NO -->
                                <td class="text-center">

                                    <span class="sr-badge">
                                        {{ $loop->iteration }}
                                    </span>

                                </td>


                                <!-- ROLE NAME -->
                                <td>

                                    <div class="role-name-wrapper">

                                        <div class="role-name-icon">
                                            <i class="bi bi-shield"></i>
                                        </div>

                                        <span class="role-name">
                                            {{ $role->name }}
                                        </span>

                                    </div>

                                </td>


                                <!-- DESCRIPTION -->
                                <td>

                                    <span class="role-description">
                                        {{ $role->description ?? '-' }}
                                    </span>

                                </td>


                                <!-- ACTIONS -->
                                @if(hasPermission('roles.edit'))

                                    <td class="text-center role-action-cell">

                                        <div class="role-actions">

                                            @if(hasPermission('roles.show'))

                                                <a href="{{ route('roles.show', $role->id) }}"
                                                class="action-btn action-view"
                                                title="View Role">

                                                    <i class="bi bi-eye"></i>

                                                </a>

                                            @endif


                                            @if(
                                                hasPermission('roles.edit') &&
                                                !($user->role_id == 2 && $role->id == 1)
                                            )

                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                class="action-btn action-edit"
                                                title="Edit Role">

                                                    <i class="bi bi-pencil"></i>

                                                </a>

                                            @endif


                                            @if(
                                                hasPermission('user.delete') &&
                                                !($user->role_id == 2 && $role->id == 1)
                                            )

                                                <form action="{{ route('roles.destroy', $role->id) }}"
                                                    method="POST"
                                                    class="delete-form">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            onclick="return confirm('Delete Role ?')"
                                                            class="action-btn action-delete"
                                                            title="Delete Role">

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

                                <td colspan="{{ hasPermission('roles.edit') ? 4 : 3 }}"
                                    class="text-center empty-cell">

                                    <div class="empty-role">

                                        <div class="empty-icon">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>

                                        <h6>
                                            No Roles Found
                                        </h6>

                                        <p>
                                            No roles are available at the moment.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- =========================================================
                PAGINATION
            ========================================================= -->

            <div class="px-3 py-2">
                {{ $roles->onEachSide(0)->links('pagination::bootstrap-5') }}
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