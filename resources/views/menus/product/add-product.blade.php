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

                <!-- Content wrapper -->
                <div class="content-wrapper">              
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row justify-content-center">
                            <div class="col-12">

                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">
                                        <i class="bx bx-box me-1"></i>
                                        @if ($mode === 'add')
                                        <h4>Add Product</h4>
                                        @elseif ($mode === 'edit')
                                        Edit Product
                                        @else
                                        View Product
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form
                                            action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if (isset($product))
                                            @method('PUT')
                                            @endif

                                            <div class="row">

                                                @if($mode === 'edit' || $mode === 'view')
                                                <input type="hidden" id="selected_category_id" value="{{ $product->category_id }}">
                                                <input type="hidden" id="selected_sub_category_id" value="{{ $product->sub_category_id }}">
                                                @endif

                                                {{-- Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Category <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="category_id" id="category_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Category</option>

                                                            @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                            @endforeach

                                                        </select>
                                                        @error('category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Sub Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Sub Category <span class="text-danger">*</span>
                                                        </label>

                                                        <select name="sub_category_id"
                                                            id="sub_category_id"
                                                            class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Sub Category</option>

                                                            @if ($mode !== 'add')
                                                            @foreach ($subCategories as $sub)
                                                            <option value="{{ $sub->id }}"
                                                                {{ old('sub_category_id', $product->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                                {{ $sub->name }}
                                                            </option>
                                                            @endforeach
                                                            @endif

                                                        </select>

                                                        @error('sub_category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Brand --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Brand <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="brand_id" id="brand_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Brand</option>

                                                        @if($mode !== 'add')
                                                        @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}"
                                                            {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                        @endforeach
                                                        @endif
                                                    </select>

                                                    @error('brand_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                {{-- Product Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Product Name <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ old('name', $product->name ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}
                                                            placeholder="Enter product name">
                                                        @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- SLAG --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Slug</label>
                                                        <input type="text" name="sku" class="form-control "
                                                            placeholder="Enter sku"
                                                            value="{{ old('sku', $product->sku ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}
                                                            placeholder="Enter SKU (optional)" readonly>
                                                        @error('sku')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- BARCODE --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Barcode</label>
                                                        <input type="text" name="barcode" class="form-control "
                                                            placeholder="Enter Barcode"
                                                            value="{{ old('barcode', $product->barcode ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }} 
                                                             maxlength="20"
                                                            >
                                                        {{-- @error('Barcode')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror --}}
                                                    </div>
                                                </div>

                                                {{-- Description --}}
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" class="form-control " placeholder="Enter description" rows="3"
                                                            placeholder="Enter product description" {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('description', $product->description ?? '') }}</textarea>
                                                        @error('description')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Unit --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Unit <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="unit_id"
                                                            class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Unit</option>
                                                            @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}"
                                                                {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                                {{ $unit->name }} ({{ strtoupper($unit->short_name) }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('unit_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Unit Value --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Unit Value <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text"
                                                            step="0.01"
                                                            name="unit_value"
                                                            class="form-control"
                                                            placeholder="e.g. 500, 250, 1, 5"
                                                            value="{{ old('unit_value', $product->unit_value ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('unit_value')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- base Prices --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Base Price / unit<span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" name="base_price"
                                                            class="form-control" placeholder="Enter base price"
                                                            value="{{ old('base_price', $product->base_price ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('base_price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- selling Prices --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Selling Price <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" name="retailer_price"
                                                            class="form-control" placeholder="Enter selling price"
                                                            value="{{ old('retailer_price', $product->retailer_price ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('retailer_price')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- mrp --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            MRP <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" name="mrp"
                                                            class="form-control" placeholder="Enter mrp "
                                                            value="{{ old('mrp', $product->mrp ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('mrp')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- GST --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            GST (%) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="hidden" name="gst_percentage" id="gst_percentage">

                                                        @if($mode === 'view')
                                                        <input type="text"
                                                            class="form-control"
                                                            {{-- value="{{ $product->tax->name ?? '' }} ({{ $product->tax->gst ?? 0 }}%)" --}}
                                                            value="{{ $product->tax->gst ?? 0 }}%"
                                                            readonly>
                                                        @else
                                                        <select name="tax_id" class="form-control">
                                                            <option value="">-- Select GST --</option>

                                                            @foreach($taxes as $tax)
                                                            <option value="{{ $tax->id }}" data-gst="{{ $tax->gst }}"
                                                                {{ old('tax_id', $product->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                                                {{ $tax->name }} ({{ $tax->gst }}%)
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @endif
                                                        @error('tax_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="row mb-2">

                                                    {{-- GST Amount --}}
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold">GST Amount</label>
                                                        <input type="text"
                                                            id="gst_amount"
                                                            class="form-control"
                                                            value="{{ old('gst_amount', number_format($product->gst_amount ?? 0, 2)) }}"
                                                            readonly>
                                                    </div>

                                                    {{-- Final Net Price --}}
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold text-success">Final Net Price</label>
                                                        <input type="text"
                                                            id="final_price"
                                                            class="form-control fw-bold"
                                                            value="{{ old('final_price', number_format($product->final_price ?? 0, 2)) }}"
                                                            readonly>
                                                    </div>

                                                </div>



                                                {{-- Images --}}
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Product Images <span class="text-danger">*</span></label>

                                                    @if ($mode !== 'view')
                                                    <input type="file" name="product_images[]" multiple
                                                        class="form-control @error('product_images') is-invalid @enderror">
                                                    @endif

                                                    @error('product_images')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                    @if (!empty($product->product_images))
                                                    @php
                                                    $images = $product->product_images; // Already array
                                                    $image = $images[0] ?? null;
                                                    @endphp

                                                    @if ($image)
                                                    <img src="{{ asset('storage/products/' . $image) }}" alt="Product Image"
                                                        width="60" height="60" class="rounded border">
                                                    @else
                                                    <span class="text-muted">No Image</span>
                                                    @endif
                                                    @else
                                                    <span class="text-muted">No Image</span>
                                                    @endif
                                                </div>

                                                {{-- Buttons --}}
                                                <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                                    <a href="{{ route('product.index') }}" class="btn btn-success">
                                                        Back
                                                    </a>

                                                    @if ($mode === 'add')
                                                    <button type="submit" class="btn btn-success">
                                                        Save Product
                                                    </button>
                                                    @elseif ($mode === 'edit')
                                                    <button type="submit" class="btn btn-success">
                                                        Update Product
                                                    </button>
                                                    @endif
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                </div>
                <!-- / Content wrapper -->

            </div>
            <!-- / Layout page -->

        </div>
    </div>
    <!-- / Layout wrapper -->
</body>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="sku"]');

        if (!nameInput || !slugInput) return;

        let slugManuallyEdited = false;

        nameInput.addEventListener('input', function() {
            if (!slugManuallyEdited) {
                slugInput.value = generateSlug(this.value);
            }
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });

    //gst input hidden
    $('select[name="tax_id"]').on('change', function() {
        let gst = $(this).find(':selected').data('gst');
        $('#gst_percentage').val(gst);
    });
</script>

<script>
    $(document).ready(function() {

        /* ===============================
           LOAD SUB-CATEGORIES BY CATEGORY
           =============================== */
        $('#category_id').on('change', function() {
            let categoryId = $(this).val();
            let subCategorySelect = $('#sub_category_id');
            let brandSelect = $('#brand_id');

            subCategorySelect.html('<option value="">Loading...</option>');
            brandSelect.html('<option value="">Select Brand</option>');

            if (!categoryId) {
                subCategorySelect.html('<option value="">Select Sub Category</option>');
                return;
            }

            $.get("{{ url('get-sub-categories') }}/" + categoryId, function(data) {
                let options = '<option value="">Select Sub Category</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                subCategorySelect.html(options);
            });
        });


        /* ===============================
           LOAD BRANDS BY SUB-CATEGORY
           =============================== */
        $('#sub_category_id').on('change', function() {
            let subCategoryId = $(this).val();
            let brandSelect = $('#brand_id');

            brandSelect.html('<option value="">Loading...</option>');

            if (!subCategoryId) {
                brandSelect.html('<option value="">Select Brand</option>');
                return;
            }

            $.get("{{ url('get-brands-by-sub-category') }}/" + subCategoryId, function(data) {
                let options = '<option value="">Select Brand</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                brandSelect.html(options);
            });
        });

    });
</script>

<script>
function calculateFinalPrice() {

    const basePrice    = parseFloat($('[name="base_price"]').val()) || 0;
    const sellingPrice = parseFloat($('[name="retailer_price"]').val()) || 0;
    const mrp          = parseFloat($('[name="mrp"]').val()) || 0;

    const gstPercent = parseFloat($('[name="tax_id"] option:selected').data('gst')) || 0;

    if (!sellingPrice || !gstPercent) return;

    const gstAmount  = (sellingPrice * gstPercent) / 100;
    const finalPrice = sellingPrice + gstAmount;

    $('#gst_amount').val(gstAmount.toFixed(2));
    $('#final_price').val(finalPrice.toFixed(2));
}

$(document).ready(function () {

    calculateFinalPrice();

    $('[name="base_price"], [name="retailer_price"], [name="mrp"], [name="tax_id"]')
        .on('input change', calculateFinalPrice);

});
</script>
