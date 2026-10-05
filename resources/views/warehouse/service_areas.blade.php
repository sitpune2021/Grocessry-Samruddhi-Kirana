@extends('layouts.app')

@section('title', 'Service Areas')

@section('content')

<style>
    /* =========================================================
   SERVICE AREA PAGE
========================================================= */

.service-area-card {
    border: 1px solid #e9efeb !important;
    border-radius: 13px !important;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.035);
}


/* =========================================================
   HEADER
========================================================= */

.service-area-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 18px 20px;

    background: #ffffff;

    border-bottom: 1px solid #edf1ee;
}

.service-area-title-row {
    display: flex;
    align-items: center;
    gap: 13px;
}

.service-area-title-icon {
    width: 44px;
    height: 44px;
    flex: 0 0 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    background: linear-gradient(
        135deg,
        #e8f7ef,
        #d8f0e3
    );

    color: #198754;

    font-size: 20px;

    box-shadow:
        0 4px 12px rgba(25, 135, 84, 0.10);
}

.service-area-title-row .card-title {
    margin: 0;

    color: #202a24;

    font-size: 19px;
    font-weight: 700;
}

.service-area-subtitle {
    display: block;

    margin-top: 3px;

    color: #7a8580;

    font-size: 12.5px;
}

.service-area-subtitle strong {
    color: #198754;
}


/* =========================================================
   BACK BUTTON
========================================================= */

.service-area-header-actions {
    display: flex;
    align-items: center;
}

.service-area-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    min-height: 40px;

    padding: 0 14px;

    border-radius: 9px;

    background: #f3f7f5;

    color: #198754 !important;

    border: 1px solid #dfeae4;

    text-decoration: none !important;

    font-size: 13px;
    font-weight: 600;

    transition: all 0.2s ease;
}

.service-area-back-btn:hover {
    background: #198754;
    color: #ffffff !important;

    border-color: #198754;

    transform: translateY(-1px);

    box-shadow:
        0 5px 12px rgba(25, 135, 84, 0.16);
}

.service-area-back-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    font-size: 14px;
}


/* =========================================================
   CARD BODY
========================================================= */

.service-area-card-body {
    padding: 20px !important;
    background: #fbfcfb;
}


/* =========================================================
   SECTION TITLE
========================================================= */

.service-area-section-title {
    display: flex;
    align-items: center;

    gap: 11px;
}

.service-area-section-icon {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #eef8f2;
    color: #198754;

    font-size: 16px;
}

.service-area-section-heading {
    color: #354039;

    font-size: 14px;
    font-weight: 700;
}

.service-area-section-text {
    margin-top: 2px;

    color: #8a948f;

    font-size: 11.5px;
}


/* =========================================================
   DC SELECTOR
========================================================= */

.service-area-selector-box {
    padding: 17px;

    margin-bottom: 18px;

    background: #ffffff;

    border: 1px solid #e7eee9;
    border-radius: 11px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.service-area-selector-row {
    margin-top: 14px;
}

.service-area-select-wrapper {
    position: relative;

    max-width: 430px;
}

.service-area-select-icon {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    z-index: 2;

    color: #198754;

    font-size: 15px;

    pointer-events: none;
}

.service-area-select {
    min-height: 42px;

    padding-left: 38px !important;

    border: 1px solid #dfe8e2 !important;
    border-radius: 8px !important;

    color: #465149;

    font-size: 13px;

    box-shadow: none !important;
}

.service-area-select:focus {
    border-color: #198754 !important;

    box-shadow:
        0 0 0 3px rgba(25, 135, 84, 0.09) !important;
}


/* =========================================================
   INFO ALERT
========================================================= */

.service-area-info-alert {
    display: flex;
    align-items: center;

    gap: 11px;

    margin-bottom: 18px;
    padding: 13px 15px;

    background: #f0f7ff;

    border: 1px solid #dbeafa;
    border-radius: 10px;

    color: #41627d;
}

.service-area-info-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #e2f0ff;

    color: #3977c8;
}

.service-area-info-title {
    font-size: 13px;
    font-weight: 700;
}

.service-area-info-text {
    margin-top: 2px;

    font-size: 11.5px;
}


/* =========================================================
   ADD PINCODE BOX
========================================================= */

.service-area-add-box {
    padding: 17px;

    margin-bottom: 18px;

    background: #ffffff;

    border: 1px solid #e7eee9;
    border-radius: 11px;

    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.service-area-add-icon {
    background: #eef8f2;
}

.service-area-add-box form {
    margin-top: 14px;
}

.service-area-add-row {
    display: flex;
    align-items: center;

    gap: 10px;
}

.service-area-input-wrapper {
    position: relative;

    flex: 1;

    max-width: 430px;
}

.service-area-input-icon {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    z-index: 2;

    color: #198754;

    font-size: 15px;

    pointer-events: none;
}

.service-area-pincode-input {
    min-height: 42px;

    padding-left: 38px !important;

    border: 1px solid #dfe8e2 !important;
    border-radius: 8px !important;

    color: #39443e;

    font-size: 13px;

    box-shadow: none !important;
}

.service-area-pincode-input::placeholder {
    color: #a0a9a4;
}

.service-area-pincode-input:focus {
    border-color: #198754 !important;

    box-shadow:
        0 0 0 3px rgba(25, 135, 84, 0.09) !important;
}

.service-area-add-btn {
    min-height: 42px;

    padding: 0 16px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    border: 0;
    border-radius: 8px;

    background: #198754;
    color: #ffffff;

    font-size: 13px;
    font-weight: 600;

    cursor: pointer;

    box-shadow:
        0 4px 10px rgba(25, 135, 84, 0.14);

    transition: all 0.2s ease;
}

.service-area-add-btn:hover {
    background: #157347;

    transform: translateY(-1px);

    box-shadow:
        0 6px 14px rgba(25, 135, 84, 0.20);
}

.service-area-add-btn-icon {
    width: 21px;
    height: 21px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 5px;

    background: rgba(255, 255, 255, 0.16);
}


/* =========================================================
   SUCCESS
========================================================= */

.service-area-success-alert {
    display: flex;
    align-items: center;

    gap: 9px;

    margin-bottom: 18px;
    padding: 11px 14px;

    border-radius: 9px;

    background: #eaf8f0;

    border: 1px solid #d2ecdc;

    color: #198754;

    font-size: 12.5px;
    font-weight: 600;
}

.service-area-success-icon {
    font-size: 16px;
}


/* =========================================================
   LIST BOX
========================================================= */

.service-area-list-box {
    background: #ffffff;

    border: 1px solid #e7eee9;
    border-radius: 11px;

    overflow: hidden;

    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.service-area-list-header {
    padding: 15px 17px;

    border-bottom: 1px solid #edf1ee;
}


/* =========================================================
   TABLE
========================================================= */

.service-area-table-responsive {
    width: 100%;

    overflow-x: auto;

    -webkit-overflow-scrolling: touch;
}

.service-area-table {
    width: 100%;

    min-width: 500px;

    table-layout: fixed;

    border-collapse: separate;
    border-spacing: 0;
}

.service-area-sr-column {
    width: 100px;
}

.service-area-pincode-column {
    width: auto;
}

.service-area-action-column {
    width: 150px;
}

.service-area-table thead th {
    padding: 12px 15px;

    background: #f8faf9;

    border-top: 0;
    border-bottom: 1px solid #e3e9e5;

    color: #66716b;

    font-size: 11.5px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.3px;

    white-space: nowrap;
}

.service-area-table tbody td {
    padding: 12px 15px;

    border-top: 0;
    border-bottom: 1px solid #edf1ee;

    color: #414a45;

    font-size: 13px;

    vertical-align: middle;
}

.service-area-table tbody tr:last-child td {
    border-bottom: 0;
}


/* =========================================================
   ROW HOVER
========================================================= */

.service-area-table-row {
    position: relative;

    transition: background 0.18s ease;
}

.service-area-table-row:hover {
    background: #fbfefc;
}

.service-area-table-row td:first-child {
    position: relative;
}

.service-area-table-row td:first-child::before {
    content: "";

    position: absolute;

    left: 0;
    top: 7px;
    bottom: 7px;

    width: 3px;

    border-radius: 0 4px 4px 0;

    background: #198754;

    opacity: 0;

    transition: opacity 0.18s ease;
}

.service-area-table-row:hover td:first-child::before {
    opacity: 1;
}


/* =========================================================
   SR
========================================================= */

.service-area-sr-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 30px;
    height: 27px;

    padding: 0 8px;

    border-radius: 7px;

    background: #eef8f2;
    color: #198754;

    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   PINCODE
========================================================= */

.service-area-pincode-wrapper {
    display: flex;
    align-items: center;

    gap: 9px;
}

.service-area-pincode-icon {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 8px;

    background: #eef8f2;
    color: #198754;

    font-size: 14px;
}

.service-area-pincode {
    color: #303b34;

    font-size: 14px;
    font-weight: 700;

    letter-spacing: 0.5px;
}


/* =========================================================
   REMOVE BUTTON
========================================================= */

.service-area-delete-form {
    display: inline-flex;

    margin: 0;
}

.service-area-remove-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    min-height: 32px;

    padding: 0 11px;

    border: 1px solid #ffd9dc;
    border-radius: 8px;

    background: #fff0f0;
    color: #dc3545;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition: all 0.18s ease;
}

.service-area-remove-btn:hover {
    background: #dc3545;
    color: #ffffff;

    border-color: #dc3545;

    transform: translateY(-1px);

    box-shadow:
        0 4px 9px rgba(220, 53, 69, 0.17);
}


/* =========================================================
   EMPTY STATE
========================================================= */

.service-area-empty-cell {
    padding: 42px 20px !important;

    text-align: center;
}

.service-area-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-direction: column;
}

.service-area-empty-icon {
    width: 56px;
    height: 56px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 11px;

    border-radius: 15px;

    background: #eef8f2;
    color: #198754;

    font-size: 24px;
}

.service-area-empty-title {
    color: #39443e;

    font-size: 14px;
    font-weight: 700;
}

.service-area-empty-text {
    margin-top: 4px;

    color: #89938e;

    font-size: 12px;
}


/* =========================================================
   ERROR ALERT
========================================================= */

.service-area-alert {
    display: flex;
    align-items: center;

    border-radius: 9px;

    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 767px) {

    .service-area-header {
        align-items: flex-start;
        flex-direction: column;

        padding: 16px;
    }

    .service-area-header-actions {
        width: 100%;
    }

    .service-area-back-btn {
        width: 100%;
    }

    .service-area-title-row {
        align-items: flex-start;
    }

    .service-area-title-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;

        font-size: 18px;
    }

    .service-area-title-row .card-title {
        font-size: 17px;
    }

    .service-area-subtitle {
        font-size: 11.5px;
    }

    .service-area-card-body {
        padding: 14px !important;
    }

    .service-area-add-row {
        align-items: stretch;
        flex-direction: column;
    }

    .service-area-input-wrapper {
        max-width: none;
    }

    .service-area-add-btn {
        width: 100%;
    }

    .service-area-selector-box,
    .service-area-add-box {
        padding: 14px;
    }

    .service-area-select-wrapper {
        max-width: none;
    }
}
</style>

<head>
  <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<div class="container-xxl">

    {{-- ERROR MESSAGE --}}
    @if($errors->any())
        <div class="alert alert-danger service-area-alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card service-area-card mt-4">

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <div class="service-area-header">

            <div class="service-area-header-title">

                <div class="service-area-title-row">

                    <div class="service-area-title-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>

                    <div>

                        <h4 class="card-title">
                            Service Areas
                        </h4>

                        <span class="service-area-subtitle">

                            @if($warehouse)
                                Manage service pincodes for
                                <strong>{{ $warehouse->name }}</strong>
                            @else
                                Manage distribution center service areas
                            @endif

                        </span>

                    </div>

                </div>

            </div>


            <div class="service-area-header-actions">

                <a href="{{ route('warehouse.index') }}"
                   class="service-area-back-btn">

                    <span class="service-area-back-icon">
                        <i class="bi bi-arrow-left"></i>
                    </span>

                    <span>Back</span>

                </a>

            </div>

        </div>


        <div class="card-body service-area-card-body">


            {{-- =====================================================
                 SUPER ADMIN DC SELECTOR
            ====================================================== --}}
            @if(auth()->user()->role_id == 1)

                <div class="service-area-selector-box">

                    <div class="service-area-section-title">

                        <div class="service-area-section-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <div>

                            <div class="service-area-section-heading">
                                Distribution Center
                            </div>

                            <div class="service-area-section-text">
                                Select a distribution center to manage its service areas
                            </div>

                        </div>

                    </div>

                    <form method="GET">

                        <div class="service-area-selector-row">

                            <div class="service-area-select-wrapper">

                                <i class="bi bi-buildings service-area-select-icon"></i>

                                <select name="warehouse_id"
                                        class="form-select service-area-select"
                                        onchange="this.form.submit()">

                                    <option value="">
                                        Select Distribution Center
                                    </option>

                                    @foreach($distributionCenters as $dc)

                                        <option value="{{ $dc->id }}"
                                            {{ optional($warehouse)->id == $dc->id ? 'selected' : '' }}>

                                            {{ $dc->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </form>

                </div>

            @endif


            {{-- =====================================================
                 NO DC SELECTED
            ====================================================== --}}
            @if(!$warehouse)

                <div class="service-area-info-alert">

                    <div class="service-area-info-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <div>

                        <div class="service-area-info-title">
                            Distribution Center Required
                        </div>

                        <div class="service-area-info-text">
                            Please select a Distribution Center to manage service areas.
                        </div>

                    </div>

                </div>

            @endif


            {{-- =====================================================
                 ADD PINCODE
            ====================================================== --}}
            <div class="service-area-add-box">

                <div class="service-area-section-title">

                    <div class="service-area-section-icon service-area-add-icon">
                        <i class="bi bi-pin-map-fill"></i>
                    </div>

                    <div>

                        <div class="service-area-section-heading">
                            Add Service Pincode
                        </div>

                        <div class="service-area-section-text">
                            Add a pincode that will be serviced by this distribution center
                        </div>

                    </div>

                </div>

                <form method="POST"
                      action="{{ route('warehouse.service-areas.store') }}">

                    @csrf

                    <div class="service-area-add-row">

                        <div class="service-area-input-wrapper">

                            <i class="bi bi-geo-alt service-area-input-icon"></i>

                            <input type="text"
                                   name="pincode"
                                   maxlength="6"
                                   class="form-control service-area-pincode-input"
                                   placeholder="Enter 6 digit pincode"
                                   required>

                        </div>


                        <button type="submit"
                                class="service-area-add-btn">

                            <span class="service-area-add-btn-icon">
                                <i class="bi bi-plus-lg"></i>
                            </span>

                            <span>Add Pincode</span>

                        </button>

                    </div>

                </form>

            </div>


            {{-- =====================================================
                 SUCCESS MESSAGE
            ====================================================== --}}
            @if(session('success'))

                <div class="service-area-success-alert">

                    <div class="service-area-success-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            @endif


            {{-- =====================================================
                 PINCODE LIST
            ====================================================== --}}
            <div class="service-area-list-box">

                <div class="service-area-list-header">

                    <div class="service-area-section-title">

                        <div class="service-area-section-icon">
                            <i class="bi bi-list-check"></i>
                        </div>

                        <div>

                            <div class="service-area-section-heading">
                                Service Pincodes
                            </div>

                            <div class="service-area-section-text">
                                Pincodes currently covered by this service area
                            </div>

                        </div>

                    </div>

                </div>

                <div class="service-area-table-responsive">

                    <table class="table service-area-table mb-0">

                        <thead>

                            <tr>

                                <th class="text-center service-area-sr-column">
                                    Sr No
                                </th>

                                <th class="service-area-pincode-column">
                                    Pincode
                                </th>

                                <th class="text-center service-area-action-column">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($pincodes as $i => $pin)

                                <tr class="service-area-table-row">

                                    {{-- Sr No --}}
                                    <td class="text-center">

                                        <span class="service-area-sr-badge">
                                            {{ $i + 1 }}
                                        </span>

                                    </td>


                                    {{-- Pincode --}}
                                    <td>

                                        <div class="service-area-pincode-wrapper">

                                            <div class="service-area-pincode-icon">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>

                                            <span class="service-area-pincode">
                                                {{ $pin->pincode }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- Action --}}
                                    <td class="text-center">

                                        <form method="POST"
                                              action="{{ route('warehouse.service-areas.destroy', $pin->id) }}"
                                              class="service-area-delete-form">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="service-area-remove-btn"
                                                    onclick="return confirm('Are you sure you want to remove this service pincode?')"
                                                    title="Remove Pincode">

                                                <i class="bi bi-trash3"></i>

                                                <span>Remove</span>

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="3"
                                        class="service-area-empty-cell">

                                        <div class="service-area-empty-state">

                                            <div class="service-area-empty-icon">
                                                <i class="bi bi-geo"></i>
                                            </div>

                                            <div class="service-area-empty-title">
                                                No Service Pincodes Added
                                            </div>

                                            <div class="service-area-empty-text">
                                                Add a pincode above to start managing this service area.
                                            </div>

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

</div>

@endsection