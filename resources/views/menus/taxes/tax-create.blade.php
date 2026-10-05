@extends('layouts.app')

@section('content')

<!-- Content wrapper -->
<div class="content-wrapper">

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="tax-form-card">

            <!-- =========================
                 HEADER
            ========================== -->

            <div class="tax-form-header">

                <div>

                    @if ($mode === 'add')

                        <h4 class="tax-form-title">
                            Add Tax
                        </h4>

                        <p class="tax-form-subtitle">
                            Create a new tax rate
                        </p>

                    @elseif($mode === 'edit')

                        <h4 class="tax-form-title">
                            Edit Tax
                        </h4>

                        <p class="tax-form-subtitle">
                            Update tax information
                        </p>

                    @else

                        <h4 class="tax-form-title">
                            View Tax
                        </h4>

                        <p class="tax-form-subtitle">
                            View tax information
                        </p>

                    @endif

                </div>

            </div>

            <!-- =========================
                 BODY
            ========================== -->

            <div class="tax-form-body">

                <form
                    action="{{ $mode === 'edit' ? route('taxes.update', $tax->id) : route('taxes.store') }}"
                    method="POST">

                    @csrf

                    @if($mode === 'edit')
                        @method('PUT')
                    @endif

                    <!-- =========================
                         FORM FIELDS
                    ========================== -->

                    <div class="tax-form-grid">

                        <!-- Tax Name -->

                        <div class="tax-form-group">

                            <label for="name" class="tax-form-label">

                                Tax Name
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control tax-form-input"
                                value="{{ old('name', $tax->name ?? '') }}"
                                {{ $mode === 'show' ? 'disabled' : '' }}
                                placeholder="GST 5%">

                            @error('name')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        <!-- CGST -->

                        <div class="tax-form-group">

                            <label for="cgst" class="tax-form-label">
                                CGST (%)
                            </label>

                            <input
                                type="text"
                                step="0.01"
                                name="cgst"
                                id="cgst"
                                class="form-control tax-form-input"
                                value="{{ old('cgst', $tax->cgst ?? '') }}"
                                {{ $mode === 'show' ? 'disabled' : '' }}
                                placeholder="2.5">

                            @error('cgst')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        <!-- SGST -->

                        <div class="tax-form-group">

                            <label for="sgst" class="tax-form-label">
                                SGST (%)
                            </label>

                            <input
                                type="text"
                                step="0.01"
                                name="sgst"
                                id="sgst"
                                class="form-control tax-form-input"
                                value="{{ old('sgst', $tax->sgst ?? '') }}"
                                {{ $mode === 'show' ? 'disabled' : '' }}
                                placeholder="2.5">

                            @error('sgst')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        <!-- IGST -->

                        <div class="tax-form-group">

                            <label for="igst" class="tax-form-label">
                                IGST (%)
                            </label>

                            <input
                                type="text"
                                step="0.01"
                                name="igst"
                                id="igst"
                                class="form-control tax-form-input"
                                value="{{ old('igst', $tax->igst ?? '') }}"
                                {{ $mode === 'show' ? 'disabled' : '' }}
                                placeholder="5">

                            @error('igst')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        <!-- GST -->

                        <div class="tax-form-group">

                            <label for="gst" class="tax-form-label">
                                GST (%)
                            </label>

                            <input
                                type="text"
                                step="0.01"
                                name="gst"
                                id="gst"
                                class="form-control tax-form-input"
                                value="{{ old('gst', $tax->gst ?? '') }}"
                                {{ $mode === 'show' ? 'disabled' : '' }}
                                placeholder="5">

                            @error('gst')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        <!-- Status -->

                        <div class="tax-form-group">

                            <label for="statusToggle" class="tax-form-label">
                                Status
                            </label>


                            <div class="tax-status-box">

                                <div class="form-check form-switch tax-status-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="statusToggle"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', $tax->is_active ?? 1) ? 'checked' : '' }}
                                        {{ $mode === 'show' ? 'disabled' : '' }}
                                        onchange="toggleStatusLabel()">


                                    <label
                                        class="form-check-label"
                                        id="statusLabel"
                                        for="statusToggle">

                                        Active

                                    </label>

                                </div>

                            </div>


                            @error('is_active')

                                <div class="text-danger tax-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>

                    <!-- =========================
                         ACTIONS
                    ========================== -->

                    <div class="tax-form-actions">

                        <a
                            href="{{ route('taxes.index') }}"
                            class="btn btn-success tax-back-btn">

                            Back

                        </a>


                        @if($mode !== 'show')

                            <button
                                type="submit"
                                class="btn btn-success tax-submit-btn">

                                {{ $mode === 'edit' ? 'Update Tax' : 'Save Tax' }}

                            </button>

                        @endif

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<style>

/* =====================================================
   TAX FORM CARD
===================================================== */

.tax-form-card {

    width: 100%;

    background: #ffffff;

    border: 1px solid #e9ecef;

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);

}


/* =====================================================
   HEADER
===================================================== */

.tax-form-header {

    display: flex;

    align-items: center;

    padding: 18px 22px;

    background: #ffffff;

    border-bottom: 1px solid #edf0f2;

}


.tax-form-title {

    margin: 0;

    color: #212529;

    font-size: 20px;

    font-weight: 600;

    line-height: 1.3;

}


.tax-form-subtitle {

    margin: 3px 0 0;

    color: #8f969c;

    font-size: 11px;

    font-weight: 400;

    line-height: 1.4;

}


/* =====================================================
   BODY
===================================================== */

.tax-form-body {

    padding: 24px;

}


/* =====================================================
   FORM GRID
===================================================== */

.tax-form-grid {

    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 20px;

    width: 100%;

}


/* =====================================================
   FORM GROUP
===================================================== */

.tax-form-group {

    min-width: 0;

}


/* =====================================================
   LABEL
===================================================== */

.tax-form-label {

    display: block;

    margin-bottom: 7px;

    color: #343a40;

    font-size: 13px;

    font-weight: 600;

}


/* =====================================================
   INPUT
===================================================== */

.tax-form-input {

    width: 100%;

    height: 42px;

    padding: 9px 13px;

    border: 1px solid #dfe3e6;

    border-radius: 7px;

    color: #343a40;

    background: #ffffff;

    font-size: 13px;

    box-shadow: none;

    transition: all 0.2s ease;

}


.tax-form-input::placeholder {

    color: #adb5bd;

    font-size: 12px;

}


.tax-form-input:focus {

    border-color: #198754;

    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.08);

}


.tax-form-input:disabled {

    background: #f8f9fa;

    color: #6c757d;

    cursor: default;

}


/* =====================================================
   ERROR
===================================================== */

.tax-error {

    margin-top: 5px;

    font-size: 11px;

}


/* =====================================================
   STATUS
===================================================== */

.tax-status-box {

    height: 42px;

    display: flex;

    align-items: center;

    padding: 0 13px;

    border: 1px solid #dfe3e6;

    border-radius: 7px;

    background: #ffffff;

}


.tax-status-switch {

    display: flex;

    align-items: center;

    gap: 8px;

    margin: 0 !important;

    padding: 0 !important;

}


.tax-status-switch .form-check-input {

    width: 38px;

    height: 20px;

    margin: 0 !important;

    cursor: pointer;

    box-shadow: none;

}


.tax-status-switch .form-check-input:checked {

    background-color: #198754;

    border-color: #198754;

}


.tax-status-switch .form-check-input:focus {

    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.10);

}


.tax-status-switch .form-check-label {

    color: #495057;

    font-size: 12px;

    font-weight: 500;

    cursor: pointer;

}


/* =====================================================
   ACTIONS
===================================================== */

.tax-form-actions {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 24px;

    padding-top: 18px;

    border-top: 1px solid #edf0f2;

}


/* =====================================================
   BUTTONS
===================================================== */

.tax-back-btn,

.tax-submit-btn {

    min-height: 38px;

    padding: 8px 18px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: 500;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 992px) {

    .tax-form-grid {

        grid-template-columns: repeat(2, 1fr);

        gap: 18px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 576px) {

    .tax-form-header {

        padding: 15px;

    }


    .tax-form-title {

        font-size: 18px;

    }


    .tax-form-subtitle {

        font-size: 11px;

    }


    .tax-form-body {

        padding: 15px;

    }


    .tax-form-grid {

        grid-template-columns: 1fr;

        gap: 17px;

    }


    .tax-form-input {

        height: 40px;

    }


    .tax-status-box {

        height: 40px;

    }


    .tax-form-actions {

        flex-direction: column-reverse;

        align-items: stretch;

        gap: 8px;

    }


    .tax-back-btn,

    .tax-submit-btn {

        width: 100%;

        text-align: center;

    }

}


/* =====================================================
   SMALL MOBILE
===================================================== */

@media (max-width: 400px) {

    .tax-form-body {

        padding: 13px;

    }


    .tax-form-header {

        padding: 14px;

    }

}

</style>


<!-- =========================
     EXISTING SCRIPTS
========================= -->

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const cgstInput = document.getElementById('cgst');
        const sgstInput = document.getElementById('sgst');
        const gstInput = document.getElementById('gst');

        function calculateGST() {
            let cgst = parseFloat(cgstInput.value) || 0;
            let sgst = parseFloat(sgstInput.value) || 0;

            let gst = cgst + sgst;
            gstInput.value = gst.toFixed(2);
        }

        cgstInput.addEventListener('input', calculateGST);
        sgstInput.addEventListener('input', calculateGST);

        // Calculate on page load (edit mode)
        calculateGST();
    });
</script>

<script>
    function toggleStatusLabel() {

        let toggle = document.getElementById("statusToggle");

        let label = document.getElementById("statusLabel");

        if (toggle.checked) {

            label.innerText = "Active";

        } else {

            label.innerText = "Inactive";

        }

    }
</script>

@endpush

@endsection
