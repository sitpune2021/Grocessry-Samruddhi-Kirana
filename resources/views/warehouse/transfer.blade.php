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
                                   District Warehouse Stock Request
                                </h4>
 
                                <div class="card-body">
                                    <form method="POST" action="{{ isset($transfer) ? route('transfer.update', $transfer->id) : route('transfer.store') }}">
                                        @csrf
                                        @if(isset($transfer))
                                        @method('PUT')
                                        @endif
 
                                        <input type="hidden" name="category_id" value="1">
 
                                        @if(isset($transfer))
                                            <input type="hidden" id="current_transfer_id" value="{{ $transfer->id }}">
                                        @endif

                                        @if(isset($transfer))
                                            <input type="hidden" id="current_product_id" value="{{ $transfer->product_id }}">
                                        @endif

                                        <!-- Row 1: FROM & TO -->
                                        <div class="row g-3 mb-3">

                                            <div class="col-md-6">
                                                <label class="form-label">Loged Warehouse<span class="text-danger">*</span></label>
 
                                                <select class="form-select" disabled>
                                                    <option selected>{{ $toWarehouse->name }}</option>
                                                </select>
 
                                                <input type="hidden"
                                                name="requested_by_warehouse_id"
                                                id="requested_by_warehouse_id"
                                                value="{{ $toWarehouse->id }}"
                                                data-name="{{ $toWarehouse->name }}">

                                            </div>
 
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Request Warehouse <span class="text-danger">*</span>
                                                </label>
 
                                                <select name="approved_by_warehouse_id"
                                                    id="approved_by_warehouse_id"
                                                    class="form-select"
                                                    required>
 
                                                    <option value="">Select Warehouse</option>
 
                                                    @foreach($fromWarehouses as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ isset($transfer) && $transfer->approved_by_warehouse_id == $w->id ? 'selected' : '' }}>
                                                        {{ $w->name }}
                                                    </option>
                                                    @endforeach
 
                                                </select>

                                                <input type="hidden"
                                                    name="to_warehouse_id"
                                                    id="to_warehouse_id"
                                                    value="{{ $toWarehouse->id }}">
                                            </div>
 
                                        </div>
 
                                        <!-- Row 2: PRODUCT -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="product_id" class="form-label">
                                                    Product <span class="text-danger">*</span>
                                                </label>
 
                                                <select name="product_id[]" id="product_id" class="form-control form-select" multiple>
                                                    <option value="">Select Product</option>
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
                                                    <option value="{{ $b->id }}" selected>
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
                                        @if(!isset($transfers))
                                            <!-- Row 3: QTY -->
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label for="quantity" class="form-label">
                                                        Quantity <span class="text-danger">*</span>
                                                    </label>
    
                                                    <input type="text"
                                                        name="quantity"
                                                        id="quantity"
                                                        class="form-control"
                                                        placeholder="qty"
                                                        value="{{ old('quantity', $transfer->quantity ?? '') }}">
                                                </div>
                                            </div>
                                        @endif
 
                                        @if (request()->is('warehouse-transfer/*/edit'))
                                            <div class="text-end mt-3">
                                                <button type="submit" class="btn btn-success" style="">
                                                    Update
                                                </button>
                                            </div>
                                            <a href="{{ route('transfer.index') }}" class="btn btn-success">Back</a>
                                        @else
 
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('transfer.index') }}" class="btn btn-success">Back</a>
 
                                                <button type="button" class="btn btn-success" id="addItemBtn">
                                                    Add
                                                </button>
                                            </div>
                                            <div class="text-end mt-3">
                                                <button type="submit" class="btn btn-success" style="">
                                                    Product Transfer
                                                </button>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Table -->
                                        <div class="table-responsive mt-4" id="workOrderTableWrapper" style="display: none;">
                                            
                                            <table class="table table-bordered" id="workOrderTable">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th style="width:100px;">Sr No </th>
                                                        <th>Approve Warehouse</th>
                                                        <th>Request Warehouse</th>
                                                        <th>Product</th>
                                                        <th>Batch</th>
                                                        <th style="width:10%;">Quantity</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>

                                            <div id="itemsContainer"></div>
 
                                            <!-- <div class="text-end mt-3">
                                                <button type="submit" class="btn btn-success" style="display:none;">
                                                    Product Transfer
                                                </button>
                                            </div> -->
                                            
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
        let editIndex = null;
        let index = 0;
 
        const fromWarehouseEl = $('#approved_by_warehouse_id');
        const productEl = $('#product_id');
        const batchEl = $('#batch_id');
        const qtyEl = $('#quantity');
 
        const toWarehouseHidden = $('input[name="requested_by_warehouse_id"]');
 
        const tableBody = $('#workOrderTable tbody');
        const tableWrapper = $('#workOrderTableWrapper');
        const itemsContainer = $('#itemsContainer');
 
        /* ================= LOAD PRODUCTS BY FROM WAREHOUSE ================= */
        function loadProductsByWarehouse(wid, reset = true) {
            $.get("{{ route('ajax.warehouse.stock.data') }}", {
                warehouse_id: wid
            }, function(res) {
 
                let opt = '<option></option>';
                if (res.data && res.data.length) {
                    res.data.forEach(p => {
                        opt += `<option value="${p.id}">${p.name}</option>`;
                    });
                }
 
                productEl.html(opt);
 
                if (reset) {
                    productEl.val(null).trigger('change');
                    batchEl.html('').trigger('change');
                    qtyEl.val('');
                }
            });
        }
 
        /* ================= INITIAL LOAD (MASTER DEFAULT) ================= */
        const initialFromWarehouse = fromWarehouseEl.val();

        if (initialFromWarehouse) {
            loadProductsByWarehouse(initialFromWarehouse, false);

            const selectedProduct = $('#current_product_id').val();

            if (selectedProduct) {
                setTimeout(() => {
                    productEl.val([selectedProduct]).trigger('change');  // AUTO SELECT
                }, 800);
            }
        }


        /* ================= FROM WAREHOUSE CHANGE ================= */
        fromWarehouseEl.on('change', function() {
            const wid = $(this).val();
            if (!wid) return;
            loadProductsByWarehouse(wid);
        });
 
        /* ================= PRODUCT CHANGE → FIFO BATCH + AUTO QTY ================= */
        productEl.on('change', function() 
        {
 
            const productIds = $(this).val();
 
            if (!productIds || !productIds.length) {
                batchEl.html('').trigger('change');
                qtyEl.val('');
                return;
            }
 
            $.get("{{ route('ajax.product.batches') }}", {
                product_ids: productIds,
                warehouse_id: fromWarehouseEl.val()
            }, function(res) {
 
                if (!res.data || !res.data.length) {
                    batchEl.html('').trigger('change');
                    qtyEl.val('');
                    return;
                }
 
                let batchOptions = '';
                let batchIds = [];
                let quantities = [];
 
                res.data.forEach(b => {
                    batchOptions += `<option value="${b.id}">${b.batch_no}</option>`;
                    batchIds.push(b.id);
                    quantities.push(b.quantity ?? 0);
                });
 
                batchEl.html(batchOptions)
                    .val(batchIds)
                    .trigger('change');
 
                // CSV qty for multi product
                qtyEl.val(quantities.join(','));

                @if(isset($transfer))
                const transferId = $('#current_transfer_id').val();

                if (transferId) {
                    $.get("{{ route('ajax.transfer.qty') }}", {
                        transfer_id: transferId
                    }, function(res) {
                        if (res.quantity) {
                            qtyEl.val(res.quantity);   // ✅ Yahin 200 aayega
                        }
                    });
                }
                @endif

            });
            
        });
 
 
        /* ================= ADD / UPDATE ITEM ================= */
        $('#addItemBtn').on('click', function() {
 
            const fw = fromWarehouseEl.val();
            const tw = toWarehouseHidden.val();
            const pids = productEl.val();
            const qty = qtyEl.val();
 
            if (!fw || !tw || !pids || !pids.length || !qty) {
                alert('Fill all fields');
                return;
            }
 
            /* ================= EDIT MODE ================= */
            if (editIndex !== null) {
 
                const pid = pids[0];
 
                $.get("{{ route('ajax.product.batches') }}", {
                    product_ids: [pid],
                    warehouse_id: fromWarehouseEl.val()
                }, function(res) {
 
                    const batch = res.data[0];
 
                    const row = $(`#row_${editIndex}`);
                    row.find('td:eq(1)').text(fromWarehouseEl.find(':selected').text());
                    row.find('td:eq(2)').text($('#requested_by_warehouse_id').data('name'));
                    row.find('td:eq(3)').text(productEl.find(`option[value="${pid}"]`).text());
                    row.find('td:eq(4)').text(batch.batch_no);
                    row.find('.qty-input').val(qty);
 
                    $(`[name="items[${editIndex}][approved_by_warehouse_id]"]`).val(fw);
                    $(`[name="items[${editIndex}][requested_by_warehouse_id]"]`).val(tw);
                    $(`[name="items[${editIndex}][product_id]"]`).val(pid);
                    $(`[name="items[${editIndex}][batch_id]"]`).val(batch.id);
                    $(`[name="items[${editIndex}][quantity]"]`).val(qty);
 
                    editIndex = null;
                    resetForm();
                });
 
                return;
            }
 
            /* ================= ADD MODE (MULTI PRODUCT) ================= */
            pids.forEach(pid => {
 
                const rid = index++;
 
                $.get("{{ route('ajax.product.batches') }}", {
                    product_ids: [pid],
                    warehouse_id: fromWarehouseEl.val()
                }, function(res) {
 
                    const batch = res.data[0];
 
                    tableBody.append(`
                    <tr id="row_${rid}">
                        <td>${rid + 1}</td>
                        <td>${fromWarehouseEl.find(':selected').text()}</td>
                        <td>${$('#requested_by_warehouse_id').data('name')}</td>
                        <td>${productEl.find(`option[value="${pid}"]`).text()}</td>
                        <td>${batch.batch_no}</td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm qty-input"
                                   value="${qty}"
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
                    <input type="hidden" name="items[${rid}][category_id]" value="1">
                    <input type="hidden" name="items[${rid}][approved_by_warehouse_id]" value="${fw}">
                    <input type="hidden" name="items[${rid}][requested_by_warehouse_id]" value="${tw}">
                    <input type="hidden" name="items[${rid}][product_id]" value="${pid}">
                    <input type="hidden" name="items[${rid}][batch_id]" value="${batch.id}">
                    <input type="hidden" name="items[${rid}][quantity]" value="${qty}">
                `);
 
                    tableWrapper.show();
                    toggleSubmit();
                });
            });
 
            resetForm();
        });
 
        /* ================= EDIT ROW ================= */
        $(document).on('click', '.edit-row', function() {
 
            editIndex = $(this).data('i');
 
            const get = f => $(`[name="items[${editIndex}][${f}]"]`).val();
 
            fromWarehouseEl.val(get('approved_by_warehouse_id'));
            loadProductsByWarehouse(get('approved_by_warehouse_id'), false);
 
            setTimeout(() => {
                productEl.val([get('product_id')]).trigger('change');
                setTimeout(() => {
                    batchEl.val([get('batch_id')]).trigger('change');
                    qtyEl.val(get('quantity'));
                }, 300);
            }, 300);
 
            $('#addItemBtn').text('Update');
        });
 
        /* ================= REMOVE ROW ================= */
        $(document).on('click', '.remove-row', function() {
            const i = $(this).data('i');
            $(`#row_${i}`).remove();
            $(`[name^="items[${i}]"]`).remove();
            toggleSubmit();
        });
 
        function resetForm() {
            productEl.val(null).trigger('change');
            batchEl.val(null).trigger('change');
            qtyEl.val('');
            $('#addItemBtn').text('Add');
        }
 
        function toggleSubmit() {
            $('button[type="submit"]').toggle(tableBody.children().length > 0);
        }
 
        /* ================= QTY CHANGE SYNC ================= */
        $(document).on('input change', '.qty-input', function() {
            const index = $(this).data('index');
            const newQty = $(this).val();
 
            // hidden input update
            $(`input[name="items[${index}][quantity]"]`).val(newQty);
        });
 
 
 
    });
</script>
 
 
