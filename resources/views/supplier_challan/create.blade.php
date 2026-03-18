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

            // ✅ Load existing items INSIDE ready, so index is accessible
            if (typeof window.existingItems !== 'undefined' && window.existingItems.length > 0) {
                $('#itemsSection').removeClass('d-none');

                window.existingItems.forEach(function(item) {
                    let isView = window.pageMode === 'view';

                    let categoryName = item.category ? item.category.name : '';
                    let subCategoryName = item.sub_category ? item.sub_category.name : '';
                    let productName = item.product ? item.product.name : '';

                    let qtyInput = isView ?
                        `<input type="number" class="form-control" value="${item.received_qty}" readonly>` :
                        `<input type="number" name="items[${index}][received_qty]" class="form-control" value="${item.received_qty}" required>`;

                    let actionBtn = isView ?
                        '' :
                        `<button type="button" class="btn btn-danger btn-sm removeRow">X</button>`;

                    $('#itemsBody').append(`
                    <tr>
                        <td>${categoryName}</td>
                        <td>${subCategoryName}</td>
                        <td>${productName}</td>
                        <td>${qtyInput}</td>
                        <td>${actionBtn}</td>
                        <input type="hidden" name="items[${index}][category_id]"     value="${item.category_id}">
                        <input type="hidden" name="items[${index}][sub_category_id]" value="${item.sub_category_id}">
                        <input type="hidden" name="items[${index}][product_id]"      value="${item.product_id}">
                    </tr>
                `);
                    index++;
                });
            }

            // Select2 init
            $('#categorySelect, #subCategorySelect, #productSelect').select2({
                closeOnSelect: false,
                width: '100%',
                placeholder: "Select options"
            });

            // ... rest of your existing JS unchanged ...
        });
    </script>

</body>
