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
                                    <form method="POST" action="{{ route('transfer.store') }}">
                                         @csrf
                                        

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
                                                <input type="text" id="quantity" class="form-control" placeholder="qty">
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
                                                        <th>Sr No </th>
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
$(document).ready(function () {

    /* ================= SELECT2 ================= */
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

    /* ================= VARIABLES ================= */
    let index = 0;
    let editIndex = null;

    const fromWarehouseEl = $('#from_warehouse_id');
    const toWarehouseEl   = $('#to_warehouse_id');
    const productEl       = $('#product_id');
    const batchEl         = $('#batch_id');
    const qtyEl           = $('#quantity');

    const tableBody       = $('#workOrderTable tbody');
    const tableWrapper    = $('#workOrderTableWrapper');
    const itemsContainer  = $('#itemsContainer');
    const submitBtn       = $('button[type="submit"]');

    /* ================= FROM WAREHOUSE → PRODUCTS ================= */
    fromWarehouseEl.on('change', function () {
        const wid = $(this).val();
        if (!wid) return;

        $.get("{{ route('ajax.warehouse.stock.data') }}", {
            warehouse_id: wid
        }, function (res) {

            let options = '';
            res.data.forEach(p => {
                options += `<option value="${p.id}">${p.name}</option>`;
            });

            productEl.html(options).val(null).trigger('change');
            batchEl.html('').val(null).trigger('change');
            qtyEl.val('');
        });
    });

    /* ================= PRODUCTS → BATCHES ================= */
    productEl.on('change', function () {
        const productIds = $(this).val();
        if (!productIds || !productIds.length) return;

        $.get("{{ route('ajax.product.batches') }}", {
            product_ids: productIds
        }, function (res) {

            let options = '';
            res.data.forEach(b => {
                options += `<option value="${b.id}">${b.batch_no}</option>`;
            });

            batchEl.html(options).val(null).trigger('change');
            qtyEl.val('');
        });
    });

    /* ================= BATCH → QTY ================= */
    batchEl.on('change', function () {
        const batchIds = $(this).val();
        if (!batchIds || !batchIds.length) {
            qtyEl.val('');
            return;
        }

        let qtyArr = [];

        Promise.all(batchIds.map(id =>
            $.get(`/get-batch-stock/${id}`, function (res) {
                qtyArr.push(res.quantity ?? 0);
            })
        )).then(() => {
            qtyEl.val(qtyArr.join(','));
        });
    });

    /* ================= ADD / UPDATE ROW ================= */
    $('#addItemBtn').on('click', function () {

        const fw   = fromWarehouseEl.val();
        const tw   = toWarehouseEl.val();
        const pids = productEl.val();
        const bids = batchEl.val();
        const qtys = qtyEl.val().split(',');

        if (!fw || !tw || !pids || !bids || !qtys.length) {
            alert('All fields required');
            return;
        }

        if (pids.length !== bids.length || bids.length !== qtys.length) {
            alert('Product / Batch / Qty mismatch');
            return;
        }

        /* ---- EDIT MODE REMOVE OLD ROW ---- */
        if (editIndex !== null) {
            removeRow(editIndex);
            editIndex = null;
        }

        /* ---- ADD ROWS ---- */
        pids.forEach((pid, i) => {

            const rid = index++;

            tableBody.append(`
                <tr id="row_${rid}">
                    <td>${tableBody.children().length + 1}</td>
                    <td>${fromWarehouseEl.find(':selected').text()}</td>
                    <td>${toWarehouseEl.find(':selected').text()}</td>
                    <td>${$('#product_id option[value="'+pid+'"]').text()}</td>
                    <td>${$('#batch_id option[value="'+bids[i]+'"]').text()}</td>
                    <td>
                        <input type="number"
                               class="form-control form-control-sm qty-input"
                               value="${qtys[i]}"
                               min="1"
                               data-index="${rid}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm edit-row" data-i="${rid}">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm remove-row" data-i="${rid}">Remove</button>
                    </td>
                </tr>
            `);

            itemsContainer.append(`
                <input type="hidden" name="items[${rid}][from_warehouse_id]" value="${fw}">
                <input type="hidden" name="items[${rid}][to_warehouse_id]" value="${tw}">
                <input type="hidden" name="items[${rid}][category_id]" value="1">
                <input type="hidden" name="items[${rid}][product_id]" value="${pid}">
                <input type="hidden" name="items[${rid}][batch_id]" value="${bids[i]}">
                <input type="hidden" name="items[${rid}][quantity]" value="${qtys[i]}">
            `);
        });

        tableWrapper.show();
        resetForm();
        toggleSubmit();
    });

    /* ================= EDIT ROW ================= */
    $(document).on('click', '.edit-row', function () {

        const i = $(this).data('i');
        editIndex = i;

        const getVal = f => $(`[name="items[${i}][${f}]"]`).val();

        const fw  = getVal('from_warehouse_id');
        const tw  = getVal('to_warehouse_id');
        const pid = getVal('product_id');
        const bid = getVal('batch_id');
        const qty = getVal('quantity');

        fromWarehouseEl.val(fw).trigger('change');
        toWarehouseEl.val(tw);

        const waitProducts = setInterval(() => {
            if ($('#product_id option').length) {
                clearInterval(waitProducts);
                productEl.val([pid]).trigger('change');
            }
        }, 100);

        const waitBatches = setInterval(() => {
            if ($('#batch_id option').length) {
                clearInterval(waitBatches);
                batchEl.val([bid]).trigger('change');
                qtyEl.val(qty);
            }
        }, 150);

        $('#addItemBtn').text('Update');
    });

    /* ================= REMOVE ROW ================= */
    $(document).on('click', '.remove-row', function () {
        removeRow($(this).data('i'));
        toggleSubmit();
    });

    function removeRow(i) {
        $(`#row_${i}`).remove();
        $(`[name^="items[${i}]"]`).remove();
    }

    /* ================= QTY LIVE SYNC ================= */
    $(document).on('input', '.qty-input', function () {
        const i = $(this).data('index');
        $(`[name="items[${i}][quantity]"]`).val($(this).val());
    });

    /* ================= HELPERS ================= */
    function resetForm() {
        productEl.val(null).trigger('change');
        batchEl.val(null).trigger('change');
        qtyEl.val('');
        $('#addItemBtn').text('Add');
    }

    function toggleSubmit() {
        submitBtn.toggle(tableBody.children().length > 0);
    }

});
</script>
