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

                                                        <select name="sub_category_id" id="sub_category_id"
                                                            class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Sub Category</option>

                                                            @if ($mode !== 'add')
                                                            @foreach ($subCategories as $sub)
                                                            <option value="{{ $sub->id }}">
                                                                {{ old('sub_category_id', $product->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}
                                                                {{ $sub->name }}
                                                            </option>
                                                            @endforeach
                                                            @endif

                                                            {{-- @foreach ($subCategories as $sub)
                                                            <option value="{{ $sub->id }}"
                                                            {{ old('sub_category_id', $product->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                            {{ $sub->name }}
                                                            </option>
                                                            @endforeach --}}

                                                        </select>

                                                        @error('sub_category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
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
                                                        <label class="form-label">Slag</label>
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

                                                {{-- Brand --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Brand <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="brand_id" id="brand_id" class="form-select"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Brand</option>
                                                        @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}"
                                                            {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
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

                                                {{-- Prices --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Base Price <span class="text-danger">*</span>
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
                                                {{-- Discount Type --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Discount type</label>
                                                    <select name="discount_type" class="form-control"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="percentage"
                                                            {{ old('discount_type', $offer->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                                            Percentage (%)
                                                        </option>
                                                        <option value="flat"
                                                            {{ old('discount_type', $offer->discount_type ?? '') == 'flat' ? 'selected' : '' }}>
                                                            Flat Amount
                                                        </option>
                                                    </select>
                                                    @error('discount_type')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Discount Value --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Discount Value</label>
                                                    <input type="number" step="0.01" name="discount_value"
                                                        class="form-control" placeholder="Enter discount value"
                                                        value="{{ old('discount_value', $product->discount_value ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('discount_value')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
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
                                                            value="{{ $product->tax->name ?? '' }} ({{ $product->tax->igst ?? 0 }}%)"
                                                            readonly>
                                                        @else
                                                        <select name="tax_id" class="form-control" required>
                                                            <option value="">-- Select GST --</option>

                                                            @foreach($taxes as $tax)
                                                            <option value="{{ $tax->id }}"
                                                                {{ old('tax_id', $product->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                                                {{ $tax->name }} ({{ $tax->igst }}%)
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @endif
                                                        @error('tax_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
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
                                                <div class="mt-4 d-flex justify-content-end gap-2">
                                                    <a href="{{ route('product.index') }}" class="btn btn-success">
                                                        <i class="bx bx-arrow-back"></i> Back
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

        let categorySelect = $('#category_id');

        let subCategorySelect = $('#sub_category_id');

        let selectedCategoryId = $('#selected_category_id').val();

        let selectedSubCategoryId = $('#selected_sub_category_id').val();


        $.ajax({
            url: "{{ url('get-categories') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let options = '<option value="">Select Category</option>';
                $.each(data, function(key, value) {
                    let selected = value.id == selectedCategoryId ? 'selected' : '';
                    options += `<option value="${value.id}" ${selected}>${value.name}</option>`;
                });
                categorySelect.html(options);
                // If edit mode → load sub categories
                if (selectedCategoryId) {
                    loadSubCategories(selectedCategoryId, selectedSubCategoryId);
                }
            },
            error: function() {
                categorySelect.html('<option value="">Select Category</option>');
            }
        });

        categorySelect.on('change', function() {
            let categoryId = $(this).val();
            subCategorySelect.html('<option value="">Select Sub Category</option>');
            if (!categoryId) return;
            loadSubCategories(categoryId, null);
        });

        function loadSubCategories(categoryId, selectedSubCatId = null) {
            subCategorySelect.html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ url('get-sub-categories') }}/" + categoryId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let options = '<option value="">Select Sub Category</option>';
                    $.each(data, function(key, value) {
                        let selected = value.id == selectedSubCatId ? 'selected' : '';
                        options += `<option value="${value.id}" ${selected}>${value.name}</option>`;
                    });
                    subCategorySelect.html(options);
                },
                error: function() {
                    subCategorySelect.html('<option value="">Select Sub Category</option>');
                }
            });
        }
    });
</script>