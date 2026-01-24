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
                        <h4 class="card-header">
                            @if ($mode === 'view')
                                View Supplier Challan
                            @elseif($mode === 'edit')
                                Edit Supplier Challan
                            @else
                                Create Supplier Challan
                            @endif
                        </h4>

                        <div class="card-body">
                            <form method="POST"
                                action="{{ isset($challan) && $mode === 'edit'
                                    ? route('supplier_challan.update', $challan->id)
                                    : route('supplier_challan.store') }}">

                                @csrf
                                @if (isset($challan) && $mode === 'edit')
                                    @method('PUT')
                                @endif


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
                                            value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label">Challan Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="challan_date" class="form-control"
                                                value="{{ old('challan_date', isset($challan) ? $challan->challan_date->format('Y-m-d') : date('Y-m-d')) }}"
                                                {{ $mode === 'view' ? 'disabled' : '' }} required>

                                        </div>
                                    </div>
                                    <hr>

                                    {{-- CATEGORY | SUB CATEGORY | PRODUCT --}}
                                    <div class="row mb-3 align-items-end">

                                        <div class="col-md-3">
                                            <label class="form-label">Category <span
                                                    class="text-danger">*</span></label>
                                            <select id="categorySelect" class="form-select" multiple
                                                {{ $mode === 'view' ? 'disabled' : '' }}>
                                                @foreach ($categories as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Sub Category <span
                                                    class="text-danger">*</span></label>
                                            <select id="subCategorySelect" class="form-select" multiple
                                                {{ $mode === 'view' ? 'disabled' : '' }}></select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Product <span class="text-danger">*</span></label>
                                            <select id="productSelect" class="form-select" multiple
                                                {{ $mode === 'view' ? 'disabled' : '' }}></select>
                                        </div>

                                    </div>
                                    @if ($mode !== 'view')
                                        <button type="button" id="addProductBtn" class="btn btn-success">
                                            + Add Product
                                        </button>
                                    @endif


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

                                        @if ($mode !== 'view')
                                            <button type="submit" class="btn btn-success">
                                                Save Supplier Challan
                                            </button>
                                        @endif

                                    </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            /* =====================
               INDEX INIT
            ===================== */
            let index = 0;

            /* =====================
               SELECT2 INIT
            ===================== */
            $('#categorySelect').select2({
                placeholder: 'Select Category',
                closeOnSelect: false,
                width: '100%'
            });

            $('#subCategorySelect, #productSelect').select2({
                closeOnSelect: false,
                width: '100%'
            });


            /* =====================
               CATEGORY → AUTO SUB CATEGORY
            ===================== */
            $('#categorySelect').on('change', function() {

                let categoryIds = $(this).val();

                if (!categoryIds || categoryIds.length === 0) {
                    $('#subCategorySelect').empty().trigger('change');
                    $('#productSelect').empty().trigger('change');
                    $('#itemsBody').empty();
                    return;
                }

                let existingSubCats = $('#subCategorySelect option')
                    .map(function() {
                        return $(this).val();
                    })
                    .get();

                $.get('/ajax/subcategories', {
                    category_ids: categoryIds
                }, function(res) {

                    res.data.forEach(sc => {
                        if (!existingSubCats.includes(sc.id.toString())) {
                            let option = new Option(sc.name, sc.id, true, true);
                            $('#subCategorySelect').append(option);
                        }
                    });

                    $('#subCategorySelect').trigger('change');
                });
            });


            /* =====================
               SUB CATEGORY → AUTO PRODUCT
            ===================== */
            $('#subCategorySelect').on('change', function() {

                let subCategoryIds = $(this).val();

                if (!subCategoryIds || subCategoryIds.length === 0) {
                    $('#productSelect').empty().trigger('change');
                    $('#itemsBody').empty();
                    return;
                }

                let existingProducts = $('#productSelect option')
                    .map(function() {
                        return $(this).val();
                    })
                    .get();

                $.get('/ajax/products-by-subcategory', {
                    sub_category_ids: subCategoryIds
                }, function(res) {

                    res.data.forEach(p => {

                        if (existingProducts.includes(p.id.toString())) return;

                        let option = new Option(p.name, p.id, true, true);

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


            /* =====================
               ADD PRODUCT (NO CHANGE)
            ===================== */
            $('#addProductBtn').on('click', function() {

                let productIds = $('#productSelect').val();

                if (!productIds || productIds.length === 0) {
                    alert('No products available');
                    return;
                }

                $('#itemsSection').removeClass('d-none');

                productIds.forEach(pid => {

                    // prevent duplicate row
                    if ($('#itemsBody input[name$="[product_id]"][value="' + pid + '"]').length) {
                        return;
                    }

                    let option = $('#productSelect option[value="' + pid + '"]');

                    $('#itemsBody').append(`
                <tr>
                    <td>${option.data('category-name')}</td>
                    <td>${option.data('sub-category-name')}</td>
                    <td>${option.text()}</td>

                    <td>
                        <input type="number"
                            name="items[${index}][received_qty]"
                            class="form-control"
                            min="1"
                            required>
                    </td>

                    <td>
                        <button type="button"
                            class="btn btn-danger btn-sm removeRow">X</button>
                    </td>

                    <input type="hidden" name="items[${index}][category_id]" value="${option.data('category-id')}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${option.data('sub-category-id')}">
                    <input type="hidden" name="items[${index}][product_id]" value="${pid}">
                </tr>
            `);

                    index++;
                });
            });


            /* =====================
               MANUAL REMOVE : PRODUCT CHIP
            ===================== */
            $('#productSelect').on('select2:unselect', function(e) {

                let productId = e.params.data.id;

                $('#itemsBody input[name$="[product_id]"][value="' + productId + '"]')
                    .closest('tr')
                    .remove();
            });


            /* =====================
               MANUAL REMOVE : SUB CATEGORY CHIP
            ===================== */
            $('#subCategorySelect').on('select2:unselect', function(e) {

                let subCategoryId = e.params.data.id;

                $('#productSelect option[data-sub-category-id="' + subCategoryId + '"]').each(function() {

                    let productId = $(this).val();

                    $('#itemsBody input[name$="[product_id]"][value="' + productId + '"]')
                        .closest('tr')
                        .remove();

                    $(this).remove();
                });

                $('#productSelect').trigger('change');
            });


            /* =====================
               MANUAL REMOVE : CATEGORY CHIP
            ===================== */
            $('#categorySelect').on('select2:unselect', function(e) {

                let categoryId = e.params.data.id;

                $('#productSelect option[data-category-id="' + categoryId + '"]').each(function() {

                    let productId = $(this).val();

                    $('#itemsBody input[name$="[product_id]"][value="' + productId + '"]')
                        .closest('tr')
                        .remove();

                    $(this).remove();
                });

                $('#subCategorySelect option').each(function() {
                    $(this).remove();
                });

                $('#subCategorySelect').trigger('change');
                $('#productSelect').trigger('change');
            });


            /* =====================
               REMOVE ROW BUTTON (TABLE)
            ===================== */
            $(document).on('click', '.removeRow', function() {

                let productId = $(this)
                    .closest('tr')
                    .find('input[name$="[product_id]"]')
                    .val();

                // remove chip also
                $('#productSelect option[value="' + productId + '"]').prop('selected', false);
                $('#productSelect').trigger('change');

                $(this).closest('tr').remove();
            });

        });
    </script>
</body>
