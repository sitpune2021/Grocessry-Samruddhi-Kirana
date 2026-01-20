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
                                <form method="POST"
                                    action="{{ $mode === 'edit'
                                                ? route('offers.update', $offer->id)
                                                : ($mode === 'add' ? route('offers.store') : '#')
                                    }}">
                                    @csrf
                                    @if($mode === 'edit')
                                    @method('PUT')
                                    @endif

                                    <div class="row">
                                        {{-- Offer Title --}}
                                        <div class="col-md-6 mb-3">
                                            <label>Offer Title<span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ old('title', $offer->title ?? '') }}"
                                                {{ $mode === 'view' ? 'readonly' : '' }}>
                                            @error('title')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror

                                        </div>

                                        {{-- Offer Type --}}
                                        <div class="col-md-6 mb-3">
                                            <label>Offer Type <span class="text-danger">*</span></label>

                                            <select name="offer_type"
                                                class="form-select"
                                                {{ $mode === 'view' ? 'disabled' : '' }}>

                                                <option value="">Select Offer Type</option>

                                                <option value="flat_discount"
                                                    {{ old('offer_type', $offer->offer_type ?? '') == 'flat_discount' ? 'selected' : '' }}>
                                                    Flat Discount
                                                </option>

                                                <option value="percentage"
                                                    {{ old('offer_type', $offer->offer_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                                    Percentage Discount
                                                </option>

                                                <option value="buy_x_get_y"
                                                    {{ old('offer_type', $offer->offer_type ?? '') == 'buy_x_get_y' ? 'selected' : '' }}>
                                                    Buy X Get Y
                                                </option>

                                            </select>

                                            @error('offer_type')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- Warehouse -
                                    {{-- <div class="mb-3">
                                            <label>Warehouse</label>
                                            <select name="warehouse_id" class="form-select" required>
                                                <option value="">Select Warehouse</option>
                                                @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">
                                    {{ $warehouse->name }}
                                    </option>
                                    @endforeach
                                    </select>
                            </div> --}}


                            {{-- Buy X Get Y Section --}}
                            <div id="bxgySection"
                                style="display: {{ old('offer_type', $offer->offer_type ?? '') === 'buy_x_get_y' ? 'block' : 'none' }}">

                                <div class="row">

                                    {{-- Buy Quantity --}}
                                    <div class="col-md-6 mb-3">
                                        <label>Buy Quantity (X) <span class="text-danger">*</span></label>

                                        <input type="number"
                                            name="buy_quantity"
                                            class="form-control"
                                            value="{{ old('buy_quantity', $offer->buy_quantity ?? '') }}"
                                            {{ $mode === 'view' ? 'readonly' : '' }}>

                                        @error('buy_quantity')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Get Quantity --}}
                                    <div class="col-md-6 mb-3">
                                        <label>Get Quantity (Y) <span class="text-danger">*</span></label>

                                        <input type="number"
                                            name="get_quantity"
                                            class="form-control"
                                            value="{{ old('get_quantity', $offer->get_quantity ?? '') }}"
                                            {{ $mode === 'view' ? 'readonly' : '' }}>

                                        @error('get_quantity')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            {{-- <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Buy Product <span class="text-danger">*</span></label>
                                                <select name="buy_product_id"
                                                    class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}
                            required>
                            <option value="">Select Product</option>

                            @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ old('buy_product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Get Product <span class="text-danger">*</span></label>
                            <select name="get_product_id"
                                class="form-select"
                                {{ $mode === 'view' ? 'disabled' : '' }}
                                required>
                                <option value="">Select Product</option>

                                @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ old('get_product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    --}}



                    {{-- Discount --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Discount Value</label>
                            <input type="number"
                                name="discount_value"
                                class="form-control"
                                value="{{ old('discount_value', $offer->discount_value ?? '') }}"
                                {{ $mode === 'view' ? 'readonly' : '' }}>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Max Discount (optional)</label>
                            <input type="number"
                                name="max_discount"
                                class="form-control"
                                value="{{ old('max_discount', $offer->max_discount ?? '') }}"
                                {{ $mode === 'view' ? 'readonly' : '' }}>
                        </div>
                    </div>


                    <div class="row">

                        {{-- Minimum Order Amount --}}
                        <div class="col-md-6 mb-3">
                            <label>Minimum Order Amount <span class="text-danger">*</span></label>

                            <input type="number"
                                name="min_order_amount"
                                class="form-control"
                                value="{{ old('min_order_amount', $offer->min_order_amount ?? '') }}"
                                {{ $mode === 'view' ? 'readonly' : '' }}>

                            @error('min_order_amount')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label>Status <span class="text-danger">*</span></label>

                            <select name="status"
                                class="form-select"
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

                            @error('status')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Validity --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Start Date <span class="text-danger">*</span></label>

                            <input type="date"
                                name="start_date"
                                class="form-control"
                                value="{{ old('start_date', isset($offer) ? optional($offer->start_date)->format('Y-m-d') : '') }}"
                                {{ $mode === 'view' ? 'readonly' : '' }}>

                            @error('start_date')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>End Date <span class="text-danger">*</span></label>

                            <input type="date"
                                name="end_date"
                                class="form-control"
                                value="{{ old('end_date', isset($offer) ? optional($offer->end_date)->format('Y-m-d') : '') }}"
                                {{ $mode === 'view' ? 'readonly' : '' }}>

                            @error('end_date')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    {{-- Description --}}
                    <div class="mb-3">
                        <label>Description</label>

                        <textarea name="description"
                            class="form-control"
                            {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('description', $offer->description ?? '') }}</textarea>

                        @error('description')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="card-footer text-end">

                        @if($mode === 'view')
                        <a href="{{ route('offers.index') }}" class="btn btn-secondary">
                            Back
                        </a>
                        @endif

                        @if($mode === 'add')
                        <button type="submit" class="btn btn-success">
                            Save Offer
                        </button>
                        @endif

                        @if($mode === 'edit')
                        <button type="submit" class="btn btn-success">
                            Update Offer
                        </button>
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
{{-- <script>
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
</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const categorySelect = document.querySelector('select[name="category_id"]');
        const productSelect = document.querySelector('select[name="product_id"]');

        categorySelect.addEventListener('change', function() {

            let categoryId = this.value;

            // Reset product dropdown
            productSelect.innerHTML = `
            <option value="" disabled selected>Select Product</option>
            <option value="all">All Products</option>
        `;

            if (!categoryId) {
                return;
            }

            if (categoryId === 'all') {
                fetch(`/offer/all-products`)
                    .then(response => response.json())
                    .then(products => {
                        products.forEach(product => {
                            productSelect.innerHTML +=
                                `<option value="${product.id}">${product.name}</option>`;
                        });
                    })
                    .catch(() => {
                        productSelect.innerHTML +=
                            `<option value="">No products found</option>`;
                    });
                return;
            }

            fetch(`/offer/products-by-category/${categoryId}`)
                .then(response => response.json())
                .then(products => {
                    products.forEach(product => {
                        productSelect.innerHTML +=
                            `<option value="${product.id}">${product.name}</option>`;
                    });
                })
                .catch(() => {
                    productSelect.innerHTML +=
                        `<option value="">No products found</option>`;
                });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const offerType = document.querySelector('select[name="offer_type"]');
        const bxgySection = document.getElementById('bxgySection');

        function toggleBxgy() {
            if (offerType.value === 'buy_x_get_y') {
                bxgySection.style.display = 'block';
            } else {
                bxgySection.style.display = 'none';
            }
        }

        offerType.addEventListener('change', toggleBxgy);
        toggleBxgy(); // on page load
    });
</script>