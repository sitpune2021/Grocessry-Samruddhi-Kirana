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
                                    <h2 class="text-xl font-semibold mb-4">
                                        {{ isset($retailer) ? 'Edit Retailer' : 'Assign Retailer Pricinng' }}
                                    </h2>
                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form method="POST"
                                            action="{{ isset($pricing)
                                                    ? route('retailer-pricing.update', $pricing)
                                                    : route('retailer-pricing.store') }}">

                                            @csrf
                                            @isset($pricing) @method('PUT') @endisset

                                            <div class="row">
                                                <!-- Retailer -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Retailer</label>
                                                    <select name="retailer_id" class="form-select" required>
                                                        <option value="">Select Retailer</option>
                                                        @foreach($retailers as $r)
                                                            <option value="{{ $r->id }}"
                                                                @selected(old('retailer_id', $pricing->retailer_id ?? '') == $r->id)>
                                                                {{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Category -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select id="category_id" name="category_id" class="form-select" required>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}"
                                                                @selected(old('category_id', $pricing->category_id ?? '') == $cat->id)>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Product -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Product</label>
                                                    <select name="product_id" id="product_id" class="form-select" required>
                                                        <option value="">Select Product</option>
                                                    </select>
                                                </div>

                                                <!-- Base Price -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Base Price</label>
                                                    <input type="number"
                                                        name="base_price"
                                                        class="form-control"
                                                        step="0.01"
                                                        value="{{ old('base_price', $pricing->base_price ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Discount % -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Discount Percent</label>
                                                    <input type="number"
                                                        name="discount_percent"
                                                        class="form-control"
                                                        step="0.01"
                                                        value="{{ old('discount_percent', $pricing->discount_percent ?? '') }}">
                                                </div>

                                                <!-- Discount Amount -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Discount Amount</label>
                                                    <input type="number"
                                                        name="discount_amount"
                                                        class="form-control"
                                                        step="0.01"
                                                        value="{{ old('discount_amount', $pricing->discount_amount ?? '') }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Effective From -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Effective From</label>
                                                    <input type="date"
                                                        name="effective_from"
                                                        class="form-control"
                                                        value="{{ old('effective_from', $pricing->effective_from ?? '') }}">
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="mt-4 d-flex justify-content-end text-end">
                                                <a href="{{ route('retailer-pricing.index') }}" class="btn btn-outline-secondary">
                                                    Back
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    {{ isset($pricing) ? 'Update Price' : 'Assign Price' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- / Content -->
                    @include('layouts.footer')
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const categorySelect = document.getElementById('category_id');
    const productSelect  = document.getElementById('product_id');

    categorySelect.addEventListener('change', function () {

        const categoryId = this.value;
        productSelect.innerHTML = '<option value="">Loading...</option>';

        if (!categoryId) {
            productSelect.innerHTML = '<option value="">Select Product</option>';
            return;
        }

        fetch("{{ url('/get-products-by-category') }}/" + categoryId)
            .then(res => res.json())
            .then(data => {

                productSelect.innerHTML = '<option value="">Select Product</option>';

                if (data.length === 0) {
                    productSelect.innerHTML +=
                        `<option value="">No products found</option>`;
                }

                data.forEach(product => {
                    productSelect.innerHTML +=
                        `<option value="${product.id}">${product.name}</option>`;
                });
            })
            .catch(() => {
                productSelect.innerHTML =
                    '<option value="">Error loading products</option>';
            });
    });
});
</script>

@if(isset($pricing))
@parent
<script>
    window.addEventListener('load', function () {

    const categoryId = "{{ $pricing->product->category_id }}";
    const productId  = "{{ $pricing->product_id }}";

    const categorySelect = document.getElementById('category_id');
    categorySelect.value = categoryId;
    categorySelect.dispatchEvent(new Event('change'));

    setTimeout(() => {
        document.getElementById('product_id').value = productId;
    }, 300);
});
</script>
@endif







