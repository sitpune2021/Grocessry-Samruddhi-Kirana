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

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="row mb-4">

                                    <div class="col-md-4">
                                        <label class="form-label">Warehouse <span class="text-danger">*</span></label>                                      

                                        @if(auth()->user()->role_id == 1 && $mode !== 'view')
                                            {{-- SUPER ADMIN → dropdown --}}
                                                <select name="warehouse_id" class="form-select">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}"
                                                            {{ old('warehouse_id', $challan->warehouse_id ?? '') == $w->id ? 'selected' : '' }}>
                                                            {{ $w->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                        @else
                                            {{-- MASTER USER → readonly --}}

                                                <input type="text" class="form-control"
                                                    value="{{ $warehouse->name ?? '' }}" readonly>
                                                <input type="hidden" name="warehouse_id"
                                                    value="{{ $warehouse->id ?? '' }}">

                                        @endif

                                        @error('warehouse_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
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
                                        @error('supplier_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Challan No <span class="text-danger">*</span></label>
                                        <input type="text" name="challan_no" class="form-control"
                                            value="{{ old('challan_no', $challan->challan_no ?? $autoChallanNo) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                        @error('challan_no')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Challan Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="challan_date" class="form-control"
                                            value="{{ old('challan_date', isset($challan) ? $challan->challan_date->format('Y-m-d') : date('Y-m-d')) }}"
                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                        @error('challan_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

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

                                    <button type="button" id="addProductBtn" class="btn btn-success mb-3">
                                        + Add Product
                                    </button>
                                @endif

                                {{-- TABLE SECTION --}}
                                <div id="itemsSection"
                                    class="{{ isset($challan) && count($challan->items) > 0 ? '' : 'd-none' }} mt-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">SR.NO</th>
                                                <th>Category</th>
                                                <th>Sub Category</th>
                                                <th>Product</th>
                                                <th style="width: 150px;">Qty</th>
                                                @if ($mode !== 'view')
                                                    <th style="width: 50px;">Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                            @if (isset($challan) && $challan->items)
                                                @foreach ($challan->items as $item)
                                                    <tr>
                                                        <td class="sr-no">{{ $loop->iteration }}</td>
                                                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                                                        <td>{{ $item->subCategory->name ?? 'N/A' }}</td>
                                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <input type="number"
                                                                name="items[{{ $loop->index }}][received_qty]"
                                                                class="form-control"
                                                                value="{{ old('items.' . $loop->index . '.received_qty', $item->received_qty) }}"
                                                                {{ $mode === 'view' ? 'readonly' : '' }}
                                                                min="1">
                                                                @error('items.' . $loop->index . '.received_qty')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                        </td>
                                                        @if ($mode !== 'view')
                                                            <td><button type="button"
                                                                    class="btn btn-danger btn-sm removeRow">X</button>
                                                            </td>
                                                        @endif
                                                        {{-- Hidden Inputs to preserve IDs during Update --}}
                                                        <input type="hidden"
                                                            name="items[{{ $loop->index }}][category_id]"
                                                            value="{{ $item->category_id }}">
                                                        <input type="hidden"
                                                            name="items[{{ $loop->index }}][sub_category_id]"
                                                            value="{{ $item->sub_category_id }}">
                                                        <input type="hidden"
                                                            name="items[{{ $loop->index }}][product_id]"
                                                            value="{{ $item->product_id }}">
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    <br>
                                    @if ($mode !== 'view')
                                        <button type="submit" class="btn btn-success">
                                            @if ($mode === 'edit')
                                                Update Supplier Challan
                                            @else
                                                Save Supplier Challan
                                            @endif
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

</body>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Important: Start index from existing row count to prevent overwriting data
            let index = $('#itemsBody tr').length;

            // Select2 Initialization
            $('#categorySelect, #subCategorySelect, #productSelect').select2({
                closeOnSelect: false,
                placeholder: "Select options",
                width: '100%'
            });

            // 1. Category Change -> Load & Auto-Select ALL Sub-Categories
            $('#categorySelect').on('change', function() {
                let ids = $(this).val();
                if (!ids || ids.length === 0) {
                    $('#subCategorySelect').empty().trigger('change');
                    return;
                }

                $.get('/ajax/subcategories', {
                    category_ids: ids
                }, function(res) {
                    let subSelect = $('#subCategorySelect');
                    let newSubIds = [];

                    res.data.forEach(sc => {
                        if (!subSelect.find('option[value="' + sc.id + '"]').length) {
                            let newOpt = new Option(sc.name, sc.id, true, true);
                            $(newOpt).attr('data-category-id', sc.category_id);
                            subSelect.append(newOpt);
                        }
                        newSubIds.push(sc.id.toString());
                    });
                    subSelect.val(newSubIds).trigger('change');
                });
            });

            // 2. Sub-Category Change -> Load & Auto-Select ALL Products
            $('#subCategorySelect').on('change', function() {
                let ids = $(this).val();
                if (!ids || ids.length === 0) {
                    $('#productSelect').empty().trigger('change');
                    return;
                }

                $.get('/ajax/products-by-subcategory', {
                    sub_category_ids: ids
                }, function(res) {
                    let prodSelect = $('#productSelect');
                    let newProdIds = [];

                    res.data.forEach(p => {
                        if (!prodSelect.find('option[value="' + p.id + '"]').length) {
                            let opt = new Option(p.name, p.id, true, true);
                            $(opt).attr({
                                'data-category-id': p.sub_category.category.id,
                                'data-category-name': p.sub_category.category.name,
                                'data-sub-category-id': p.sub_category.id,
                                'data-sub-category-name': p.sub_category.name
                            });
                            prodSelect.append(opt);
                        }
                        newProdIds.push(p.id.toString());
                    });
                    prodSelect.val(newProdIds).trigger('change');
                });
            });

            // --- REVERSE REMOVE LOGIC ---
            $('#productSelect').on('select2:unselect', function(e) {
                let subId = $(e.params.data.element).data('sub-category-id');
                let remainingInSub = $('#productSelect option:selected').filter(function() {
                    return $(this).data('sub-category-id') == subId;
                }).length;

                if (remainingInSub === 0) {
                    let currentSubs = $('#subCategorySelect').val() || [];
                    $('#subCategorySelect').val(currentSubs.filter(id => id != subId)).trigger('change');
                }
            });

            $('#subCategorySelect').on('select2:unselect', function(e) {
                let subId = e.params.data.id;
                let catId = $(e.params.data.element).data('category-id');

                let currentProds = $('#productSelect').val() || [];
                let filteredProds = currentProds.filter(pid => {
                    return $('#productSelect option[value="' + pid + '"]').data(
                        'sub-category-id') != subId;
                });
                $('#productSelect').val(filteredProds).trigger('change');

                let remainingInCat = $('#subCategorySelect option:selected').filter(function() {
                    return $(this).data('category-id') == catId;
                }).length;

                if (remainingInCat === 0) {
                    let currentCats = $('#categorySelect').val() || [];
                    $('#categorySelect').val(currentCats.filter(id => id != catId)).trigger(
                        'change.select2');
                }
            });

            // --- Table Logic ---
            $('#addProductBtn').on('click', function() {
                let ids = $('#productSelect').val();
                if (!ids || ids.length === 0) return;

                $('#itemsSection').removeClass('d-none');
                ids.forEach(pid => {
                    if ($(`#itemsBody input[value="${pid}"][name*="product_id"]`).length > 0)
                return;
                    let opt = $('#productSelect option[value="' + pid + '"]');
                    appendItemRow(opt.data('category-name'), opt.data('sub-category-name'), opt
                        .text(), opt.data('category-id'), opt.data('sub-category-id'), pid);
                });
                updateSrNo();
            });

            function appendItemRow(catName, subName, prodName, catId, subId, prodId) {
                let html = `
                <tr>
                    <td class="sr-no"></td>
                    <td>${catName}</td>
                    <td>${subName}</td>
                    <td>${prodName}</td>
                    <td><input type="number" name="items[${index}][received_qty]" class="form-control" placeholder="Qty" required min="1"></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                    <input type="hidden" name="items[${index}][category_id]" value="${catId}">
                    <input type="hidden" name="items[${index}][sub_category_id]" value="${subId}">
                    <input type="hidden" name="items[${index}][product_id]" value="${prodId}">
                </tr>`;
                $('#itemsBody').append(html);
                index++;
            }

            function updateSrNo() {
                $('#itemsBody tr').each(function(i) {
                    $(this).find('.sr-no').text(i + 1);
                });
            }

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateSrNo();
                if ($('#itemsBody tr').length === 0) $('#itemsSection').addClass('d-none');
            });
        });
    </script>
