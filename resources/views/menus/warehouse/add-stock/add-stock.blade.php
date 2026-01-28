@include('layouts.header')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">

                @include('layouts.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row justify-content-center">
                            <div class="col-12">

                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">
                                        <i class="bx bx-box me-1"></i>
                                        @if ($mode === 'add')
                                        Add Warehouse Stock
                                        @elseif($mode === 'edit')
                                        Edit Stock
                                        @else
                                        View Stock
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form
                                            action="{{ $mode === 'edit' ? route('stock.update', $warehouse_stock->id) : route('warehouse.addStock') }}"
                                            enctype="multipart/form-data" method="POST">
                                            @csrf
                                            @if ($mode === 'edit')
                                            @method('PUT')
                                            @endif

                                            <div class="row">
                                                {{-- Supplier Challan --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Supplier Challan <span
                                                                class="text-danger">
                                                                *</span></label>
                                                        <select id="supplier_challan_id" class="form-select"
                                                            {{ $mode === 'view' || $mode === 'edit' ? 'disabled' : '' }}>
                                                            <option value="">-- Select Challan --</option>
                                                            @foreach ($challans as $challan)
                                                            <option value="{{ $challan->id }}"
                                                                {{ isset($selectedChallan) && $selectedChallan->id == $challan->id ? 'selected' : '' }}>
                                                                {{ $challan->challan_no }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        {{-- ✅ REAL value for submit --}}
                                                        @if ($mode === 'view' || $mode === 'edit')
                                                        <input type="hidden" name="supplier_challan_id"
                                                            value="{{ $selectedChallan->id ?? '' }}">
                                                        @else
                                                        {{-- add mode --}}
                                                        <input type="hidden" name="supplier_challan_id"
                                                            id="supplier_challan_hidden">
                                                        @endif
                                                        @if (session('error'))
                                                        <span class="text-danger mt-1 d-block">
                                                            {{ session('error') }}
                                                        </span>
                                                        @endif

                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Supplier</label>
                                                        <select name="supplier_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Supplier</option>
                                                            @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}"
                                                                {{ old('supplier_id', $warehouse_stock->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                                                {{ $supplier->supplier_name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('supplier_id')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                {{-- Warehouse --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse</label>

                                                        {{-- SUPER ADMIN → DROPDOWN --}}
                                                        @if (Auth::user()->role_id == 1)

                                                        <select name="warehouse_id" class="form-control"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                                            <option value="">-- Select Warehouse --</option>

                                                            @foreach ($warehouses as $w)
                                                            <option value="{{ $w->id }}"
                                                                {{ old('warehouse_id', $warehouse_stock->warehouse_id ?? ($userWarehouse->id ?? '')) == $w->id
                                                                            ? 'selected'
                                                                            : '' }}>
                                                                {{ $w->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        {{-- disabled select won't submit --}}
                                                        @if ($mode === 'view')
                                                        <input type="hidden" name="warehouse_id"
                                                            value="{{ $warehouse_stock->warehouse_id }}">
                                                        @endif

                                                        {{-- OTHER USERS → KEEP CURRENT LOGIC --}}
                                                        @else
                                                        @if ($mode === 'add')
                                                        <input type="text" class="form-control"
                                                            value="{{ $userWarehouse->name ?? 'N/A' }}"
                                                            readonly>

                                                        <input type="hidden" name="warehouse_id"
                                                            value="{{ $userWarehouse->id }}">
                                                        @endif

                                                        @if ($mode === 'view' || $mode === 'edit')
                                                        <input type="text"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}
                                                            class="form-control"
                                                            value="{{ $stockWarehouse->name ?? 'N/A' }}"
                                                            readonly>

                                                        <input type="hidden" name="warehouse_id"
                                                            value="{{ old('warehouse_id', $warehouse_stock->warehouse_id) }}">
                                                        @endif

                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Category --}}
                                                {{-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Category <span class="text-danger">
                                                                *</span></label>
                                                        <select name="category_id" id="category_id" class="form-select "
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                                <option value="">Select Category</option>

                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $warehouse_stock->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach

                                                </select>

                                                @error('category_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                    </div> --}}

                                    {{-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Sub Category <span
                                                                class="text-danger">*</span></label>
                                                        <select name="sub_category_id" id="sub_category_id"
                                                            class="form-select">
                                                            <option value="">-- Select Sub Category --</option>

                                                            @if (isset($sub_categories))
                                                                @foreach ($sub_categories as $sub)
                                                                    <option value="{{ $sub->id }}"
                                    {{ old('sub_category_id', $warehouse_stock->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                    </option>
                                    @endforeach
                                    @endif
                                    </select>

                                    @error('sub_category_id')
                                    <span class="text-danger mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="product_id">Product <span
                                                                class="text-danger">*</span></label>

                                                        <select name="product_id" id="product_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                            <option value="">-- Select Product --</option>

                            @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                {{ old('product_id', $warehouse_stock->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach

                            </select>


                            @error('product_id')
                            <span class="text-danger mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div> --}}


                    {{-- qty --}}
                    {{-- <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quantity <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="quantity"
                                                            class="form-control"
                                                            value="{{ old('quantity', $warehouse_stock->quantity ?? '') }}"
                    {{ $mode === 'view' ? 'readonly' : '' }}
                    placeholder="Quantity">
                    @error('quantity')
                    <span class="text-danger mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}

            <div class="col-md-4">
                <label class="form-label fw-medium">
                    Bill No <span class="text-danger">*</span>
                </label>
                <input type="text" name="bill_no" class="form-control"
                    value="{{ old('bill_no', $warehouse_stock->bill_no ?? '') }}"
                    placeholder="Enter bill number"
                    {{ $mode === 'view' ? 'readonly' : '' }}>
                @error('bill_no')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>


            <div class="col-md-4">
                <label class="form-label fw-medium">
                    Batch No <span class="text-danger">*</span>
                </label>
                <input type="text" name="batch_no" class="form-control"
                    value="{{ old('batch_no', $warehouse_stock->batch_no ?? '') }}"
                    placeholder="Enter batch number"
                    {{ $mode === 'view' ? 'readonly' : '' }}>
                @error('batch_no')
                <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Supplier Challan Products Table --}}
            <div class="col-12 mt-4">
                <div class="card border">
                    <div class="card-header bg-light fw-semibold">
                        Supplier Challan Products
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0"
                                id="challanProductsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5"
                                            class="text-center text-muted">
                                            Select Supplier Challan to view products
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Buttons (Right Aligned) -->
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('index.addStock.warehouse') }}"
                    class="btn btn-success">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                @if ($mode === 'add')
                <button type="submit" class="btn btn-success">
                    Save Stock
                </button>
                @elseif($mode === 'edit')
                <button type="submit" class="btn btn-success">
                    Update Stock
                </button>
                @endif
            </div>

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
    </div>
    </div>

    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {

        const categorySelect = document.getElementById('category_id');
        const productSelect = document.getElementById('product_id');

        categorySelect.addEventListener('change', function() {

            const categoryId = this.value;
            productSelect.innerHTML = '<option value="">Loading...</option>';

            if (!categoryId) {
                productSelect.innerHTML = '<option value="">-- Select Product --</option>';
                return;
            }

            fetch(`/get-products-by-category/${categoryId}`)
                .then(res => res.json())
                .then(data => {

                    productSelect.innerHTML = '<option value="">-- Select Product --</option>';

                    if (data.length === 0) {
                        productSelect.innerHTML +=
                            '<option value="">No products found</option>';
                    }

                    data.forEach(product => {
                        productSelect.innerHTML += `
                        <option value="${product.id}">
                            ${product.name}
                        </option>`;
                    });
                })
                .catch(() => {
                    productSelect.innerHTML =
                        '<option value="">Error loading products</option>';
                });
        });

    });
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const subCategorySelect = document.getElementById('sub_category_id');
        const productSelect = document.getElementById('product_id');

        const selectedProduct =
            "{{ old('product_id', $warehouse_stock->product_id ?? '') }}";

        function loadProducts(subCategoryId, selectedId = null) {

            if (!subCategoryId) {
                productSelect.innerHTML = '<option value="">-- Select Product --</option>';
                return;
            }

            productSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/get-products-by-sub-category/${subCategoryId}`)
                .then(res => res.json())
                .then(data => {

                    productSelect.innerHTML =
                        '<option value="">-- Select Product --</option>';

                    if (data.length === 0) {
                        productSelect.innerHTML +=
                            '<option value="">No products found</option>';
                    }

                    data.forEach(product => {
                        const selected =
                            selectedId == product.id ? 'selected' : '';
                        productSelect.innerHTML += `
                        <option value="${product.id}" ${selected}>
                            ${product.name}
                        </option>`;
                    });
                })
                .catch(() => {
                    productSelect.innerHTML =
                        '<option value="">Error loading products</option>';
                });
        }


        subCategorySelect.addEventListener('change', function() {
            loadProducts(this.value);
        });


        if (subCategorySelect.value) {
            loadProducts(subCategorySelect.value, selectedProduct);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const categorySelect = document.getElementById('category_id');
        const subCategorySelect = document.getElementById('sub_category_id');
        const selectedSubCategory = "{{ old('sub_category_id', $warehouse_stock->sub_category_id ?? '') }}";

        function loadSubCategories(categoryId, selectedId = null) {

            if (!categoryId) {
                subCategorySelect.innerHTML = '<option value="">-- Select Sub Category --</option>';
                return;
            }

            fetch(`/get-sub-categories/${categoryId}`)
                .then(res => res.json())
                .then(data => {

                    subCategorySelect.innerHTML = '<option value="">-- Select Sub Category --</option>';

                    if (data.length === 0) {
                        subCategorySelect.innerHTML += '<option>No sub category found</option>';
                    }

                    data.forEach(item => {
                        const selected = selectedId == item.id ? 'selected' : '';
                        subCategorySelect.innerHTML += `
                        <option value="${item.id}" ${selected}>
                            ${item.name}
                        </option>`;
                    });
                });
        }

        // 🔹 On category change
        categorySelect.addEventListener('change', function() {
            loadSubCategories(this.value);
        });

        // 🔹 AUTO LOAD on edit page
        if (categorySelect.value) {
            loadSubCategories(categorySelect.value, selectedSubCategory);
        }

    });
</script>
<script>
    document.getElementById('supplier_challan_id')
        ?.addEventListener('change', function() {

            if (!this.value) return;

            fetch(`/get-supplier-challan/${this.value}`)
                .then(res => res.json())
                .then(data => {

                    /* ===============================
                       ✅ AUTO FILL FIELDS
                    =============================== */

                    // Supplier
                    const supplierSelect = document.querySelector('[name="supplier_id"]');
                    if (supplierSelect) {
                        supplierSelect.value = data.supplier_id;
                    }

                    // Warehouse (dropdown or hidden both supported)
                    const warehouseField = document.querySelector('[name="warehouse_id"]');
                    if (warehouseField) {
                        warehouseField.value = data.warehouse_id;
                    }

                    // Challan No
                    const challanInput = document.querySelector('[name="challan_no"]');
                    if (challanInput) {
                        challanInput.value = data.challan_no;
                    }

                    /* ===============================
                       ✅ FILL PRODUCTS TABLE
                    =============================== */

                    const tbody = document.querySelector('#challanProductsTable tbody');
                    tbody.innerHTML = '';

                    if (!data.items || data.items.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No products found
                            </td>
                        </tr>`;
                        return;
                    }

                    data.items.forEach((item, index) => {
                        tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.category}</td>
                            <td>${item.sub_category}</td>
                            <td>${item.product}</td>
                            <td>${item.quantity}</td>

                            <!-- 🔒 hidden inputs for submit -->
                            <input type="hidden" name="products[${index}][category_id]" value="${item.category_id}">
                            <input type="hidden" name="products[${index}][sub_category_id]" value="${item.sub_category_id}">
                            <input type="hidden" name="products[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                        </tr>
                    `;
                    });

                })
                .catch(err => {
                    console.error('Supplier challan fetch error:', err);
                });
        });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const challanSelect = document.getElementById('supplier_challan_id');

        // ✅ AUTO LOAD challan products on VIEW / EDIT
        if (challanSelect && challanSelect.value) {
            challanSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
<script>
    document.getElementById('supplier_challan_id')
        ?.addEventListener('change', function() {
            const hidden = document.getElementById('supplier_challan_hidden');
            if (hidden) {
                hidden.value = this.value;
            }
        });
</script>