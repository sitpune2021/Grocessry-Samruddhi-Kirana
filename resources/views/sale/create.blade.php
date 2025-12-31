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
                                                    <label for="warehouse_id" class="form-label">Warehouse <span
                                                                class="text-danger">*</span></label>
                                                    <select name="warehouse_id" id="warehouse_id" class="form-select">
                                                        <option value="">Select Warehouse</option>
                                                        @foreach ($warehouses as $w)
                                                            <option value="{{ $w->id }}">{{ $w->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('warehouse_id')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="category_id" class="form-label">Category <span
                                                                class="text-danger">*</span></label>
                                                    <select name="category_id" id="category_id" class="form-select">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('warehouse_id')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                    <select name="sub_category_id" id="sub_category_id" class="form-select">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>

                                                <!-- Product Dropdown -->

                                                <div class="col-md-4">
                                                    <label for="product_id" class="form-label">Product Name <span
                                                                class="text-danger">*</span></label>
                                                    <select name="product_id" id="product_id" class="form-select ">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $selectedProduct == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_id')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="quantity" class="form-label">Product Quantity <span
                                                                class="text-danger">*</span></label>
                                                    <input type="number" name="quantity" id="quantity" min="1"
                                                        max="{{ $availableStock }}"
                                                        class="form-control @error('quantity') is-invalid @enderror"
                                                        placeholder="Max available: {{ $availableStock }}">
                                                      @error('quantity')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                                    <small id="stock-info" class="text-muted">
                                                        Max available in selected warehouse: {{ $availableStock }}
                                                    </small>
                                                </div>

                                            </div>

                                            <div class="d-flex justify-content-end gap-2 text-success">
                                                <a href="{{ route('batches.index') }}"
                                                    class="btn btn-success">
                                                    Back
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    PRODUCT SELL
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/* Warehouse → Category */
$('#warehouse_id').change(function () {

    let wid = $(this).val();
    $('#category_id').html('<option>Loading...</option>');
    $('#sub_category_id').html('<option value="">Select Sub Category</option>');
    $('#product_id').html('<option value="">Select Product</option>');
    $('#quantity').val('');
    $('#stock-info').text('');

    if (!wid) return;

    $.get('/sell/ws/categories/' + wid, function (data) {
        let html = '<option value="">Select Category</option>';
        data.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
        $('#category_id').html(html);
    });
});

/* Category → Sub Category */
$('#category_id').change(function () {

    let wid = $('#warehouse_id').val();
    let cid = $(this).val();

    $('#sub_category_id').html('<option>Loading...</option>');
    $('#product_id').html('<option value="">Select Product</option>');
    $('#quantity').val('');
    $('#stock-info').text('');

    if (!cid) return;

    $.get('/sell/ws/subcategories/' + wid + '/' + cid, function (data) {
        let html = '<option value="">Select Sub Category</option>';
        data.forEach(s => html += `<option value="${s.id}">${s.name}</option>`);
        $('#sub_category_id').html(html);
    });
});

/* Sub Category → Product */
$('#sub_category_id').change(function () {

    let wid = $('#warehouse_id').val();
    let sid = $(this).val();

    $('#product_id').html('<option>Loading...</option>');
    $('#quantity').val('');
    $('#stock-info').text('');

    if (!sid) return;

    $.get('/sell/ws/products/' + wid + '/' + sid, function (data) {
        let html = '<option value="">Select Product</option>';
        data.forEach(p => html += `<option value="${p.id}">${p.name}</option>`);
        $('#product_id').html(html);
    });
});

/* Product → Quantity */
$('#product_id').change(function () {

    let wid = $('#warehouse_id').val();
    let pid = $(this).val();

    if (!pid) return;

    $.get('/sell/ws/quantity/' + wid + '/' + pid, function (qty) {
        $('#quantity').attr('max', qty);
        $('#stock-info').text(`Max available in selected warehouse: ${qty}`);
    });
});
</script>
