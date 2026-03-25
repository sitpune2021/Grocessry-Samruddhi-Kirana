@extends('layouts.app')
@section('content')
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <div class="col-12">

                            <div class="card mb-4" style="max-width:1200px;margin:auto;">
                                <h4 class="card-header">
                                    @if ($mode === 'add')
                                    Add Batch
                                    @elseif ($mode === 'edit')
                                    Edit Batch
                                    @else
                                    View Batch
                                    @endif
                                </h4>

                                <div class="card-body">
                                    <form method="POST"
                                        action="{{ $mode === 'edit' ? route('batches.update', $batch->id) : ($mode === 'add' ? route('batches.store') : '#') }}"
                                        {{ $mode === 'view' ? 'onsubmit=return false;' : '' }}>

                                        @csrf
                                        @if ($mode === 'edit')
                                        @method('PUT')
                                        @endif

                                        <div class="row g-3 mb-3">

                                            {{-- Warehouse --}}
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    Warehouse <span class="text-danger">*</span>
                                                </label>

                                                <select name="warehouse_id" id="warehouse_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    <option value="">Select Warehouse</option>

                                                    @foreach ($warehouses as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ old('warehouse_id', auth()->user()?->warehouse_id) == $w->id ? 'selected' : '' }}>
                                                        {{ $w->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('warehouse_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Category --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Category <span
                                                        class="text-danger">*</span></label>
                                                <select name="category_id" class="form-select" id="category_id"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Category</option>

                                                    @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $batch->product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('category_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Sub Category --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Sub Category <span
                                                        class="text-danger">*</span></label>
                                                <select name="sub_category_id" id="sub_category_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    <option value="">Select Sub Category</option>

                                                    @foreach ($subCategories as $sub)
                                                        <option value="{{ $sub->id }}"
                                                            {{ old('sub_category_id', $batch->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                            {{ $sub->name }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                                {{-- disabled select submit nahi hota --}}
                                                @if($mode === 'view')
                                                    <input type="hidden" name="sub_category_id" value="{{ $batch->sub_category_id }}">
                                                @endif
                                            </div>

                                            {{-- Product --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Product <span
                                                        class="text-danger">*</span></label>
                                                <select name="product_id" id="product_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Product</option>

                                                </select>
                                                @error('product_id')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror

                                                {{-- View / Edit → Product Image --}}
                                                @if (isset($batch->product->image))
                                                @php
                                                $imgUrl = asset('storage/products/' . $batch->product->image);
                                                @endphp

                                                @if ($mode === 'view')
                                                <a href="{{ $imgUrl }}" target="_blank"
                                                    class="d-block mt-2 text-primary text-decoration-underline">
                                                    View Product Image
                                                </a>
                                                @elseif ($mode === 'edit')
                                                <a href="{{ $imgUrl }}" target="_blank">
                                                    <img src="{{ $imgUrl }}" width="80"
                                                        class="rounded border mt-2">
                                                </a>
                                                @endif
                                                @endif
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    Unit <span class="text-danger">*</span>
                                                </label>

                                                <!-- <select name="unit_id" id="unit_id" class="form-select"
                                                    {{ $readonly ?? false ? 'disabled' : '' }}> -->
                                                    <select name="unit_id" id="unit_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Unit</option>

                                                    @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        {{ old('unit_id', $batch->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('unit_id')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Batch Number --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Batch Number <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="batch_no"
                                                    value="{{ old('batch_no', $batch->batch_no ?? '') }}"
                                                    class="form-control" placeholder="Enter batch no"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('batch_no')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Quantity --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Quantity <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="quantity" id="quantity" readonly
                                                    value="{{ old('quantity', $batch->quantity ?? '') }}"
                                                    class="form-control" placeholder="Enter quantity"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('quantity')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- MFG --}}
                                            <div class="col-md-4">
                                                <label class="form-label">MFG Date<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="mfg_date"
                                                    value="{{ old('mfg_date', $batch->mfg_date ?? '') }}"
                                                    class="form-control" {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('mfg_date')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Expiry --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Expiry Date<span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="expiry_date"
                                                    value="{{ old('expiry_date', $batch->expiry_date ?? '') }}"
                                                    class="form-control " {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('expiry_date')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            
                                        </div>

                                        {{-- Buttons --}}
                                        <div class="d-flex mt-4 justify-content-end gap-2 text-end">
                                            <a href="{{ route('batches.index') }}" class="btn btn-success">
                                                Back
                                            </a>

                                            @if ($mode !== 'view')
                                            <button type="submit" class="btn btn-success">
                                                {{ $mode === 'edit' ? 'Update Batch' : 'Save Batch' }}
                                            </button>
                                            @endif
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- / Content -->
@endSection


@push('scripts')
<script>
$(function () {
 
    console.log("Warehouse script loaded");
 
    /* =========================
       HELPERS
    ========================== */
 
    function getWarehouseId() {
        return $('#warehouse_id').val();
    }
 
    function resetSubCategory() {
        $('#sub_category_id').html('<option value="">Select Sub Category</option>');
    }
 
    function resetProduct() {
        $('#product_id').html('<option value="">Select Product</option>');
        $('#quantity').val('');
    }
 
    /* =========================
       LOAD CATEGORIES
    ========================== */
 
    function loadCategories(wid, selected = '') {
 
        if (!wid) return;
 
        $.get('/ws/categories/' + wid, function (data) {
 
            let html = '<option value="">Select Category</option>';
 
            data.forEach(function (c) {
                html += `<option value="${c.id}" ${selected == c.id ? 'selected' : ''}>${c.name}</option>`;
            });
 
            $('#category_id').html(html);
 
        });
 
    }
 
    /* =========================
       LOAD SUB CATEGORIES
    ========================== */
 
    function loadSubCategories(wid, cid, selected = '') {
 
        if (!wid || !cid) return;
 
        $.get('/ws/subcategories/' + wid + '/' + cid, function (data) {
 
            let html = '<option value="">Select Sub Category</option>';
 
            data.forEach(function (s) {
                html += `<option value="${s.id}" ${selected == s.id ? 'selected' : ''}>${s.name}</option>`;
            });
 
            $('#sub_category_id').html(html);
 
        });
 
    }
 
    /* =========================
       LOAD PRODUCTS
    ========================== */
 
    function loadProducts(wid, sid, selected = '') {
 
        if (!wid || !sid) return;
 
        $.get('/ws/products-by-sub/' + wid + '/' + sid, function (data) {
 
            let html = '<option value="">Select Product</option>';
 
            data.forEach(function (p) {
                html += `<option value="${p.id}" ${selected == p.id ? 'selected' : ''}>${p.name}</option>`;
            });
 
            $('#product_id').html(html);
 
        });
 
    }
 
    /* =========================
       LOAD QUANTITY
    ========================== */
 
    function loadQuantity(wid, pid) {
 
        if (!wid || !pid) return;
 
        $.get('/ws/quantity/' + wid + '/' + pid, function (res) {
 
            $('#quantity').val(res.quantity ?? 0);
 
        });
 
    }
 
    /* =========================
       EVENTS
    ========================== */
 
    $('#warehouse_id').on('change', function () {
 
        let wid = getWarehouseId();
 
        resetSubCategory();
        resetProduct();
 
        loadCategories(wid);
 
    });
 
 
    $('#category_id').on('change', function () {
 
        let wid = getWarehouseId();
        let cid = $(this).val();
 
        resetSubCategory();
        resetProduct();
 
        loadSubCategories(wid, cid);
 
    });
 
 
    $('#sub_category_id').on('change', function () {
 
        let wid = getWarehouseId();
        let sid = $(this).val();
 
        resetProduct();
 
        loadProducts(wid, sid);
 
    });
 
 
    $('#product_id').on('change', function () {
 
        let wid = getWarehouseId();
        let pid = $(this).val();
 
        loadQuantity(wid, pid);
 
    });
 
 
    /* =========================
       AUTO LOAD FOR EDIT
    ========================== */
 
    let mode = @json($mode??'add');
 
    if (mode !== 'add') {
 
        let wid = @json($batch->warehouse_id ?? null);
        let cid = @json($batch->category_id ?? null);
        let sid = @json($batch->sub_category_id ?? null);
        let pid = @json($batch->product_id ?? null);
 
        if (wid) {
 
            $('#warehouse_id').val(wid);
 
            loadCategories(wid, cid);
 
            if (cid) {
                setTimeout(() => loadSubCategories(wid, cid, sid), 300);
            }
 
            if (sid) {
                setTimeout(() => loadProducts(wid, sid, pid), 600);
            }
 
            
 
        }
 
    }
 
});
</script>
@endpush
 