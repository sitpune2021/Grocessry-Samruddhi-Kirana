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
                                        Retailer Order Pricing
                                    </h2>
                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('retailer-orders.store') }}">
                                            @csrf

                                            <div class="row">
                                                <!-- Retailer -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Retailer</label>
                                                    <select id="retailer_id" name="retailer_id" class="form-select" required>
                                                        <option value="">Select Retailer</option>
                                                        @foreach($retailers as $retailer)
                                                            <option value="{{ $retailer->id }}">{{ $retailer->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Category -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select id="category_id" name="category_id" class="form-select" required>
                                                        <option value="">Select Category</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Product -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Product</label>
                                                    <select id="product_id" name="product_id" class="form-select" required>
                                                        <option value="">Select Product</option>
                                                    </select>
                                                </div>

                                                <!-- Locked Price -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Price</label>
                                                    <input type="number"
                                                        id="price"
                                                        name="price"
                                                        class="form-control"
                                                        readonly
                                                        placeholder="Price">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Quantity -->
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number"
                                                        id="quantity"
                                                        name="quantity"
                                                        class="form-control"
                                                        placeholder="Qty"
                                                        min="1"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Warehouse -->
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Warehouse</label>
                                                <select id="warehouse_id" name="warehouse_id" class="form-select" required>
                                                    <option value="">Select Warehouse</option>
                                                </select>
                                            </div>

                                            <!-- 🔒 Hidden fields -->
                                            <input type="hidden" name="items[0][category_id]" id="h_category">
                                            <input type="hidden" name="items[0][product_id]" id="h_product">
                                            <input type="hidden" name="items[0][price]" id="h_price">
                                            <input type="hidden" name="items[0][quantity]" id="h_quantity">

                                            <!-- Submit -->
                                            <div class="mt-4 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">
                                                    Place Order
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
document.querySelector('form').addEventListener('submit', function () {

    document.getElementById('h_category').value = category.value;
    document.getElementById('h_product').value  = product.value;
    document.getElementById('h_price').value    = priceEl.value;
    document.getElementById('h_quantity').value = document.getElementById('quantity').value;

});
</script>

<script>
const retailer = document.getElementById('retailer_id');
const category = document.getElementById('category_id');
const product  = document.getElementById('product_id');
const priceEl  = document.getElementById('price');

retailer.addEventListener('change', function () {

    category.innerHTML = '<option value="">Loading...</option>';
    product.innerHTML  = '<option value="">Select Product</option>';
    priceEl.value = '';

    fetch(`/retailer-orders/get-categories-by-retailer/${this.value}`)
        .then(res => res.json())
        .then(data => {
            category.innerHTML = '<option value="">Select Category</option>';
            data.forEach(cat => {
                category.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        });
});
</script>

<script>
category.addEventListener('change', function () {

    product.innerHTML = '<option value="">Loading...</option>';
    priceEl.value = '';

    fetch(`/retailer-orders/get-products-by-retailer/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            product.innerHTML = '<option value="">Select Product</option>';
            data.forEach(p => {
                product.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
        });
});
</script>

<script>
product.addEventListener('change', function () {

    if (!retailer.value || !this.value) return;

    fetch(`/retailer-orders/get-retailer-price/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            priceEl.value = data.price ?? 0;
        });
});
</script>

<script>
const warehouse = document.getElementById('warehouse_id');
</script>

<!-- category wise district and taluka wise show -->
<script>
category.addEventListener('change', function () {

    product.innerHTML   = '<option value="">Select Product</option>';
    warehouse.innerHTML = '<option value="">Loading...</option>';
    priceEl.value = '';

    // 🔥 Load warehouses by category
    fetch(`/retailer-orders/ajax/get-warehouses-by-category/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            warehouse.innerHTML = '<option value="">Select Warehouse</option>';
            data.forEach(w => {
                warehouse.innerHTML += `<option value="${w.id}">${w.name}</option>`;
            });
        });

    // Existing product fetch
    fetch(`/retailer-orders/get-products-by-retailer/${retailer.value}/${this.value}`)
        .then(res => res.json())
        .then(data => {
            product.innerHTML = '<option value="">Select Product</option>';
            data.forEach(p => {
                product.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
        });
});
</script>
