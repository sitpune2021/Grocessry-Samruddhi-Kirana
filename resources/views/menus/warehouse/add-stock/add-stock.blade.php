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
                                                        <label class="form-label">
                                                            Supplier Challan <span class="text-danger">*</span>
                                                        </label>

                                                        {{-- Visible dropdown --}}
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

                                                        {{-- Hidden field (because disabled select does not submit) --}}
                                                        @if ($mode === 'view' || $mode === 'edit')
                                                            <input type="hidden" name="supplier_challan_id"
                                                                value="{{ $selectedChallan->id ?? '' }}">
                                                        @else
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
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const challanSelect = document.getElementById('supplier_challan_id');
        const supplierSelect = document.querySelector('[name="supplier_id"]');

        if (!challanSelect || !supplierSelect) return;

        function lockSupplierIfChallanSelected() {
            if (challanSelect.value) {
                supplierSelect.disabled = true; // 🔒 lock dropdown
            } else {
                supplierSelect.disabled = false; // 🔓 allow manual selection
            }
        }

        // On page load (EDIT / VIEW case)l
        lockSupplierIfChallanSelected();

        // On challan change (ADD case)
        challanSelect.addEventListener('change', lockSupplierIfChallanSelected);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 🔥 IMPORTANT: read from hidden input, NOT disabled select
        const hiddenChallanInput = document.querySelector('input[name="supplier_challan_id"]');

        if (!hiddenChallanInput || !hiddenChallanInput.value) return;

        const challanId = hiddenChallanInput.value;

        fetch(`/get-supplier-challan/${challanId}`)
            .then(res => res.json())
            .then(data => {

                // ✅ Supplier
                const supplierSelect = document.querySelector('[name="supplier_id"]');
                if (supplierSelect) {
                    supplierSelect.value = data.supplier_id;
                }

                // ✅ Warehouse
                const warehouseField = document.querySelector('[name="warehouse_id"]');
                if (warehouseField) {
                    warehouseField.value = data.warehouse_id;
                }

                // ✅ Products table
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
                    </tr>`;
                });
            })
            .catch(err => {
                console.error('Auto load challan failed:', err);
            });

    });
</script>
