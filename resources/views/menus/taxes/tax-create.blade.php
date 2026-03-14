@extends('layouts.app')

@section('content')

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Form controls -->
            <div class="col-xxl-12">
                <div class="card">
                <div class="card-header d-flex align-items-center">
                    <!-- Card Header -->
                    <h4 class="mb-0 flex-grow-1">
                        @if ($mode === 'add')
                        Add Tax
                        @elseif($mode === 'edit')
                        Edit Tax
                        @else
                        View Tax
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <form
                        action="{{ $mode === 'edit' ? route('taxes.update', $tax->id) : route('taxes.store') }}"
                        method="POST">
                        @csrf
                        @if($mode === 'edit')
                        @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-2">
                                <label for="name" class="form-label">Tax Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $tax->name ?? '') }}"
                                    {{ $mode === 'show' ? 'disabled' : '' }}
                                    placeholder="GST 5%">

                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label for="cgst" class="form-label">CGST (%)</label>
                                <input type="text" step="0.01" name="cgst" id="cgst" class="form-control"
                                    value="{{ old('cgst', $tax->cgst ?? '') }}"
                                    {{ $mode === 'show' ? 'disabled' : '' }}
                                    placeholder="2.5">

                                @error('cgst')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="sgst" class="form-label">SGST (%)</label>
                                <input type="text" step="0.01" name="sgst" id="sgst" class="form-control"
                                    value="{{ old('sgst', $tax->sgst ?? '') }}"
                                    {{ $mode === 'show' ? 'disabled' : '' }}
                                    placeholder="2.5">

                                @error('sgst')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="text" step="0.01" name="igst" id="igst" class="form-control"
                                    value="{{ old('igst', $tax->igst ?? '') }}"
                                    {{ $mode === 'show' ? 'disabled' : '' }}
                                    placeholder="5">
                                @error('igst')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="gst" class="form-label">GST (%)</label>
                                <input type="text" step="0.01" name="gst" id="gst" class="form-control"
                                    value="{{ old('gst', $tax->gst ?? '') }}"
                                    {{ $mode === 'show' ? 'disabled' : '' }}
                                    placeholder="5">
                                @error('gst')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="statusToggle" class="form-label mt-1">
                                        Status
                                    </label><br>

                                    <div class="form-check form-switch mt-1">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="statusToggle"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', $tax->is_active ?? 1) ? 'checked' : '' }}
                                            {{ $mode === 'show' ? 'disabled' : '' }}
                                            onchange="toggleStatusLabel()">

                                        <label class="form-check-label" id="statusLabel" for="statusToggle">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                @error('is_active')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="mt-3 text-end">
                            <a href="{{ route('taxes.index') }}" class="btn btn-success">
                                Back
                            </a>

                            @if($mode !== 'show')
                            <button type="submit" class="btn btn-success">
                                {{ $mode === 'edit' ? 'Update Tax' : 'Save Tax' }}
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content wrapper -->
@endsection

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