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
                                <form action="{{ route('offers.store') }}" method="POST">
                                    @csrf

                                    <div class="card-body">

                                        {{-- Offer Title --}}
                                        <div class="mb-3">
                                            <label class="form-label">Offer Title</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>

                                        {{-- Description --}}
                                        <div class="mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"></textarea>
                                        </div>

                                        {{-- Warehouse --}}
                                        <div class="mb-3">
                                            <label>Warehouse</label>
                                            <select name="warehouse_id" class="form-control" required>
                                                <option value="">Select Warehouse</option>
                                                @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">
                                                    {{ $warehouse->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Offer Type --}}
                                        <div class="mb-3">
                                            <label>Offer Type</label>
                                            <select name="offer_type" class="form-control" required>
                                                <option value="flat">Flat Discount</option>
                                                <option value="percentage">Percentage Discount</option>
                                                <option value="bxgy">Buy X Get Y</option>
                                            </select>
                                        </div>

                                        {{-- Discount --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Discount Value</label>
                                                <input type="number" name="discount_value" class="form-control">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Max Discount (optional)</label>
                                                <input type="number" name="max_discount" class="form-control">
                                            </div>
                                        </div>

                                        {{-- Minimum Order Amount --}}
                                        <div class="mb-3">
                                            <label>Minimum Order Amount</label>
                                            <input type="number" name="min_order_amount" class="form-control">
                                        </div>

                                        {{-- Validity --}}
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control" required>
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="card-footer text-end">
                                        <button type="submit" class="btn btn-success">
                                            Save Offer
                                        </button>
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

            // 🔹 If "Select Categories"
            if (!categoryId) {
                return;
            }

            // 🔹 If "All Categories" → load ALL products
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

            // 🔹 Particular category → load category products
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