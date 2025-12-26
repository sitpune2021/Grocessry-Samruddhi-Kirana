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
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <!-- Form card -->
                        <div class="col-12 col-md-10 col-lg-12">
                            <div class="card mb-4">
                                <h4 class="card-header text-center">
                                    District Wise Warehouse-to-Warehouse Stock Transfer
                                </h4>

                                <div class="card-body">

                                    <form id="transferForm" method="POST"
                                        action="{{ isset($transfer) 
                                            ? route('transfer.update', $transfer->id) 
                                            : route('transfer.store') }}">

                                        @csrf
                                        @if(isset($transfer))
                                        @method('PUT')
                                        @endif

                                        <input type="hidden" name="category_id[]" value="1">
                                        <!-- Row 1: FROM & TO -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="from_warehouse_id" class="form-label">From Warehouse <span class="text-danger">*</span></label>
                                                <select name="from_warehouse_id" id="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ (isset($transfer) && $transfer->from_warehouse_id == $w->id) ? 'selected' : '' }}>
                                                        {{ $w->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('from_warehouse_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="to_warehouse_id" class="form-label">To Warehouse <span class="text-danger">*</span></label>
                                                <select name="to_warehouse_id" id="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ (isset($transfer) && $transfer->to_warehouse_id == $w->id) ? 'selected' : '' }}>
                                                        {{ $w->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('to_warehouse_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: CATEGORY & PRODUCT -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6 d-none">

                                                <label class="form-label fw-semibold">
                                                    Category <span class="text-danger">*</span>
                                                </label>

                                                <select name="category_id[]" id="category_id"
                                                    class="form-control form-select"
                                                    multiple>
                                                    <option value="all">Select All</option>

                                                    @foreach($categories as $c)
                                                    <option value="{{ $c->id }}">
                                                        {{ $c->name }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('category_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!--     <div class="col-md-6">
                                              
                                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $c)
                                                    <option value="{{ $c->id }}"
                                                        {{ (isset($transfer) && $transfer->category_id == $c->id) ? 'selected' : '' }}>
                                                        {{ $c->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                             -->

                                            <div class="col-md-6">
                                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                                <select name="product_id[]" id="product_id"
                                                    class="form-control form-select"
                                                    multiple>
                                                    <option value="">Select All</option>

                                                    @if(isset($products))
                                                    @foreach($products as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ (isset($transfer) && $transfer->product_id == $p->id) ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                                @error('product_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- <div class="col-md-6">
                                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                                <select name="product_id" id="product_id" class="form-select">
                                                    <option value="">Select Product</option>

                                                    @if(isset($products))
                                                    @foreach($products as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ (isset($transfer) && $transfer->product_id == $p->id) ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                                @error('product_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div> -->
                                        </div>

                                        <!-- Row 3: BATCH & QTY -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                                                <select name="batch_id[]" id="batch_id"
                                                    class="form-control form-select"
                                                    multiple>
                                                    <option value="">Select Batch</option>

                                                    @if(isset($batches))
                                                    @foreach($batches as $b)
                                                    <option value="{{ $b->id }}"
                                                        {{ (isset($transfer) && $transfer->batch_id == $b->id) ? 'selected' : '' }}>
                                                        {{ $b->batch_no }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                                @error('batch_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    name="quantity"
                                                    id="quantity"
                                                    min="1" placeholder="Enter quantity"
                                                    value="{{ old('quantity', $transfer->quantity ?? '') }}"
                                                    class="form-control ">

                                                @if($errors->has('quantity'))
                                                <small class="text-danger">{{ $errors->first('quantity') }}</small>
                                                @endif
                                                <small id="qtyError" class="text-danger" style="display:none;"></small>
                                            </div>
                                        </div>

                                        @if(isset($transfer))
                                        <input type="hidden" id="old_batch_id" value="{{ $transfer->batch_id }}">
                                        @endif

                                        @if(isset($transfer))
                                        <input type="hidden" id="old_category_id" value="{{ $transfer->category_id }}">
                                        <input type="hidden" id="old_product_id" value="{{ $transfer->product_id }}">
                                        <input type="hidden" id="old_batch_id" value="{{ $transfer->batch_id }}">
                                        @endif

                                        @if(isset($transfer))
                                        <input type="hidden" id="old_to_warehouse_id" value="{{ $transfer->to_warehouse_id }}">
                                        @endif


                                        <!-- Buttons -->
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('transfer.index') }}" class="btn btn-outline-secondary">Back</a>
                                            <button type="button" class="btn btn-primary" id="addItemBtn">
                                                Add
                                            </button>
                                        </div>

                                        <!-- Table -->
                                        <div class="table-responsive mt-4" id="workOrderTableWrapper" style="display: none;">
                                            <table class="table table-bordered" id="workOrderTable">
                                                <thead>
                                                    <tr>
                                                        <th>From Warehouse</th>
                                                        <th>To Warehouse</th>
                                                        <th>Category</th>
                                                        <th>Product</th>
                                                        <th>Batch</th>
                                                        <th>Quantity</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <div id="itemsContainer"></div>

                                            <div class="text-end mt-3">
                                                <button type="submit" class="btn btn-success">
                                                    Product Transfer
                                                </button>

                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Content wrapper -->
            </div>

            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>


<!-- edit mode get warehouse wise category -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fromWarehouse = document.getElementById('from_warehouse_id');

        if (fromWarehouse.value) {
            fromWarehouse.dispatchEvent(new Event('change'));
        }
    });
</script>

<!-- multiple product Transfer with edit-->
<script>
    let index = 0;
    let editingIndex = null;

    // cache elements
    const fromWarehouseEl = document.getElementById('from_warehouse_id');
    const toWarehouseEl = document.getElementById('to_warehouse_id');
    const categoryEl = document.getElementById('category_id');
    const productEl = document.getElementById('product_id');
    const batchEl = document.getElementById('batch_id');
    const qtyEl = document.getElementById('quantity');
    const tableWrapper = document.getElementById('workOrderTableWrapper');
    const tableBody = document.querySelector('#workOrderTable tbody');
    const itemsContainer = document.getElementById('itemsContainer');

    document.getElementById('addItemBtn').addEventListener('click', function() {

        const fromWarehouse = fromWarehouseEl.value;
        const toWarehouse = toWarehouseEl.value;
        const categoryId = document.querySelector('input[name="category_id[]"]').value;

        const productIds = $('#product_id').val(); // ARRAY
        const batchIds = $('#batch_id').val(); // ARRAY
        const quantity = qtyEl.value; // "10,20,30"

        if (!fromWarehouse || !toWarehouse || !productIds || !batchIds || !quantity) {
            alert('Please fill all fields');
            return;
        }

        const quantityList = quantity.split(',').map(q => q.trim());

        if (
            productIds.length !== batchIds.length ||
            batchIds.length !== quantityList.length
        ) {
            alert('Product, Batch and Quantity count mismatch');
            return;
        }
 
        // 🔥 LOOP = MULTIPLE ROWS
        productIds.forEach((productId, i) => {

            const batchId = batchIds[i];
            const qty = quantityList[i];

            const rowIndex = index++;

            const row = `
        <tr id="row_${rowIndex}">
            <td>${fromWarehouseEl.options[fromWarehouseEl.selectedIndex].text}</td>
            <td>${toWarehouseEl.options[toWarehouseEl.selectedIndex].text}</td>
            <td>Category</td>
            <td>${$('#product_id option[value="'+productId+'"]').text()}</td>
            <td>${$('#batch_id option[value="'+batchId+'"]').text()}</td>
            <td>${qty}</td>
            <td>
                <button type="button" class="btn btn-warning btn-sm me-1 edit-row" data-index="${rowIndex}">Edit</button>
                <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${rowIndex}">Remove</button>
            </td>
        </tr>
        `;

            tableWrapper.style.display = 'block';
            tableBody.insertAdjacentHTML('beforeend', row);

            itemsContainer.insertAdjacentHTML('beforeend', `
    <input type="hidden" name="items[${rowIndex}][from_warehouse_id]" value="${fromWarehouse}">
    <input type="hidden" name="items[${rowIndex}][to_warehouse_id]" value="${toWarehouse}">
    <input type="hidden" name="items[${rowIndex}][category_id]" value="${categoryId}">
    <input type="hidden" name="items[${rowIndex}][product_id]" value="${productId}">
    <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchId}">
    <input type="hidden" name="items[${rowIndex}][quantity]" value="${qty}">
    `);
        });


        resetFullForm();
    });


    /* ================= EDIT ================= */

    async function editRow(i) {
        editingIndex = i;

        const getVal = name =>
            document.querySelector(`[name="items[${i}][${name}]"]`)?.value;

        fromWarehouseEl.value = getVal('from_warehouse_id');
        toWarehouseEl.value = getVal('to_warehouse_id');

        // trigger warehouse → category
        fromWarehouseEl.dispatchEvent(new Event('change'));

        await waitForOptions(categoryEl, getVal('category_id'));
        categoryEl.value = getVal('category_id');
        categoryEl.dispatchEvent(new Event('change'));

        await waitForOptions(productEl, getVal('product_id'));
        productEl.value = getVal('product_id');
        productEl.dispatchEvent(new Event('change'));

        await waitForOptions(batchEl, getVal('batch_id'));
        batchEl.value = getVal('batch_id');



        removeRow(i);
    }

    /* ================= HELPERS ================= */

    function removeRow(i) {
        document.getElementById(`row_${i}`)?.remove();
        document.querySelectorAll(`[name^="items[${i}]"]`).forEach(el => el.remove());
    }

    function resetFullForm() {
        productEl.innerHTML = '<option value="">Select Product</option>';
        batchEl.innerHTML = '<option value="">Select Batch</option>';
        qtyEl.value = '';
        categoryEl.value = '';
        fromWarehouseEl.value = '';
        toWarehouseEl.value = '';
    }

    /* wait till dropdown options are loaded */
    function waitForOptions(selectEl, value) {
        return new Promise(resolve => {
            const interval = setInterval(() => {
                if ([...selectEl.options].some(o => o.value == value)) {
                    clearInterval(interval);
                    resolve();
                }
            }, 100);
        });
    }
</script>


<!-- multiple selection script  -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {

        $('#category_id').select2({
            placeholder: 'Select Category',
            closeOnSelect: false,
            width: '100%'
        });

        $('#product_id').select2({
            placeholder: 'Select Product',
            closeOnSelect: false,
            width: '100%'
        });

        $('#batch_id').select2({
            placeholder: 'Select Batch',
            closeOnSelect: false,
            width: '100%'
        });

        $('#from_warehouse_id').on('change', function() {

            let warehouseId = $(this).val();
            if (!warehouseId) return;

            $.get("{{ route('ajax.warehouse.stock.data') }}", {
                warehouse_id: warehouseId
            }, function(res) {

                if (res.type === 'products') {

                    let options = '';
                    res.data.forEach(p => {
                        options += `<option value="${p.id}">${p.name}</option>`;
                    });

                    $('#product_id').html(options).trigger('change');
                    $('#batch_id').html('').trigger('change');
                }
            });
        });




        $('#product_id').on('change', function() {

            let productIds = $(this).val();
            let warehouseId = $('#from_warehouse_id').val();

            if (!productIds || !warehouseId) return;

            $.get("{{ route('ajax.warehouse.stock.data') }}", {
                product_ids: productIds,
                warehouse_id: warehouseId
            }, function(res) {

                if (res.type === 'batches') {
                    let options = '';
                    res.data.forEach(b => {
                        options += `
                <option value="${b.id}">
                    ${b.batch_no}
                </option>
            `;
                    });

                    $('#batch_id').html(options).trigger('change');
                }
            });
        });



    });
</script>

<script>
    $('#batch_id').on('change', function() {

        let selectedBatches = $(this).val(); // array
        let warehouseId = $('#from_warehouse_id').val();

        if (!selectedBatches || !warehouseId) {
            $('#quantity').val('');
            return;
        }

        let qtyList = [];
        let requests = [];

        selectedBatches.forEach(batchId => {
            requests.push(
                $.get(`/get-warehouse-stock/${warehouseId}/${batchId}`, function(res) {
                    if (res.quantity) {
                        qtyList.push(res.quantity);
                    }
                })
            );
        });

        Promise.all(requests).then(() => {
            // 🔥 COMMA SEPARATED, NOT SUM
            $('#quantity').val(qtyList.join(','));
        });
    });
</script>