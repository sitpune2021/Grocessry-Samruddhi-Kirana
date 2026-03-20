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

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ $warehouse->name ?? '' }}"
                                            readonly>
                                        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id ?? '' }}">
                                    </div>

                                    <div class="col-md-4">
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

                                    <div class="col-md-4">
                                        <label class="form-label">Challan No <span class="text-danger">*</span></label>
                                        <input type="text" name="challan_no" class="form-control"
                                            value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Challan Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="challan_date" class="form-control"
                                            value="{{ old('challan_date', isset($challan) ? $challan->challan_date->format('Y-m-d') : date('Y-m-d')) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <hr>
                                <hr>

                                @if ($mode !== 'view')
                                    <div class="row mb-3 align-items-end" id="selectionSection">

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
                                @endif
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
                                                @if ($mode !== 'view')
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody"></tbody>
                                    </table>
                                    <br>
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
            window.existingItems = @json($challan->items->load(['category', 'subCategory', 'product']));
            window.pageMode = "{{ $mode }}";
        </script>
    @endif
    <script>
        $(document).ready(function() {
            let index = 0;

            // Select2 Initialization
            $('#categorySelect').select2({
                closeOnSelect: false,
                placeholder: "Select Category",
                width: '100%'
            });
            $('#subCategorySelect').select2({
                closeOnSelect: false,
                placeholder: "Select Sub Category",
                width: '100%'
            });
            $('#productSelect').select2({
                closeOnSelect: false,
                placeholder: "Select Product",
                width: '100%'
            });

            // --- AUTO LOAD & FILL LOGIC ---

            // 1. Category Change -> Load & Auto-Select ALL Sub-Categories
            $('#categorySelect').on('change', function(e) {
                let ids = $(this).val();
                if (!ids || ids.length === 0) {
                    $('#subCategorySelect').val(null).trigger('change');
                    return;
                }

                $.get('/ajax/subcategories', {
                    category_ids: ids
                }, function(res) {
                    let subSelect = $('#subCategorySelect');
                    let newSubIds = [];

                    res.data.forEach(sc => {
                        if (!subSelect.find('option[value="' + sc.id + '"]').length) {
                            let newOpt = new Option(sc.name, sc.id, true,
                                true); // true, true केल्याने auto-select होईल
                            $(newOpt).attr('data-category-id', sc.category_id);
                            subSelect.append(newOpt);
                        }
                        newSubIds.push(sc.id.toString());
                    });

                    // फक्त निवडलेल्या कॅटेगरीच्याच सब-कॅटेगरी सिलेक्ट ठेवा
                    subSelect.val(newSubIds).trigger('change');
                });
            });

            // 2. Sub-Category Change -> Load & Auto-Select ALL Products
            $('#subCategorySelect').on('change', function(e) {
                let ids = $(this).val();
                if (!ids || ids.length === 0) {
                    $('#productSelect').val(null).trigger('change');
                    return;
                }

                $.get('/ajax/products-by-subcategory', {
                    sub_category_ids: ids
                }, function(res) {
                    let prodSelect = $('#productSelect');
                    let newProdIds = [];

                    res.data.forEach(p => {
                        if (!prodSelect.find('option[value="' + p.id + '"]').length) {
                            let opt = new Option(p.name, p.id, true,
                                true); // true, true केल्याने auto-select होईल
                            $(opt).attr('data-category-id', p.sub_category.category.id)
                                .attr('data-category-name', p.sub_category.category.name)
                                .attr('data-sub-category-id', p.sub_category.id)
                                .attr('data-sub-category-name', p.sub_category.name);
                            prodSelect.append(opt);
                        }
                        newProdIds.push(p.id.toString());
                    });

                    prodSelect.val(newProdIds).trigger('change');
                });
            });

            // --- REVERSE REMOVE LOGIC (जेव्हा एखादा टॅग 'x' करून काढाल) ---

            // Product काढला तर Sub-category काढा (जर त्याचे सर्व product गेले असतील तर)
            $('#productSelect').on('select2:unselect', function(e) {
                let subId = $(e.params.data.element).data('sub-category-id');
                let remainingProds = $('#productSelect option:selected').filter(function() {
                    return $(this).data('sub-category-id') == subId;
                }).length;

                if (remainingProds === 0) {
                    let currentSubs = $('#subCategorySelect').val() || [];
                    $('#subCategorySelect').val(currentSubs.filter(id => id != subId)).trigger('change');
                }
            });

            // Sub-category काढली तर Category काढा
            $('#subCategorySelect').on('select2:unselect', function(e) {
                let catId = $(e.params.data.element).data('category-id');

                // त्या सब-कॅटेगरीचे प्रॉडक्ट्स पण काढून टाका
                let subId = e.params.data.id;
                let currentProds = $('#productSelect').val() || [];
                let filteredProds = currentProds.filter(pid => {
                    return $('#productSelect option[value="' + pid + '"]').data(
                        'sub-category-id') != subId;
                });
                $('#productSelect').val(filteredProds).trigger('change');

                // कॅटेगरी चेक करा
                let remainingSubs = $('#subCategorySelect option:selected').filter(function() {
                    return $(this).data('category-id') == catId;
                }).length;

                if (remainingSubs === 0) {
                    let currentCats = $('#categorySelect').val() || [];
                    $('#categorySelect').val(currentCats.filter(id => id != catId)).trigger(
                        'change.select2');
                }
            });

            // --- बाकीचे Add Product आणि Table लॉजिक ---
            $('#addProductBtn').on('click', function() {
                let ids = $('#productSelect').val();
                if (!ids || ids.length === 0) return;
                $('#itemsSection').removeClass('d-none');
                ids.forEach(pid => {
                    if ($('#itemsBody input[value="' + pid + '"]').length) return;
                    let opt = $('#productSelect option[value="' + pid + '"]');
                    appendItemRow(opt.data('category-name'), opt.data('sub-category-name'), opt
                        .text(), opt.data('category-id'), opt.data('sub-category-id'), pid, '');
                });
            });

            function appendItemRow(catName, subName, prodName, catId, subId, prodId, qty) {
                $('#itemsBody').append(`
                <tr>
                    <td>${catName}</td><td>${subName}</td><td>${prodName}</td>
                    <td><input type="number" name="items[${index}][received_qty]" class="form-control" value="${qty}" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                    <input type="hidden" name="items[${index}][category_id]" value="${catId}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${subId}">
                    <input type="hidden" name="items[${index}][product_id]" value="${prodId}">
                </tr>`);
                index++;
            }

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>

</body>
