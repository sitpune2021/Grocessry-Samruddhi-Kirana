@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <!-- Page -->
            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="card shadow-sm border-0 rounded-3">

                            <!-- Header -->
                            <div class="card-header bg-white fw-semibold">
                                @if ($mode === 'add')
                                    <h4>Add Offer</h4>
                                @elseif ($mode === 'edit')
                                    <h4>Edit Offer</h4>
                                @else
                                    <h4>View Offer</h4>
                                @endif
                            </div>

                            <!-- Body -->
                            <div class="card-body">
                                <form
                                    action="{{ $mode === 'edit' ? route('offers.update', $offer->id) : route('offers.store') }}"
                                    method="POST">
                                    @csrf
                                    @if ($mode === 'edit')
                                        @method('PUT')
                                    @endif

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="code" class="form-control"
                                                value="{{ old('code', $offer->code ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>

                                        {{-- Offer Name --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Title<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control "
                                                value="{{ old('title', $offer->title ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                            @error('title')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- Discount Type --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Discount Type</label>
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
                                        </div>
                                        {{-- Discount Value --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Discount Value</label>
                                            <input type="number" step="0.01" name="discount_value"
                                                class="form-control"
                                                value="{{ old('discount_value', $offer->discount_value ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>
                                              {{-- Start Date --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Start Date</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date', $offer->start_date ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>

                                        {{-- End Date --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">End Date</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date', $offer->end_date ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>
                                         <div class="col-md-4">
                                            <label class="form-label fw-medium">Minimum Amount <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="minimum_amount"
                                                class="form-control"
                                                value="{{ old('minimum_amount', $offer->minimum_amount ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Maximum Usage <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="maximum_usage" class="form-control"
                                                value="{{ old('maximum_usage', $offer->maximum_usage ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                        </div>
                                                            {{-- Status --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Status</label>
                                            <select name="status" class="form-control"
                                                {{ $mode === 'view' ? 'disabled' : '' }}>
                                                <option value="1"
                                                    {{ old('status', $offer->status ?? 1) == 1 ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0"
                                                    {{ old('status', $offer->status ?? 1) == 0 ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>
                                        {{-- Category --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Category <span
                                                    class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control"
                                                {{ $mode === 'view' ? 'disabled' : '' }}>
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $offer->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Product --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium">Product <span
                                                    class="text-danger">*</span></label>
                                            <select name="product_id" class="form-control"
                                                {{ $mode === 'view' ? 'disabled' : '' }}>
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ old('product_id', $offer->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                  
                                       

                    
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium">Description</label>
                                            <textarea name="description" class="form-control" rows="3" {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('description', $offer->description ?? '') }}</textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium">Terms & Condition</label>
                                            <textarea name="terms_condition" class="form-control" rows="3" {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('terms_condition', $offer->terms_condition ?? '') }}</textarea>
                                        </div>

                                    </div>

                                    {{-- Buttons --}}
                                    <div class="mt-4 d-flex justify-content-end gap-2">
                                        <a href="{{ route('offers.index') }}" class="btn btn-success">
                                            <i class="bx bx-arrow-back"></i> Back
                                        </a>

                                        @if ($mode === 'add')
                                            <button type="submit" class="btn btn-success">Save Offer</button>
                                        @elseif ($mode === 'edit')
                                            <button type="submit" class="btn btn-success">Update Offer</button>
                                        @endif
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const categorySelect = document.querySelector('select[name="category_id"]');
        const productSelect = document.querySelector('select[name="product_id"]');

        categorySelect.addEventListener('change', function() {

            let categoryId = this.value;

            productSelect.innerHTML = '<option value="">Loading...</option>';

            if (!categoryId) {
                productSelect.innerHTML = '<option value="">Select Product</option>';
                return;
            }

            fetch(`/offer/products-by-category/${categoryId}`)
                .then(response => response.json())
                .then(products => {
                    productSelect.innerHTML = '<option value="">Select Product</option>';

                    products.forEach(product => {
                        productSelect.innerHTML +=
                            `<option value="${product.id}">${product.name}</option>`;
                    });
                })
                .catch(() => {
                    productSelect.innerHTML = '<option value="">No products found</option>';
                });
        });

    });
</script>
