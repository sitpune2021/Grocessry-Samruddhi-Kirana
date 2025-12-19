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

                            <!-- Form card -->
                            <div>
                                <div class="card mb-4" style="  margin:auto;">
                                    <h4 class="card-header">
                                        Sell Product
                                    </h4>
                                    <div class="card-body">

                                        <form method="POST" action="{{ route('sale.store') }}">
                                            @csrf

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label for="warehouse_id" class="form-label">Warehouse</label>
                                                    <select name="warehouse_id" id="warehouse_id" class="form-select">
                                                        <option value="">Select Warehouse</option>
                                                        @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>



                                                <div class="col-md-4">
                                                    <label for="category_id" class="form-label">Category</label>
                                                    <select name="category_id" id="category_id" class="form-select">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <!-- Product Dropdown -->

                                                <div class="col-md-4">
                                                    <label for="product_id" class="form-label">Product Name</label>
                                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ ($selectedProduct == $product->id) ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>




                                                <div class="col-md-4">
                                                    <label for="quantity" class="form-label">Product Quantity</label>
                                                    <input type="number"
                                                        name="quantity"
                                                        id="quantity"
                                                        min="1"
                                                        max="{{ $availableStock }}"
                                                        class="form-control @error('quantity') is-invalid @enderror"
                                                        placeholder="Max available: {{ $availableStock }}">
                                                    @error('quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small id="stock-info" class="text-muted">
                                                        Max available in selected warehouse: {{ $availableStock }}
                                                    </small>
                                                </div>
                                            </div>


                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary">
                                                    Back
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    Product Sell
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
    document.getElementById('category_id').addEventListener('change', function() {
        let categoryId = this.value;
        let productSelect = document.getElementById('product_id');

        productSelect.innerHTML = '<option>Loading...</option>';

        fetch(`/get-products-by-category/${categoryId}`)
            .then(res => res.json())
            .then(data => {
                productSelect.innerHTML = '<option value="">-- Select Product --</option>';
                data.forEach(p => {
                    productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });
    });
</script>

<script>
    document.getElementById('product_id').addEventListener('change', function() {
        let productId = this.value;
        let warehouseId = document.getElementById('warehouse_id').value;
        let quantityInput = document.getElementById('quantity');
        let stockInfo = document.getElementById('stock-info');

        if (!warehouseId || !productId) return;

        fetch(`/get-stock/${warehouseId}/${productId}`)
            .then(res => res.json())
            .then(data => {
                let stock = data.stock;
                quantityInput.max = stock;
                stockInfo.textContent = `Max available in selected warehouse: ${stock}`;
            });
    });
</script>