@include('layouts.header')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="card">
                        <h4 class="card-header">Create Supplier Challan</h4>

                        <div class="card-body">
                            <form method="POST" action="{{ route('supplier_challan.store') }}">
                                @csrf

                                {{-- HEADER --}}

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $warehouse->name ?? '' }}"
                                            readonly>
                                        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id ?? '' }}">
                                    </div>
                                    {{-- Supplier --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                        <select name="supplier_id" class="form-select"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                            <option value="">Select Supplier</option>
                                            @foreach ($suppliers as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ old('supplier_id', $challan->supplier_id ?? '') == $s->id ? 'selected' : '' }}>
                                                    {{ $s->supplier_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Auto Challan No --}}
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Challan No <span class="text-danger">*</span></label>
                                        <input type="text" name="challan_no" class="form-control"
                                            value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}">
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label">Challan Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="challan_date" class="form-control"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <hr>

                                    {{-- CATEGORY | SUB CATEGORY | PRODUCT --}}
                                    <div class="row mb-3 align-items-end">

                                        <div class="col-md-3">
                                            <label class="form-label">Category <span
                                                    class="text-danger">*</span></label>
                                            <select id="categorySelect" class="form-select" multiple>
                                                @foreach ($categories as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Sub Category <span
                                                    class="text-danger">*</span></label>
                                            <select id="subCategorySelect" class="form-select" multiple></select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Product <span class="text-danger">*</span></label>
                                            <select id="productSelect" class="form-select" multiple></select>
                                        </div>

                                    </div>

                                    <button type="button" id="addProductBtn" class="btn btn-success">
                                        + Add Product
                                    </button>

                                    {{-- TABLE --}}
                                    <div id="itemsSection" class="d-none mt-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Sub Category</th>
                                                    <th>Product</th>
                                                    <th>Qty</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsBody"></tbody>
                                        </table>

                                        <button type="submit" class="btn btn-success">
                                            Save Supplier Challan
                                        </button>
                                    </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            let index = 0;

            $('#categorySelect, #subCategorySelect, #productSelect').select2({
                placeholder: 'Select',
                closeOnSelect: false,
                width: '100%'
            });

            /* CATEGORY → SUB CATEGORY */
            $('#categorySelect').on('change', function() {

                let categoryIds = $(this).val();
                $('#subCategorySelect').empty().trigger('change');
                $('#productSelect').empty().trigger('change');

                if (!categoryIds || categoryIds.length === 0) return;

                $.get('/ajax/subcategories', {
                    category_ids: categoryIds
                }, function(res) {
                    res.data.forEach(sc => {
                        $('#subCategorySelect').append(
                            new Option(sc.name, sc.id)
                        );
                    });
                    $('#subCategorySelect').trigger('change');
                });
            });

            /* SUB CATEGORY → PRODUCT */
            $('#subCategorySelect').on('change', function() {

                let subCategoryIds = $(this).val();
                $('#productSelect').empty().trigger('change');

                if (!subCategoryIds || subCategoryIds.length === 0) return;

                $.get('/ajax/products-by-subcategory', {
                    sub_category_ids: subCategoryIds
                }, function(res) {

                    res.data.forEach(p => {

                        let option = new Option(p.name, p.id, false, false);

                        $(option)
                            .attr('data-category-id', p.sub_category.category.id)
                            .attr('data-category-name', p.sub_category.category.name)
                            .attr('data-sub-category-id', p.sub_category.id)
                            .attr('data-sub-category-name', p.sub_category.name);

                        $('#productSelect').append(option);
                    });

                    $('#productSelect').trigger('change');
                });
            });

            /* ✅ ADD PRODUCT — MUST BE INSIDE READY */
            $('#addProductBtn').on('click', function() {

                let productIds = $('#productSelect').val();

                if (!productIds || productIds.length === 0) {
                    alert('Select Product');
                    return;
                }

                $('#itemsSection').removeClass('d-none');

                productIds.forEach(pid => {

                    let option = $('#productSelect option[value="' + pid + '"]');

                    let productName = option.text();
                    let categoryId = option.data('category-id');
                    let categoryName = option.data('category-name');
                    let subCategoryId = option.data('sub-category-id');
                    let subCategoryName = option.data('sub-category-name');

                    $('#itemsBody').append(`
                <tr>
                    <td>${categoryName}</td>
                    <td>${subCategoryName}</td>
                    <td>${productName}</td>
                    <td>
                        <input type="number"
                               name="items[${index}][received_qty]"
                               class="form-control" min="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>

                    <input type="hidden" name="items[${index}][category_id]" value="${categoryId}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${subCategoryId}">
                    <input type="hidden" name="items[${index}][product_id]" value="${pid}">
                </tr>
            `);

                    index++;
                });

                $('#productSelect').val(null).trigger('change');
            });

            /* REMOVE ROW */
            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>

</body>
