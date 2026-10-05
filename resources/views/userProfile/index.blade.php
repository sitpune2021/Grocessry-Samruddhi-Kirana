@extends('layouts.app')

@section('content')

<style>

/* =========================================================
   USER MANAGEMENT - PREMIUM RESPONSIVE UI
========================================================= */

.user-table-wrapper {
    width: 100%;
    margin-top: 18px;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* =========================================================
   HEADER
========================================================= */

.user-header {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 22px;
    background: #fff;
    border-bottom: 1px solid #edf0f2;
}

.user-header-title {
    flex: 1;
    min-width: 0;
}

.user-header-title .card-title {
    margin: 0;
    color: #212529;
    font-size: 21px;
    font-weight: 700;
    line-height: 1.3;
}

.user-header-subtitle {
    display: block;
    margin-top: 4px;
    color: #8a9299;
    font-size: 12px;
}

.user-header-actions {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.user-add-btn {
    min-height: 40px;
    padding: 9px 17px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.12);
    transition: all .2s ease;
}

.user-add-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(25, 135, 84, 0.18);
}

/* =========================================================
   TABLE RESPONSIVE
========================================================= */

.user-table-wrapper .table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

.user-table-wrapper .table-responsive::-webkit-scrollbar {
    height: 7px;
}

.user-table-wrapper .table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;
}

.user-table-wrapper .table-responsive::-webkit-scrollbar-thumb {
    background: #cbd3d8;
    border-radius: 10px;
}

.user-responsive-table {
    width: 100% !important;
    min-width: 1100px;
    margin: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

/* =========================================================
   COLUMN WIDTHS
========================================================= */

.user-sr-column {
    width: 70px !important;
    min-width: 70px !important;
}

.user-photo-column {
    width: 85px !important;
    min-width: 85px !important;
}

.user-name-column {
    width: 17% !important;
    min-width: 170px !important;
}

.user-contact-column {
    width: 135px !important;
    min-width: 135px !important;
}

.user-email-column {
    width: 225px !important;
    min-width: 225px !important;
}

.user-warehouse-column {
    width: 165px !important;
    min-width: 165px !important;
}

.user-role-column {
    width: 125px !important;
    min-width: 125px !important;
}

.user-status-column {
    width: 100px !important;
    min-width: 100px !important;
}

.user-action-column {
    width: 170px !important;
    min-width: 170px !important;
    max-width: 170px !important;
}

/* =========================================================
   TABLE HEADER
========================================================= */

.user-responsive-table thead th {
    height: 52px;
    padding: 14px 15px !important;
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

.user-responsive-table thead th:first-child {
    border-radius: 15px 0 0 0;
}

.user-responsive-table thead th:last-child {
    border-radius: 0 15px 0 0;
}

/* =========================================================
   TABLE BODY
========================================================= */

.user-responsive-table tbody td {
    height: 66px;
    padding: 13px 15px !important;
    background: #fff;
    border: none !important;
    border-bottom: 1px solid #edf0f2 !important;
    vertical-align: middle !important;
}

.user-responsive-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* =========================================================
   ROW HOVER
========================================================= */

.user-row {
    transition: all .2s ease;
}

.user-row:hover td {
    background: #f9fcfa !important;
}

.user-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}

/* =========================================================
   SR BADGE
========================================================= */

.user-sr-badge {
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
   PROFILE IMAGE
========================================================= */

.user-profile-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 11px;
    border: 1px solid #e4e8eb;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    transition: all .2s ease;
}

.user-profile-img:hover {
    transform: scale(1.08);
    box-shadow: 0 5px 12px rgba(0, 0, 0, .10);
}

.user-profile-placeholder {
    width: 40px;
    height: 40px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #f1f8f3;
    color: #198754;
    font-size: 19px;
    border: 1px solid #e3eee7;
}

/* =========================================================
   USER NAME
========================================================= */

.user-name {
    display: block;
    color: #212529;
    font-size: 14px;
    font-weight: 650;
    max-width: 210px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* =========================================================
   NORMAL TEXT
========================================================= */

.user-text {
    display: block;
    color: #555e65;
    font-size: 13px;
    font-weight: 500;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* =========================================================
   ROLE BADGE
========================================================= */

.user-role {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 8px;
    background: #f1f3f5;
    color: #495057;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

/* =========================================================
   STATUS SWITCH
========================================================= */

.user-status-switch {
    display: flex !important;
    align-items: center;
    justify-content: center;
    margin: 0 !important;
    padding: 0 !important;
}

.user-status-switch .form-check-input {
    width: 40px;
    height: 21px;
    margin: 0 !important;
    cursor: pointer;
    box-shadow: none;
}

.user-status-switch .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.user-status-switch .form-check-input:focus {
    box-shadow: 0 0 0 3px rgba(25, 135, 84, .10);
}

/* =========================================================
   ACTION AREA
========================================================= */

.user-actions {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
}

.user-delete-form {
    display: inline-flex !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* =========================================================
   ACTION BUTTON
========================================================= */

.user-action-btn {
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

.user-action-btn:hover {
    transform: translateY(-2px);
}

/* =========================================================
   VIEW
========================================================= */

.user-view-btn {
    background: #e9f2ff !important;
    color: #0d6efd !important;
}

.user-view-btn:hover {
    background: #0d6efd !important;
    color: #fff !important;
}

/* =========================================================
   EDIT
========================================================= */

.user-edit-btn {
    background: #fff3dc !important;
    color: #f59f00 !important;
}

.user-edit-btn:hover {
    background: #f59f00 !important;
    color: #fff !important;
}

/* =========================================================
   DELETE
========================================================= */

.user-delete-btn {
    background: #ffe9e9 !important;
    color: #dc3545 !important;
}

.user-delete-btn:hover {
    background: #dc3545 !important;
    color: #fff !important;
}

/* =========================================================
   EMPTY STATE
========================================================= */

.user-empty-cell {
    padding: 55px 20px !important;
}

.user-empty-state {
    color: #6c757d;
}

.user-empty-icon {
    width: 68px;
    height: 68px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: #f1f8f3;
    color: #198754;
    font-size: 28px;
}

.user-empty-state h6 {
    margin-bottom: 5px;
    color: #343a40;
    font-weight: 700;
}

.user-empty-state p {
    margin: 0;
    color: #adb5bd;
    font-size: 13px;
}

/* =========================================================
   SEARCH AREA
========================================================= */

.card-header {
    background: #fff !important;
    border: none !important;
}

/* =========================================================
   TABLET
========================================================= */

@media (max-width: 768px) {

    .user-header {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
        gap: 13px;
    }

    .user-header-title {
        width: 100%;
    }

    .user-header-title .card-title {
        font-size: 19px;
    }

    .user-header-actions {
        width: 100%;
    }

    .user-add-btn {
        width: 100%;
        min-height: 42px;
    }

    .user-table-wrapper {
        border-radius: 12px;
        margin-top: 14px;
    }

    .user-responsive-table {
        min-width: 1100px !important;
    }

    .user-responsive-table thead th {
        height: 48px;
        padding: 12px !important;
        font-size: 10px;
    }

    .user-responsive-table tbody td {
        height: 60px;
        padding: 11px 12px !important;
    }

    .user-action-column {
        width: 160px !important;
        min-width: 160px !important;
        max-width: 160px !important;
    }

    .user-name {
        font-size: 13px;
    }

    .user-text {
        font-size: 12px;
    }

    .user-action-btn {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
    }
}

/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 480px) {

    .user-header {
        padding: 14px;
    }

    .user-header-title .card-title {
        font-size: 18px;
    }

    .user-header-subtitle {
        font-size: 11px;
    }

    .user-responsive-table {
        min-width: 1050px !important;
    }

    .user-table-wrapper .table-responsive::after {
        content: "← Swipe to view more →";
        display: block;
        padding: 7px 10px;
        text-align: center;
        font-size: 10px;
        color: #8a9299;
        background: #fafbfb;
        border-top: 1px solid #edf0f2;
    }

}

/* =========================================================
   PAGINATION
========================================================= */

.user-pagination-wrapper {
    padding: 15px 20px;
    background: #fff;
    border-top: 1px solid #edf0f2;
}

/* =========================================================
   USER HEADER TITLE
========================================================= */

.user-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-title-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background: #edf8f1;
    color: #198754;
    font-size: 19px;
    border: 1px solid #e1f0e6;
}

.user-title-row .card-title {
    margin: 0;
    color: #212529;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
}


/* =========================================================
   ADD USER BUTTON
========================================================= */

.user-add-btn {
    min-height: 40px;
    padding: 8px 15px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid #198754 !important;
    border-radius: 9px;
    background: #198754 !important;
    color: #fff !important;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none !important;
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.12);
    transition: all .2s ease;
}

.user-add-btn:hover {
    background: #157347 !important;
    border-color: #157347 !important;
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 7px 16px rgba(25, 135, 84, 0.20);
}

.user-add-icon {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: rgba(255,255,255,.16);
}

.user-add-icon i {
    font-size: 12px;
}


/* =========================================================
   USER NAME WITH ICON
========================================================= */

.user-name-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.user-name-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f1f8f3;
    color: #198754;
    font-size: 14px;
    transition: all .2s ease;
}

.user-row:hover .user-name-icon {
    background: #198754;
    color: #fff;
    transform: scale(1.05);
}


/* =========================================================
   CONTACT / EMAIL / WAREHOUSE
========================================================= */

.user-text {
    color: #596168;
    font-size: 12.5px;
    font-weight: 500;
}


/* =========================================================
   ROLE BADGE
========================================================= */

.user-role {
    padding: 6px 10px;
    border: 1px solid #e7eaec;
    background: #f5f7f8;
    color: #495057;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    transition: all .2s ease;
}

.user-row:hover .user-role {
    background: #edf8f1;
    border-color: #d8ecdf;
    color: #198754;
}


/* =========================================================
   ACTION CELL
========================================================= */

.user-action-cell {
    white-space: nowrap;
}

.user-actions {
    min-height: 35px;
}


/* =========================================================
   ACTION BUTTON SHADOW
========================================================= */

.user-action-btn {
    box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
}

.user-action-btn:hover {
    box-shadow: 0 5px 12px rgba(0, 0, 0, .10);
}


/* =========================================================
   RESPONSIVE HEADER
========================================================= */

@media (max-width: 768px) {

    .user-title-row {
        gap: 10px;
    }

    .user-title-icon {
        width: 38px;
        height: 38px;
        font-size: 17px;
    }

    .user-title-row .card-title {
        font-size: 18px;
    }

}

@media (max-width: 480px) {

    .user-title-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        font-size: 16px;
    }

    .user-title-row .card-title {
        font-size: 17px;
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
            $canView = hasPermission('user.view');
            $canEdit = hasPermission('user.edit');
            $canDelete = hasPermission('user.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                
                <!-- User Management Header -->
                <div class="user-header">

                    <div class="user-header-title">

                        <div class="user-title-row">

                            <div class="user-title-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>

                            <div>
                                <h4 class="card-title">
                                    Users
                                </h4>

                                <span class="user-header-subtitle">
                                    Manage your users and account access
                                </span>
                            </div>

                        </div>

                    </div>

                    <div class="user-header-actions">

                        <a href="{{ route('user.create') }}"
                        class="user-add-btn">

                            <span class="user-add-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add User</span>

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
                    }, 10000);
                </script>
                @endif

                <!-- User Management Table -->
                <div class="user-table-wrapper">

                    <div class="table-responsive">

                        <table id="batchTable"
                            class="table user-responsive-table align-middle w-100 mb-0">

                            <thead>
                                <tr>

                                    <th class="text-center user-sr-column">
                                        Sr No
                                    </th>

                                    <th class="text-center user-photo-column">
                                        Logo
                                    </th>

                                    <th class="user-name-column">
                                        User Name
                                    </th>

                                    <th class="user-contact-column">
                                        Contact
                                    </th>

                                    <th class="user-email-column">
                                        Email
                                    </th>

                                    <th class="user-warehouse-column">
                                        Warehouse
                                    </th>

                                    <th class="user-role-column">
                                        Role
                                    </th>

                                    <th class="text-center user-status-column">
                                        Status
                                    </th>

                                    @if($canView || $canEdit || $canDelete)
                                        <th class="text-center user-action-column">
                                            Action
                                        </th>
                                    @endif

                                </tr>
                            </thead>

                            <tbody>

                                @forelse($users as $key => $user)

                                    <tr class="user-row">

                                        <!-- Sr No -->
                                        <td class="text-center">

                                            <span class="user-sr-badge">
                                                {{ $key + 1 }}
                                            </span>

                                        </td>

                                        <!-- Profile Photo -->
                                        <td class="text-center">

                                            @if($user->profile_photo)

                                                <a href="{{ asset('storage/' . $user->profile_photo) }}"
                                                target="_blank">

                                                    <img
                                                        src="{{ asset('storage/' . $user->profile_photo) }}"
                                                        alt="{{ $user->first_name }}"
                                                        class="user-profile-img">

                                                </a>

                                            @else

                                                <div class="user-profile-placeholder">
                                                    <i class="bi bi-person"></i>
                                                </div>

                                            @endif


                                        </td>

                                        <!-- User Name -->
                                        <td>

                                            <span class="user-name">
                                                {{ $user->first_name }}
                                            </span>

                                        </td>

                                        <!-- Contact -->
                                        <td>

                                            <span class="user-text">
                                                {{ $user->mobile ?? '-' }}
                                            </span>

                                        </td>

                                        <!-- Email -->
                                        <td>

                                            <span class="user-text">
                                                {{ $user->email ?? '-' }}
                                            </span>

                                        </td>

                                        <!-- Warehouse -->
                                        <td>

                                            <span class="user-text">
                                                {{ $user->warehouse->name ?? '-' }}
                                            </span>

                                        </td>

                                        <!-- Role -->
                                        <td>

                                            <span class="user-role">
                                                {{ $user->role->name ?? '-' }}
                                            </span>

                                        </td>

                                        <!-- Status -->
                                        <td class="text-center">

                                            <form action="{{ route('userstatus') }}"
                                                method="POST">

                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="{{ $user->id }}">

                                                <div class="form-check form-switch user-status-switch">

                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="userStatus{{ $user->id }}"
                                                        name="status"
                                                        value="1"
                                                        onchange="this.form.submit()"
                                                        {{ $user->status == 1 ? 'checked' : '' }}>

                                                </div>

                                            </form>

                                        </td>

                                        <!-- Actions -->
@if($canView || $canEdit || $canDelete)

    <td class="text-center user-action-cell">

        <div class="user-actions">

            @if(hasPermission('user.view'))

                <a href="{{ route('user.show', $user->id) }}"
                   class="user-action-btn user-view-btn"
                   title="View User">

                    <i class="bi bi-eye"></i>

                </a>

            @endif


            @if(hasPermission('user.edit'))

                <a href="{{ route('user.edit', $user->id) }}"
                   class="user-action-btn user-edit-btn"
                   title="Edit User">

                    <i class="bi bi-pencil"></i>

                </a>

            @endif


            @if(hasPermission('user.delete'))

                <form action="{{ route('user.destroy', $user->id) }}"
                      method="POST"
                      class="user-delete-form">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            onclick="return confirm('Delete User ?')"
                            class="user-action-btn user-delete-btn"
                            title="Delete User">

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
                                            colspan="{{ ($canView || $canEdit || $canDelete) ? 9 : 8 }}"
                                            class="text-center user-empty-cell">

                                            <div class="user-empty-state">

                                                <div class="user-empty-icon">
                                                    <i class="bi bi-people"></i>
                                                </div>

                                                <h6>
                                                    No users found
                                                </h6>

                                                <p>
                                                    No user records are available.
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
                    {{ $users->onEachSide(0)->links('pagination::bootstrap-5') }}
                </div>

            </div>

        </div>
    </div>

@endsection

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