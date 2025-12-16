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

                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <!-- Form card -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="mb-0 flex-grow-1">
                                        {{ isset($category) ? 'Edit Batch' : 'Add Batch' }}
                                    </h4>

                                        <form method="POST" action="{{ route('sale.store') }}">
                                            @csrf

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-12">
                                                <label for="warehouse_id" class="form-label">Select Warehouse</label>
                                                <select name="warehouse_id" id="warehouse_id" class="form-select">
                                                    <option value="">-- Select Warehouse --</option>
                                                    @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>

                                            <!-- Row 1: Product & Quantity -->
                                            <!-- Category Dropdown -->
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-12">
                                                    <label for="category_id" class="form-label">Category</label>
                                                    <select name="category_id" id="category_id" class="form-select">
                                                        <option value="">-- Select Category --</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <!-- Row 2: Batch Number & Quantity -->

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                   <label for="quantity" class="form-label">Product Quantity</label>
                                                <input type="number" name="quantity" id="quantity" min="1" placeholder="Product Quantity" class="form-control @error('quantity') is-invalid @enderror">
                                                @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                </div>
                                            </div>
                                    

                                             

                                        <!-- Buttons -->
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('batches.index') }}" class="btn btn-outline-info">Back</a>
                                            <button type="submit" class="btn btn-primary">Save Batch</button>
                                        </div>


                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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