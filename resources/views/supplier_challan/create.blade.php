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
                                        <label class="form-label">Warehouse *</label>
                                        <input type="text" class="form-control" value="{{ $warehouse->name ?? '' }}"
                                            readonly>
                                        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id ?? '' }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Supplier *</label>
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

                                    <div class="col-md-4">
                                        <label class="form-label">Challan No *</label>
                                        <input type="text" name="challan_no" class="form-control"
                                            value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Challan Date *</label>
                                        <input type="date" name="challan_date" class="form-control"
                                            value="{{ old('challan_date', isset($challan) ? $challan->challan_date->format('Y-m-d') : date('Y-m-d')) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <hr>

                                {{-- CATEGORY / SUB CATEGORY / PRODUCT --}}
                                <div class="row mb-3 align-items-end" id="selectionSection">

                                    <div class="col-md-3">
                                        <label class="form-label">Category *</label>
                                        <select id="categorySelect" class="form-select" multiple>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Sub Category *</label>
                                        <select id="subCategorySelect" class="form-select" multiple></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Product *</label>
                                        <select id="productSelect" class="form-select" multiple></select>
                                    </div>

                                </div>

                                @if ($mode !== 'view')
                                    <button type="button" id="addProductBtn" class="btn btn-success mb-3">
                                        + Add Product
                                    </button>
                                @endif

                                {{-- TABLE --}}
                                <div id="itemsSection" class="d-none mt-3">
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

    @if (isset($challan))
        <script>
            window.existingItems = @json($challan->items);
            window.pageMode = "{{ $mode }}";
        </script>
    @endif

    <script>
        $(document).ready(function() {

            let index = 0;

            $('#categorySelect').select2({
                closeOnSelect: false,
                width: '100%'
            });
            $('#subCategorySelect, #productSelect').select2({
                closeOnSelect: false,
                width: '100%'
            });

            if (typeof pageMode !== 'undefined' && pageMode === 'view') {
                $('#selectionSection').hide();
                $('#addProductBtn').hide();
            }

            /* =====================
               PREFILL EDIT / VIEW
            ===================== */
            if (typeof existingItems !== 'undefined' && existingItems.length) {

                $('#itemsSection').removeClass('d-none');

                existingItems.forEach(item => {
                    $('#itemsBody').append(`
                <tr>
                    <td>${item.category.name}</td>
                    <td>${item.sub_category.name}</td>
                    <td>${item.product.name}</td>
                    <td>
                        <input type="number"
                               name="items[${index}][received_qty]"
                               class="form-control"
                               value="${item.received_qty}"
                               ${pageMode === 'view' ? 'readonly' : ''}>
                    </td>
                    <td>
                        ${pageMode !== 'view'
                            ? '<button type="button" class="btn btn-danger btn-sm removeRow">X</button>'
                            : ''}
                    </td>

                    <input type="hidden" name="items[${index}][category_id]" value="${item.category_id}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${item.sub_category_id}">
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                </tr>
            `);
                    index++;
                });
            }

            /* =====================
               CATEGORY → SUB CATEGORY
            ===================== */
            $('#categorySelect').on('change', function() {

                let ids = $(this).val();
                if (!ids || ids.length === 0) return;

                $.get('/ajax/subcategories', {
                    category_ids: ids
                }, function(res) {
                    res.data.forEach(sc => {
                        if (!$('#subCategorySelect option[value="' + sc.id + '"]').length) {
                            $('#subCategorySelect')
                                .append(new Option(sc.name, sc.id, true, true));
                        }
                    });
                    $('#subCategorySelect').trigger('change');
                });
            });

            /* =====================
               SUB CATEGORY → PRODUCT
            ===================== */
            $('#subCategorySelect').on('change', function() {

                let ids = $(this).val();
                if (!ids || ids.length === 0) return;

                $.get('/ajax/products-by-subcategory', {
                    sub_category_ids: ids
                }, function(res) {

                    res.data.forEach(p => {
                        if ($('#productSelect option[value="' + p.id + '"]').length) return;

                        let opt = new Option(p.name, p.id, true, true);
                        $(opt)
                            .attr('data-category-id', p.sub_category.category.id)
                            .attr('data-category-name', p.sub_category.category.name)
                            .attr('data-sub-category-id', p.sub_category.id)
                            .attr('data-sub-category-name', p.sub_category.name);

                        $('#productSelect').append(opt);
                    });

                    $('#productSelect').trigger('change');
                });
            });

            /* =====================
               ADD PRODUCT
            ===================== */
            $('#addProductBtn').on('click', function() {

                let ids = $('#productSelect').val();
                if (!ids || ids.length === 0) return;

                $('#itemsSection').removeClass('d-none');

                ids.forEach(pid => {

                    if ($('#itemsBody input[value="' + pid + '"]').length) return;

                    let opt = $('#productSelect option[value="' + pid + '"]');

                    $('#itemsBody').append(`
                <tr>
                    <td>${opt.data('category-name')}</td>
                    <td>${opt.data('sub-category-name')}</td>
                    <td>${opt.text()}</td>
                    <td>
                        <input type="number"
                               name="items[${index}][received_qty]"
                               class="form-control" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                    </td>

                    <input type="hidden" name="items[${index}][category_id]" value="${opt.data('category-id')}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${opt.data('sub-category-id')}">
                    <input type="hidden" name="items[${index}][product_id]" value="${pid}">
                </tr>
            `);

                    index++;
                });
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>

</body>
