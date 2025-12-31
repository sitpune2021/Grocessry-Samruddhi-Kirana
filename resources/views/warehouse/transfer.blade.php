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
                                                    <option value="{{ $w->id }}" {{ (isset($transfer) && $transfer->from_warehouse_id == $w->id) ? 'selected' : '' }}>
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
                                                    <option value="{{ $w->id }}" {{ (isset($transfer) && $transfer->to_warehouse_id == $w->id) ? 'selected' : '' }}>
                                                        {{ $w->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('to_warehouse_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: PRODUCT -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                                <select name="product_id[]" id="product_id" class="form-control form-select" multiple>
                                                    <option value="">Select All</option>
                                                    @if(isset($products))
                                                    @foreach($products as $p)
                                                    <option value="{{ $p->id }}" {{ (isset($transfer) && $transfer->product_id == $p->id) ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('product_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                                                <select name="batch_id[]" id="batch_id" class="form-control form-select" multiple>
                                                    <option value="">Select Batch</option>
                                                    @if(isset($batches))
                                                    @foreach($batches as $b)
                                                    <option value="{{ $b->id }}" {{ (isset($transfer) && $transfer->batch_id == $b->id) ? 'selected' : '' }}>
                                                        {{ $b->batch_no }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                @error('batch_id')
                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Row 3: QTY -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                                <input type="text" id="quantity" class="form-control" placeholder="Comma separated qty">
                                                <small id="qtyError" class="text-danger" style="display:none;"></small>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="d-flex justify-content-between mb-3">
                                            <a href="{{ route('transfer.index') }}" class="btn btn-success">Back</a>
                                            <button type="button" class="btn btn-success" id="addItemBtn">
                                                Add
                                            </button>
                                        </div>

                                        <!-- Table -->
                                        <div class="table-responsive mt-4" id="workOrderTableWrapper" style="display: none;">
                                            <table class="table table-bordered" id="workOrderTable">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>From Warehouse</th>
                                                        <th>To Warehouse</th>
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
                                                <button type="submit" class="btn btn-success" style="display:none;">
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
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
</body>

<!-- jQuery + Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    $('#product_id').select2({ placeholder: 'Select Product', closeOnSelect: false, width: '100%' });
    $('#batch_id').select2({ placeholder: 'Select Batch', closeOnSelect: false, width: '100%' });

    const fromWarehouseEl = $('#from_warehouse_id');
    const toWarehouseEl = $('#to_warehouse_id');
    const productEl = $('#product_id');
    const batchEl = $('#batch_id');
    const qtyEl = $('#quantity');
    const tableWrapper = $('#workOrderTableWrapper');
    const tableBody = $('#workOrderTable tbody');
    const itemsContainer = $('#itemsContainer');
    let index = 0;

    /* ================= GET PRODUCTS ON WAREHOUSE CHANGE ================= */
    fromWarehouseEl.on('change', function() {
        const warehouseId = $(this).val();
        if (!warehouseId) return;
        $.get("{{ route('ajax.warehouse.stock.data') }}", { warehouse_id: warehouseId }, function(res) {
            if (res.type === 'products') {
                let options = '';
                res.data.forEach(p => { options += `<option value="${p.id}">${p.name}</option>`; });
                $('#product_id').html(options).trigger('change');
                $('#batch_id').html('').trigger('change');
            }
        });
    });

    /* ================= GET BATCHES ON PRODUCT CHANGE ================= */
    productEl.on('change', function() {
        const productIds = $(this).val();
        if (!productIds || productIds.length === 0) return;
        $.get("{{ route('ajax.product.batches') }}", { product_ids: productIds }, function(res) {
            if (res.type === 'batches') {
                let options = '';
                res.data.forEach(b => { options += `<option value="${b.id}">${b.batch_no}</option>`; });
                $('#batch_id').html(options).trigger('change');
            }
        });
    });

    /* ================= GET QTY ON BATCH CHANGE ================= */
    batchEl.on('change', function() {
        const batchIds = $(this).val();
        if (!batchIds || batchIds.length === 0) { qtyEl.val(''); return; }

        let qtyList = [];
        let requests = batchIds.map(batchId => {
            return $.get(`/get-batch-stock/${batchId}`, function(res) {
                qtyList.push(res.quantity ?? 0);
            });
        });

        Promise.all(requests).then(() => {
            qtyEl.val(qtyList.join(','));
        });
    });

    /* ================= ADD ITEM ================= */
    $('#addItemBtn').on('click', function() {

        const fromWarehouse = fromWarehouseEl.val();
        const toWarehouse = toWarehouseEl.val();
        const productIds = productEl.val();
        const batchIds = batchEl.val();
        const quantity = qtyEl.val();

        if (!fromWarehouse || !toWarehouse || !productIds || !batchIds || !quantity) {
            alert('Please fill all fields');
            return;
        }

        const qtyList = quantity.split(',').map(q => q.trim());
        if (productIds.length !== batchIds.length || batchIds.length !== qtyList.length) {
            alert('Product, Batch and Quantity count mismatch');
            return;
        }

        productIds.forEach((productId, i) => {
            const batchId = batchIds[i];
            const qty = qtyList[i];
            const rowIndex = index++;

            const row = `<tr id="row_${rowIndex}">
                <td>${fromWarehouseEl.find('option:selected').text()}</td>
                <td>${toWarehouseEl.find('option:selected').text()}</td>
                <td>${$('#product_id option[value="' + productId + '"]').text()}</td>
                <td>${$('#batch_id option[value="' + batchId + '"]').text()}</td>
                <td>${qty}</td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm edit-row" data-index="${rowIndex}">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${rowIndex}">Remove</button>
                </td>
            </tr>`;
            tableWrapper.show();
            tableBody.append(row);

            itemsContainer.append(`
                <input type="hidden" name="items[${rowIndex}][from_warehouse_id]" value="${fromWarehouse}">
                <input type="hidden" name="items[${rowIndex}][to_warehouse_id]" value="${toWarehouse}">
                <input type="hidden" name="items[${rowIndex}][category_id]" value="1">
                <input type="hidden" name="items[${rowIndex}][product_id]" value="${productId}">
                <input type="hidden" name="items[${rowIndex}][batch_id]" value="${batchId}">
                <input type="hidden" name="items[${rowIndex}][quantity]" value="${qty}">
            `);
        });

        resetFullForm();
        toggleButtons();
    });

    /* ================= EDIT / REMOVE ROW ================= */
    $(document).on('click', '.edit-row', function() {
        const i = $(this).data('index');
        const getVal = name => $(`[name="items[${i}][${name}]"]`).val();

        fromWarehouseEl.val(getVal('from_warehouse_id')).trigger('change');
        toWarehouseEl.val(getVal('to_warehouse_id')).trigger('change');

        const waitOptions = (el, val) => new Promise(res => {
            const interval = setInterval(() => {
                if ([...el[0].options].some(o => o.value == val)) { clearInterval(interval); res(); }
            }, 100);
        });

        waitOptions(productEl, getVal('product_id')).then(() => {
            productEl.val([getVal('product_id')]).trigger('change');
            waitOptions(batchEl, getVal('batch_id')).then(() => {
                batchEl.val([getVal('batch_id')]).trigger('change');
                qtyEl.val(getVal('quantity'));
            });
        });

        removeRow(i);
    });

    $(document).on('click', '.remove-row', function() {
        removeRow($(this).data('index'));
    });

    function removeRow(i) {
        $(`#row_${i}`).remove();
        $(`[name^="items[${i}]"]`).remove();
        toggleButtons();
    }

    function resetFullForm() {
        productEl.val(null).trigger('change');
        batchEl.val(null).trigger('change');
        qtyEl.val('');
    }

    function toggleButtons() {
        const hasRows = tableBody.children().length > 0;
        $('#addItemBtn').toggle(!hasRows);
        $('button[type="submit"]').toggle(hasRows);
    }

    toggleButtons(); // initial
});
</script>
