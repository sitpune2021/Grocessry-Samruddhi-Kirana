@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
   DELIVERY AGENT
========================================================= */

.delivery-agent-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px 18px;
    background: linear-gradient(
        180deg,
        #ffffff 0%,
        #fbfffd 100%
    );
}

.delivery-agent-title-row {
    display: flex;
    align-items: center;
    gap: 14px;
}

.delivery-agent-title-icon {
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: linear-gradient(
        135deg,
        rgba(25, 135, 84, 0.16),
        rgba(25, 135, 84, 0.05)
    );
    color: #198754;
    font-size: 21px;
}

.delivery-agent-header .card-title {
    color: #263238;
    font-size: 20px;
    font-weight: 700;
}

.delivery-agent-subtitle {
    display: block;
    margin-top: 4px;
    color: #8b9690;
    font-size: 12px;
    font-weight: 500;
}

/* ================= HEADER BUTTON ================= */

.delivery-agent-header-actions {
    display: flex;
    align-items: center;
}

.delivery-agent-add-btn {
    min-height: 42px;
    padding: 0 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 10px;
    color: #fff !important;
    background: linear-gradient(
        135deg,
        #198754,
        #157347
    );
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 5px 14px rgba(25, 135, 84, 0.18);
    transition: all 0.2s ease;
}

.delivery-agent-add-btn:hover {
    color: #fff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(25, 135, 84, 0.25);
}

.delivery-agent-btn-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    background: rgba(255, 255, 255, 0.16);
}

/* ================= TABLE ================= */

.delivery-agent-table-wrapper {
    margin: 8px 20px 20px;
    border: 1px solid #e7eee9;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
}

.delivery-agent-table-responsive {
    width: 100%;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #b8d8c8 #f5f8f6;
}

.delivery-agent-table-responsive::-webkit-scrollbar {
    height: 7px;
}

.delivery-agent-table-responsive::-webkit-scrollbar-track {
    background: #f5f8f6;
}

.delivery-agent-table-responsive::-webkit-scrollbar-thumb {
    background: #b8d8c8;
    border-radius: 10px;
}

.delivery-agent-responsive-table {
    width: 100%;
    min-width: 1200px;
    table-layout: fixed;
    margin: 0 !important;
}

/* ================= TABLE HEADER ================= */

.delivery-agent-responsive-table thead th {
    height: 52px;
    padding: 12px 13px;
    background: #f7faf8 !important;
    border-bottom: 1px solid #e3ebe6 !important;
    color: #52605a;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.35px;
    vertical-align: middle;
    white-space: nowrap;
}

/* ================= TABLE BODY ================= */

.delivery-agent-responsive-table tbody td {
    height: 66px;
    padding: 10px 13px;
    border-bottom: 1px solid #edf2ef !important;
    color: #4f5b56;
    font-size: 13px;
    font-weight: 500;
    vertical-align: middle;
    background: #fff;
}

.delivery-agent-responsive-table tbody tr:last-child td {
    border-bottom: 0 !important;
}

/* ================= COLUMN WIDTHS ================= */

.delivery-agent-sr-column {
    width: 70px;
    min-width: 70px;
    max-width: 70px;
}

.delivery-agent-shop-column {
    width: 210px;
    min-width: 190px;
}

.delivery-agent-name-column {
    width: 210px;
    min-width: 190px;
}

.delivery-agent-mobile-column {
    width: 150px;
    min-width: 140px;
}

.delivery-agent-email-column {
    width: 235px;
    min-width: 200px;
}

.delivery-agent-photo-column {
    width: 120px;
    min-width: 120px;
}

.delivery-agent-status-column {
    width: 120px;
    min-width: 120px;
}

.delivery-agent-action-column {
    width: 145px;
    min-width: 145px;
}

/* ================= ROW HOVER ================= */

.delivery-agent-table-row {
    transition: background-color 0.18s ease;
}

.delivery-agent-table-row:hover td {
    background: #f8fcfa !important;
}

.delivery-agent-table-row:hover td:first-child {
    box-shadow: inset 3px 0 0 #198754;
}

/* ================= SR BADGE ================= */

.delivery-agent-sr-badge {
    min-width: 30px;
    height: 30px;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f0f7f3;
    color: #198754;
    font-size: 12px;
    font-weight: 700;
}

/* ================= SHOP ================= */

.delivery-agent-shop-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.delivery-agent-shop-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eef7f2;
    color: #198754;
}

.delivery-agent-shop-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #3f4c46;
    font-weight: 600;
}

/* ================= AGENT NAME ================= */

.delivery-agent-name-wrapper {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.delivery-agent-avatar {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eaf7ef;
    color: #198754;
}

.delivery-agent-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #3f4c46;
    font-weight: 600;
}

/* ================= CONTACT ================= */

.delivery-agent-contact-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

.delivery-agent-contact-icon {
    width: 28px;
    height: 28px;
    flex: 0 0 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #eef7f2;
    color: #198754;
    font-size: 12px;
}

.delivery-agent-contact-icon.email {
    background: #eef4ff;
    color: #0d6efd;
}

.delivery-agent-email {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* ================= PROFILE PHOTO ================= */

.delivery-agent-photo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.delivery-agent-photo {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e3eee8 !important;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
}

.delivery-agent-no-photo {
    width: 42px;
    height: 42px;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #f3f5f4;
    color: #9aa49f;
    font-size: 18px;
}

/* ================= STATUS ================= */

.delivery-agent-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 82px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
}

.delivery-agent-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.delivery-agent-status.active {
    color: #198754;
    background: #eaf7ef;
}

.delivery-agent-status.inactive {
    color: #dc3545;
    background: #fdecef;
}

/* ================= ACTIONS ================= */

.delivery-agent-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.delivery-agent-delete-form {
    display: inline-flex;
    margin: 0 !important;
}

.delivery-agent-action-btn {
    width: 32px;
    height: 32px;
    padding: 0 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    font-size: 14px;
    text-decoration: none !important;
    transition: all 0.18s ease;
}

/* View */
.delivery-agent-view-btn {
    color: #0d6efd !important;
    background: #eaf2ff !important;
    border-color: #b8d3ff !important;
}

.delivery-agent-view-btn:hover {
    color: #fff !important;
    background: #0d6efd !important;
    transform: translateY(-1px);
}

/* Edit */
.delivery-agent-edit-btn {
    color: #a66a00 !important;
    background: #fff5dc !important;
    border-color: #f2d48d !important;
}

.delivery-agent-edit-btn:hover {
    color: #fff !important;
    background: #f0ad00 !important;
    transform: translateY(-1px);
}

/* Delete */
.delivery-agent-delete-btn {
    color: #dc3545 !important;
    background: #fff1f2 !important;
    border-color: #f5c2c7 !important;
}

.delivery-agent-delete-btn:hover {
    color: #fff !important;
    background: #dc3545 !important;
    transform: translateY(-1px);
}

/* ================= EMPTY STATE ================= */

.delivery-agent-empty-cell {
    padding: 55px 20px !important;
    text-align: center !important;
}

.delivery-agent-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.delivery-agent-empty-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border-radius: 16px;
    background: #f0f7f3;
    color: #198754;
    font-size: 25px;
}

.delivery-agent-empty-title {
    color: #44514b;
    font-size: 15px;
    font-weight: 700;
}

.delivery-agent-empty-text {
    margin-top: 4px;
    color: #98a29d;
    font-size: 12px;
}

/* ================= RESPONSIVE ================= */

@media (max-width: 991.98px) {

    .delivery-agent-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .delivery-agent-header-actions {
        width: 100%;
    }

    .delivery-agent-add-btn {
        width: 100%;
    }

    .delivery-agent-table-wrapper {
        margin: 8px 12px 15px;
    }
}

@media (max-width: 575.98px) {

    .delivery-agent-header {
        padding: 18px 15px 15px;
    }

    .delivery-agent-title-row {
        gap: 10px;
    }

    .delivery-agent-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        font-size: 18px;
        border-radius: 11px;
    }

    .delivery-agent-header .card-title {
        font-size: 17px;
    }

    .delivery-agent-subtitle {
        font-size: 11px;
    }

    .delivery-agent-table-wrapper {
        margin: 5px 8px 10px;
        border-radius: 11px;
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
            $canView = hasPermission('delivery_agent.view');
            $canEdit = hasPermission('delivery_agent.edit');
            $canDelete = hasPermission('delivery_agent.delete');
            @endphp

            <!-- ================= HEADER ================= -->
            <div class="delivery-agent-header">

                <div class="delivery-agent-header-title">

                    <div class="delivery-agent-title-row">

                        <div class="delivery-agent-title-icon">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>

                        <div>
                            <h4 class="card-title mb-0">
                                Delivery Agent
                            </h4>

                            <span class="delivery-agent-subtitle">
                                Manage delivery agents and assigned shops
                            </span>
                        </div>

                    </div>

                </div>

                @php
                    $user = auth()->user();
                    $isDC = optional($user->warehouse)->type === 'distribution_center';
                @endphp

                @if($isDC && !in_array($user->role_id, [1,2]))

                    <div class="delivery-agent-header-actions">

                        <a href="{{ route('delivery-agents.create') }}"
                        class="delivery-agent-add-btn">

                            <span class="delivery-agent-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Agent</span>

                        </a>

                    </div>

                @endif

            </div>

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

        <!-- ================= TABLE ================= -->
        <div class="delivery-agent-table-wrapper">

            <div class="delivery-agent-table-responsive">

                <table id="driverVehicleTable"
                    class="table delivery-agent-responsive-table mb-0">

                    <thead>
                        <tr>

                            <th class="text-center delivery-agent-sr-column">
                                Sr No
                            </th>

                            <th class="delivery-agent-shop-column">
                                Shop Name
                            </th>

                            <th class="delivery-agent-name-column">
                                Agent Name
                            </th>

                            <th class="delivery-agent-mobile-column">
                                Mobile
                            </th>

                            <th class="delivery-agent-email-column">
                                Email
                            </th>

                            <th class="text-center delivery-agent-photo-column">
                                Profile Photo
                            </th>

                            <th class="text-center delivery-agent-status-column">
                                Status
                            </th>

                            @if($canView || $canEdit || $canDelete)

                                <th class="text-center delivery-agent-action-column">
                                    Actions
                                </th>

                            @endif

                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($agents as $index => $agent)

                            <tr class="delivery-agent-table-row">

                                <!-- SR NO -->
                                <td class="text-center">

                                    <span class="delivery-agent-sr-badge">
                                        {{ $agents->firstItem() + $index }}
                                    </span>

                                </td>


                                <!-- SHOP NAME -->
                                <td>

                                    <div class="delivery-agent-shop-wrapper">

                                        <div class="delivery-agent-shop-icon">
                                            <i class="bi bi-shop"></i>
                                        </div>

                                        <span class="delivery-agent-shop-name"
                                            title="{{ $agent->shop->name ?? '-' }}">

                                            {{ $agent->shop->name ?? '-' }}

                                        </span>

                                    </div>

                                </td>


                                <!-- AGENT NAME -->
                                <td>

                                    <div class="delivery-agent-name-wrapper">

                                        <div class="delivery-agent-avatar">

                                            <i class="bi bi-person"></i>

                                        </div>

                                        <span class="delivery-agent-name"
                                            title="{{ $agent->user ? $agent->user->first_name . ' ' . ($agent->user->last_name ?? '') : '-' }}">

                                            {{ $agent->user
                                                ? $agent->user->first_name . ' ' . ($agent->user->last_name ?? '')
                                                : '-' }}

                                        </span>

                                    </div>

                                </td>


                                <!-- MOBILE -->
                                <td>

                                    <div class="delivery-agent-contact-wrapper">

                                        <span class="delivery-agent-contact-icon">
                                            <i class="bi bi-telephone"></i>
                                        </span>

                                        <span>
                                            {{ $agent->user->mobile ?? '-' }}
                                        </span>

                                    </div>

                                </td>


                                <!-- EMAIL -->
                                <td>

                                    <div class="delivery-agent-contact-wrapper">

                                        <span class="delivery-agent-contact-icon email">
                                            <i class="bi bi-envelope"></i>
                                        </span>

                                        <span class="delivery-agent-email"
                                            title="{{ $agent->user->email ?? '-' }}">

                                            {{ $agent->user->email ?? '-' }}

                                        </span>

                                    </div>

                                </td>


                                <!-- PROFILE PHOTO -->
                                <td class="text-center">

                                    @if (!empty($agent->user->profile_photo))

                                        <div class="delivery-agent-photo-wrapper">

                                            <img src="{{ asset('storage/profile_photos/' . $agent->user->profile_photo) }}"
                                                alt="Profile Photo"
                                                class="delivery-agent-photo">

                                        </div>

                                    @else

                                        <div class="delivery-agent-no-photo">
                                            <i class="bi bi-person"></i>
                                        </div>

                                    @endif

                                </td>


                                <!-- STATUS -->
                                <td class="text-center">

                                    @if ($agent->status)

                                        <span class="delivery-agent-status active">
                                            <span class="delivery-agent-status-dot"></span>
                                            Active
                                        </span>

                                    @else

                                        <span class="delivery-agent-status inactive">
                                            <span class="delivery-agent-status-dot"></span>
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <!-- ACTIONS -->
                                @if($canView || $canEdit || $canDelete)

                                    <td class="text-center">

                                        <div class="delivery-agent-actions">

                                            @if(hasPermission('delivery_agent.view'))

                                                <a href="{{ route('delivery-agents.show', $agent->id) }}"
                                                class="delivery-agent-action-btn delivery-agent-view-btn"
                                                title="View Agent">

                                                    <i class="bi bi-eye"></i>

                                                </a>

                                            @endif


                                            @if(hasPermission('delivery_agent.edit'))

                                                <a href="{{ route('delivery-agents.edit', $agent->id) }}"
                                                class="delivery-agent-action-btn delivery-agent-edit-btn"
                                                title="Edit Agent">

                                                    <i class="bi bi-pencil"></i>

                                                </a>

                                            @endif


                                            @if(hasPermission('delivery_agent.delete'))

                                                <form action="{{ route('delivery-agents.destroy', $agent->id) }}"
                                                    method="POST"
                                                    class="delivery-agent-delete-form">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            onclick="return confirm('Delete delivery_agent?')"
                                                            class="delivery-agent-action-btn delivery-agent-delete-btn"
                                                            title="Delete Agent">

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

                                <td colspan="{{ ($canView || $canEdit || $canDelete) ? 8 : 7 }}"
                                    class="delivery-agent-empty-cell">

                                    <div class="delivery-agent-empty-state">

                                        <div class="delivery-agent-empty-icon">
                                            <i class="bi bi-person-badge"></i>
                                        </div>

                                        <div class="delivery-agent-empty-title">
                                            No Delivery Agents Found
                                        </div>

                                        <div class="delivery-agent-empty-text">
                                            There are no delivery agents available at the moment.
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
            {{ $agents->onEachSide(0)->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>
@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const searchInput = document.getElementById("dt-search-1");
            const table = document.getElementById("driverVehicleTable");

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