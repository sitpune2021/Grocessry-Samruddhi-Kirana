@extends('layouts.app')
@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row justify-content-center">
                <div class="col-12">

                    <div class="card shadow-sm border-0 rounded-3">

                    <!-- Card Header -->
                    <div class="card-header bg-white fw-semibold">
                       <h4> 
                        @if ($mode === 'add')
                        Add Warehouse Stock
                        @elseif($mode === 'edit')
                        Edit Stock
                        @else
                        View Stock
                        @endif
                        </h4>
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

                                    {{-- ===================== SUPPLIER CHALLAN ===================== --}}
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Supplier Challan <span class="text-danger">*</span>
                                            </label>

                                            {{-- Visible dropdown (disabled on view/edit so user can't change) --}}
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

                                            {{-- Hidden input always submitted (disabled select won't submit) --}}
                                            @if ($mode === 'view' || $mode === 'edit')
                                                <input type="hidden" name="supplier_challan_id"
                                                    value="{{ $selectedChallan->id ?? '' }}">
                                            @else
                                                <input type="hidden" name="supplier_challan_id"
                                                    id="supplier_challan_hidden">
                                            @endif

                                            @error('supplier_challan_id')
                                                <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror

                                            @if (session('error'))
                                                <span class="text-danger mt-1 d-block">
                                                    {{ session('error') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ===================== SUPPLIER ===================== --}}
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Supplier</label>
                                            <select name="supplier_id" id="supplier_id" class="form-select"
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

                                    {{-- ===================== WAREHOUSE ===================== --}}
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Warehouse</label>

                                            @if (Auth::user()->role_id == 1)
                                                <select name="warehouse_id" id="warehouse_id_select" class="form-control"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">-- Select Warehouse --</option>
                                                    @foreach ($warehouses as $w)
                                                        <option value="{{ $w->id }}"
                                                            {{ old('warehouse_id', $warehouse_stock->warehouse_id ?? ($userWarehouse->id ?? '')) == $w->id ? 'selected' : '' }}>
                                                            {{ $w->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($mode === 'view')
                                                    <input type="hidden" name="warehouse_id"
                                                        value="{{ $warehouse_stock->warehouse_id ?? '' }}">
                                                @endif
                                            @else
                                                @if ($mode === 'add')
                                                    <input type="text" class="form-control"
                                                        value="{{ $userWarehouse->name ?? 'N/A' }}" readonly>
                                                    <input type="hidden" name="warehouse_id"
                                                        value="{{ $userWarehouse->id ?? '' }}">
                                                @endif

                                                @if ($mode === 'view' || $mode === 'edit')
                                                    <input type="text" class="form-control"
                                                        value="{{ $stockWarehouse->name ?? 'N/A' }}" readonly>
                                                    <input type="hidden" name="warehouse_id"
                                                        value="{{ old('warehouse_id', $warehouse_stock->warehouse_id ?? '') }}">
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ===================== BILL NO ===================== --}}
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">
                                                Bill No <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="bill_no" id="bill_no" class="form-control"
                                                value="{{ old('bill_no', $warehouse_stock->bill_no ?? '') }}"
                                                placeholder="Enter bill number" {{ $mode === 'view' ? 'readonly' : '' }}>
                                            @error('bill_no')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ===================== BATCH NO ===================== --}}
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-medium">
                                                Batch No <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="batch_no" id="batch_no" class="form-control"
                                                value="{{ old('batch_no', $warehouse_stock->batch_no ?? '') }}"
                                                placeholder="Enter batch number" {{ $mode === 'view' ? 'readonly' : '' }}>
                                            @error('batch_no')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ===================== CHALLAN NO (hidden - filled by JS from API) ===================== --}}
                                    {{--
                                    ✅ FIX: challan_no is NOT a visible field.
                                    It is fetched from the API when challan is selected,
                                    and stored in this hidden input for form submission.
                                --}}
                                    <input type="hidden" name="challan_no" id="challan_no_hidden"
                                        value="{{ old('challan_no', $warehouse_stock->challan_no ?? '') }}">

                                    {{-- ===================== CHALLAN PRODUCTS TABLE ===================== --}}
                                    <div class="col-12 mt-4">
                                        <div class="card border">
                                            <div class="card-header bg-light fw-semibold">
                                                Supplier Challan Products
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mb-0" id="challanProductsTable">
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
                                                                <td colspan="5" class="text-center text-muted">
                                                                    Select Supplier Challan to view products
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===================== BUTTONS ===================== --}}
                                    <div class="mt-4 d-flex justify-content-end gap-2 text-end">
                                        <a href="{{ route('index.addStock.warehouse') }}" class="btn btn-success">
                                            Back
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
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ============================================================
            // 1. CHALLAN DROPDOWN → fetch products + fill fields
            // ============================================================
            function loadChallanData(challanId) {
                if (!challanId) return;

                fetch(`/get-supplier-challan/${challanId}`)
                    .then(res => res.json())
                    .then(data => {

                        // ✅ Fill Supplier
                        const supplierSelect = document.querySelector('[name="supplier_id"]');
                        if (supplierSelect) {
                            supplierSelect.value = data.supplier_id;
                            // 🔒 lock supplier when challan is selected
                            supplierSelect.disabled = true;
                        }

                        // ✅ Fill Warehouse (dropdown for superadmin, hidden for others)
                        const warehouseField = document.getElementById('warehouse_id_select');
                        if (warehouseField) {
                            warehouseField.value = data.warehouse_id;
                        }
                        // also handle hidden warehouse_id inputs (non-admin)
                        const warehouseHidden = document.querySelector('input[name="warehouse_id"]');
                        if (warehouseHidden) {
                            warehouseHidden.value = data.warehouse_id;
                        }

                        // ✅ FIX: Fill hidden challan_no input
                        const challanNoHidden = document.getElementById('challan_no_hidden');
                        if (challanNoHidden) {
                            challanNoHidden.value = data.challan_no;
                        }

                        // ✅ Fill products table
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
                            // ✅ FIX: hidden inputs are INSIDE a <td> so browser doesn't strip them
                            tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.category}</td>
                            <td>${item.sub_category}</td>
                            <td>${item.product}</td>
                            <td>${item.quantity}</td>
                            <td style="display:none">
                                <input type="hidden" name="products[${index}][category_id]"     value="${item.category_id}">
                                <input type="hidden" name="products[${index}][sub_category_id]" value="${item.sub_category_id}">
                                <input type="hidden" name="products[${index}][product_id]"      value="${item.product_id}">
                                <input type="hidden" name="products[${index}][quantity]"        value="${item.quantity}">
                            </td>
                        </tr>`;
                        });
                    })
                    .catch(err => {
                        console.error('Supplier challan fetch error:', err);
                    });
            }

            // ============================================================
            // 2. On visible challan dropdown change (ADD mode)
            // ============================================================
            const challanSelect = document.getElementById('supplier_challan_id');
            if (challanSelect) {
                challanSelect.addEventListener('change', function() {

                    // Sync hidden input for form submit
                    const hidden = document.getElementById('supplier_challan_hidden');
                    if (hidden) hidden.value = this.value;

                    // Lock/unlock supplier
                    const supplierSelect = document.querySelector('[name="supplier_id"]');
                    if (supplierSelect) {
                        supplierSelect.disabled = !!this.value;
                    }

                    loadChallanData(this.value);
                });
            }

            // ============================================================
            // 3. AUTO LOAD on VIEW / EDIT (challan already selected)
            //    Read from hidden input because select is disabled
            // ============================================================
            const hiddenChallanInput = document.querySelector('input[name="supplier_challan_id"]');
            if (hiddenChallanInput && hiddenChallanInput.value) {
                loadChallanData(hiddenChallanInput.value);
            }

            // Also auto-load if visible select has a value (ADD mode with old() repopulation)
            if (challanSelect && challanSelect.value) {
                loadChallanData(challanSelect.value);
            }

        });
    </script>
@endpush
