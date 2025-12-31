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

                                                {{-- Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Category <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="category_id" id="category_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Category</option>

                                                            @if($mode !== 'add')
                                                            @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                            @endforeach
                                                            @endif
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

                                                        <select name="sub_category_id" id="sub_category_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Sub Category</option>

                                                            @if($mode !== 'add')
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

                                                {{-- SKU --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">SKU</label>
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



                                                {{-- Effective Date --}}
                                                <!-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Effective Date <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" name="effective_date" class="form-control"
                                                            value="{{ old('effective_date', isset($product) ? \Carbon\Carbon::parse($product->effective_date)->format('Y-m-d') : '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('effective_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> -->

                                                {{-- Expiry Date --}}
                                                <!-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Expiry Date <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" name="expiry_date" class="form-control"
                                                            value="{{ old('expiry_date', isset($product) ? \Carbon\Carbon::parse($product->expiry_date)->format('Y-m-d') : '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('expiry_date')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> -->

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

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Selling Price <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" step="0.01" name="retailer_price"
                                                            class="form-control" placeholder="Enter retailer price"
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
                                                        <input type="number" step="0.01" name="gst_percentage"
                                                            class="form-control" placeholder="Enter gst"
                                                            value="{{ old('gst_percentage', $product->gst_percentage ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('gst_percentage')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Stock --}}
                                                <!-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Stock <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" name="stock" class="form-control"
                                                            placeholder="Enter stock"
                                                            value="{{ old('stock', $product->stock ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('stock')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div> -->

                                                {{-- Images --}}
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Product Images</label>

                                                    @if ($mode !== 'view')
                                                    <input type="file" name="product_images[]" multiple
                                                        class="form-control @error('product_images') is-invalid @enderror">
                                                    @endif

                                                    @error('product_images')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                    @if (!empty($product->product_images))
                                                    <div class="mt-3 d-flex flex-wrap gap-2">
                                                        @foreach (json_decode($product->product_images) as $img)
                                                        <a href="{{ asset('storage/products/' . $img) }}"
                                                            target="_blank"
                                                            class="text-primary text-decoration-underline d-block">
                                                            View
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Buttons --}}
                                                <div class="mt-4 d-flex justify-content-end gap-2">
                                                    <a href="{{ route('product.index') }}"
                                                        class="btn btn-success">
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
document.addEventListener('DOMContentLoaded', function () {

    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="sku"]');

    if (!nameInput || !slugInput) return;

    let slugManuallyEdited = false;

    nameInput.addEventListener('input', function () {
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
</script>

<script>
    $(document).ready(function() {

        $('#brand_id').on('change', function() {

            let brandId = $(this).val();
            let categorySelect = $('#category_id');
            let subCategorySelect = $('#sub_category_id');

            // Reset dropdowns
            categorySelect.html('<option value="">Select Category</option>');
            subCategorySelect.html('<option value="">Select Sub Category</option>');

            if (!brandId) return;

            categorySelect.html('<option value="">Loading...</option>');

            $.ajax({
                url: "{{ url('get-categories-by-brand') }}/" + brandId,
                type: "GET",
                dataType: "json",
                success: function(data) {

                    let options = '<option value="">Select Category</option>';

                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });
                    } else {
                        options += '<option value="">No categories found</option>';
                    }

                    categorySelect.html(options);
                },
                error: function() {
                    categorySelect.html('<option value="">Error loading categories</option>');
                }
            });
        });

        $('#category_id').on('change', function() {

            let categoryId = $(this).val();
            let subCategorySelect = $('#sub_category_id');

            subCategorySelect.html('<option value="">Select Sub Category</option>');

            if (!categoryId) return;

            subCategorySelect.html('<option value="">Loading...</option>');

            $.ajax({
                url: "{{ url('get-sub-categories') }}/" + categoryId,
                type: "GET",
                dataType: "json",
                success: function(data) {

                    let options = '<option value="">Select Sub Category</option>';

                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });
                    } else {
                        options += '<option value="">No sub categories found</option>';
                    }

                    subCategorySelect.html(options);
                },
                error: function() {
                    subCategorySelect.html('<option value="">Error loading sub categories</option>');
                }
            });
        });

    });
</script>