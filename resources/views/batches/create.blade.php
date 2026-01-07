@include('layouts.header')

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

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
                                                <label class="form-label">Warehouse <span
                                                        class="text-danger">*</span></label>
                                                <select name="warehouse_id" id="warehouse_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Warehouse</option>
                                                    @foreach ($warehouses as $w)

                                                        <option value="{{ $w->id }}"
                                                            {{ old('warehouse_id', $batch->warehouse_id ?? Auth::user()->warehouse_id) == $w->id ? 'selected' : '' }}>
                                                            {{ $w->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
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
                                                <select name="sub_category_id" id="sub_category_id" class="form-select">
                                                    <option value="">Select Sub Category</option>

                                                   
                                                </select>
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

                                                <select name="unit_id" id="unit_id" class="form-select"
                                                    {{ $readonly ?? false ? 'disabled' : '' }}>
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
                                        <div class="d-flex justify-content-end gap-2">
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

            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    let wid = "{{ Auth::user()->warehouse_id }}";

    // Category → Sub Category
    $('#category_id').on('change', function () {
        let cid = $(this).val();
        


        $('#sub_category_id').html('<option value="">Loading...</option>');
        $('#product_id').html('<option value="">Select Product</option>');
        $('#quantity').val('');

        if (!cid) return;

        loadSubCategories(wid, cid);
    });

    // Sub Category → Product
    $('#sub_category_id').on('change', function () {
        let sid = $(this).val();

        $('#product_id').html('<option value="">Loading...</option>');
        $('#quantity').val('');

        if (!sid) return;

        loadProducts(wid, sid);
    });

    // Product → Quantity
    $('#product_id').on('change', function () {
        let pid = $(this).val();

        if (!pid) return;

        loadQuantity(wid, pid);
    });

});

    
    function loadSubCategories(wid, cid, selected = null) {
    return $.get('/ws/subcategories/' + wid + '/' + cid, function (data) {
        let html = '<option value="">Select Sub Category</option>';
        data.forEach(s => {
            html += `<option value="${s.id}" ${selected == s.id ? 'selected' : ''}>${s.name}</option>`;
        });
        $('#sub_category_id').html(html);
    });
}

function loadProducts(wid, sid, selected = null) {
    return $.get('/ws/products-by-sub/' + wid + '/' + sid, function (data) {
        let html = '<option value="">Select Product</option>';
        data.forEach(p => {
            html += `<option value="${p.id}" ${selected == p.id ? 'selected' : ''}>${p.name}</option>`;
        });
        $('#product_id').html(html);
    });
}

    function loadQuantity(wid, pid) {
        return $.get('/ws/quantity/' + wid + '/' + pid, function(res) {
            $('#quantity').val(res.quantity);
        });
    }

    /* CHANGE EVENTS */
    $('#warehouse_id').change(function() {
        loadCategories(this.value);
        $('#sub_category_id').html('<option value="">Select Sub Category</option>');
        $('#product_id').html('<option value="">Select Product</option>');
        $('#quantity').val('');
    });

    $('#category_id').change(function() {
        loadSubCategories($('#warehouse_id').val(), this.value);
    });

    $('#sub_category_id').change(function() {
        loadProducts($('#warehouse_id').val(), this.value);
    });

    $('#product_id').change(function() {
        loadQuantity($('#warehouse_id').val(), this.value);
    });

    /* ✅ EDIT MODE AUTO SELECT (NO setTimeout) */
    $(document).ready(async function() {

        let mode = "{{ $mode }}";
        if (mode !== 'edit') return;

        let wid = "{{ $batch->warehouse_id ?? '' }}";
        let cid = "{{ $batch->category_id ?? '' }}";
        let sid = "{{ $batch->sub_category_id ?? '' }}";
        let pid = "{{ $batch->product_id ?? '' }}";

        $('#warehouse_id').val(wid);

        await loadCategories(wid, cid);
        await loadSubCategories(wid, cid, sid);
        await loadProducts(wid, sid, pid);
        await loadQuantity(wid, pid);
    });
</script>

<script>
    $(document).ready(function() {
        let wid = $('#warehouse_id').val();

        if (wid) {
            loadCategories(wid); // ✅ auto load categories
        }
    });
</script>
