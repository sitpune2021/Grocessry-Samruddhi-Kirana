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
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <!-- Form card -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="mb-0 flex-grow-1">
                                        {{ isset($category) ? 'Edit Batch' : 'Add Batch' }}
                                    </h4>

                                </div>
                                <div class="card-body">
                                    <form method="POST" action="/batches">
                                        @csrf

                                        <!-- Row 1: Category & Product -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label for="category_id" class="form-label">Category</label>
                                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                                    <option value="">-- Select Category --</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="product_id" class="form-label">Product</label>
                                                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                                                        <option value="">Select Product</option>
                                                    </select>
                                                    @error('product_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <!-- Row 2: Batch Number & Quantity -->

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="batch_no" class="form-label">Batch Number</label>
                                                    <input type="text" name="batch_no" id="batch_no" class="form-control @error('batch_no') is-invalid @enderror" placeholder="Batch Number">
                                                    @error('batch_no')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="quantity" class="form-label">Quantity</label>
                                                    <input type="number" name="quantity" id="quantity" min="1" class="form-control @error('quantity') is-invalid @enderror " placeholder="Quantity">
                                                    @error('quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <!-- Row 3: MFG & Expiry -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label for="mfg_date" class="form-label">MFG Date</label>
                                                <input type="date" name="mfg_date" id="mfg_date" class="form-control @error('mfg_date') is-invalid @enderror">
                                                @error('mfg_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror">
                                                    @error('expiry_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary">Back</a>
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#category_id').change(function() {
        let categoryId = $(this).val();
        $('#product_id').html('<option value="">Loading...</option>');

        if (categoryId) {
            $.ajax({
                url: '/get-products/' + categoryId,
                type: 'GET',
                success: function(products) {
                    let options = '<option value="">-- Select Product --</option>';
                    products.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    $('#product_id').html(options);
                }
            });
        } else {
            $('#product_id').html('<option value="">-- Select Product --</option>');
        }
    });
</script>