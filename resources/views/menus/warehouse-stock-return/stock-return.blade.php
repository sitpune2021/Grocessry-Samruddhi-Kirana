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

                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

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
                                        <h4>Warehouse Stock Return</h4>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form action="{{ route('stock-returns.store') }}" method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <div class="card-body">

                                                {{-- Warehouses --}}

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>From Warehouse</label>
                                                        <input type="text"
                                                            class="form-control"
                                                            value="{{ $user->warehouse->name ?? '' }}"
                                                            readonly>
                                                        <input type="hidden" name="from_warehouse_id"
                                                            value="{{ $user->warehouse_id }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label>To Warehouse <span class="text-danger">*</span></label>
                                                        <select name="to_warehouse_id" class="form-control" required>
                                                            <option value="">Select Warehouse</option>
                                                            @foreach($warehouses as $warehouse)
                                                            <option value="{{ $warehouse->id }}">
                                                                {{ $warehouse->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Transfer Challan --}}
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label>Transfer Challan <span class="text-danger">*</span></label>
                                                        <select name="transfer_challan_id"
                                                            id="challanSelect"
                                                            class="form-control"
                                                            required>
                                                            <option value="">Select Challan</option>
                                                            @foreach($challans as $ch)
                                                            <option value="{{ $ch->id }}"
                                                                data-from="{{ $ch->from_warehouse_id }}">
                                                                {{ $ch->challan_no }}
                                                                ({{ $ch->fromWarehouse->name }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>




                                                {{-- Reason --}}
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label>Return Reason <span class="text-danger">*</span></label>
                                                        <select name="return_reason" class="form-control" required>
                                                            <option value="">Select Reason</option>
                                                            <option value="damaged">Damaged Stock</option>
                                                            <option value="excess_stock">Excess Stock</option>
                                                            <option value="wrong_item">Wrong Item</option>
                                                            <option value="near_expiry">Near Expiry</option>
                                                            <option value="quality_issue">Quality Issue</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control" placeholder="Remark" rows="2"></textarea>
                                                    </div>
                                                </div>

                                                {{-- Products --}}
                                                <hr>
                                                <h5>Return Products</h5>

                                                <table class="table table-bordered" id="productTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Condition Image</th>
                                                            <th>Batch No</th>
                                                            <th>Received Stock</th>
                                                            <th>Return Qty</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                {{-- <select name="items[0][product_id]"
                                                                    class="form-control product-select"
                                                                    required>

                                                                    <option value="">Select Product</option>

                                                                    @foreach($warehouseStocks->groupBy('product_id') as $productId => $batches)

                                                                    <option value="{{ $productId }}"
                                                                data-batches='@json(
                                                                $batches->map(fn($batch) => [
                                                                "batch_id" => $batch->id,
                                                                "batch_no" => $batch->batch_no,
                                                                "stock" => $batch->quantity
                                                                ])
                                                                )'>
                                                                {{ $batches->first()->product->name }}
                                                                </option>
                                                                @endforeach
                                                                </select> --}}

                                                                <select name="items[0][product_id]"
                                                                    class="form-control product-select"
                                                                    required>
                                                                    <option value="">Select Product</option>
                                                                </select>

                                                            </td>
                                                            {{-- Product Image --}}
                                                            <td class="text-center">
                                                                <input type="file"
                                                                    name="items[0][product_image]"
                                                                    class="form-control product-image"
                                                                    accept="image/*">

                                                                <img src="{{ asset('images/') }}"
                                                                    class="img-thumbnail mt-1 image-preview"
                                                                    width="70">
                                                            </td>

                                                            <td>
                                                                <select name="items[0][batch_no]" class="form-control batch-select" required>
                                                                    <option value="">Select Batch</option>
                                                                </select>
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    class="form-control available-stock"
                                                                    readonly>
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="items[0][return_qty]"
                                                                    class="form-control return-qty"
                                                                    min="1"
                                                                    required>
                                                            </td>

                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger removeRow">
                                                                    X
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <button type="button" id="addRow" class="btn btn-secondary">
                                                    Add Product
                                                </button>

                                            </div>

                                            {{-- Footer --}}
                                            <div class="card-footer text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    Save as Draft
                                                </button>
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
    let rowIndex = 1;

    /* ================= ADD ROW ================= */
    document.getElementById('addRow').addEventListener('click', function() {
        let table = document.querySelector('#productTable tbody');
        let row = table.rows[0].cloneNode(true);

        row.querySelectorAll('input, select').forEach(el => {
            if (el.name) el.name = el.name.replace(/\d+/, rowIndex);
            if (el.type !== 'file') el.value = '';
        });

        row.querySelector('.batch-select').innerHTML =
            '<option value="">Select Batch</option>';

        row.querySelector('.available-stock').value = '';

        row.querySelector('.image-preview').src =
            "{{ asset('images/no-image.png') }}";

        table.appendChild(row);
        rowIndex++;
    });

    /* ================= ALL CHANGE EVENTS ================= */
    document.addEventListener('change', function(e) {

        /* PRODUCT CHANGE */
        if (e.target.classList.contains('product-select')) {

            let row = e.target.closest('tr');
            let batchSelect = row.querySelector('.batch-select');
            let stockInput = row.querySelector('.available-stock');

            batchSelect.innerHTML = '<option value="">Select Batch</option>';
            stockInput.value = '';

            const selected = e.target.selectedOptions[0];
            if (!selected) return;

            // Add batch from challan
            const option = document.createElement('option');
            option.value = selected.dataset.batchId;
            option.textContent = selected.dataset.batchNo;
            option.dataset.stock = selected.dataset.max; // challan qty
            batchSelect.appendChild(option);
        }

        /* BATCH CHANGE */
        if (e.target.classList.contains('batch-select')) {
            let row = e.target.closest('tr');
            let stockInput = row.querySelector('.available-stock');
            let stock = e.target.selectedOptions[0]?.dataset.stock ?? 0;
            stockInput.value = stock;
        }

        /* IMAGE PREVIEW */
        if (e.target.classList.contains('product-image')) {
            let row = e.target.closest('tr');
            let preview = row.querySelector('.image-preview');

            if (e.target.files && e.target.files[0]) {
                let reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    });

    /* ================= STOCK VALIDATION ================= */
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('return-qty')) {
            let row = e.target.closest('tr');
            let available =
                parseInt(row.querySelector('.available-stock').value || 0);

            if (parseInt(e.target.value) > available) {
                alert('Return quantity cannot exceed available stock');
                e.target.value = '';
            }
        }
    });

    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const challanSelect = document.getElementById('challanSelect');
        if (!challanSelect) {
            console.error('Challan select not found');
            return;
        }

        challanSelect.addEventListener('change', function() {
            const challanId = this.value;
            if (!challanId) return;

            fetch(`/stock-return/challan-products/${challanId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Failed to fetch challan products');
                    }
                    return res.json();
                })
                .then(items => {

                    document.querySelectorAll('.product-select').forEach(select => {
                        select.innerHTML = '<option value="">Select Product</option>';

                        items.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.product_id;
                            opt.textContent = item.product_name;
                            opt.dataset.batchId = item.batch_id;
                            opt.dataset.batchNo = item.batch_no;
                            opt.dataset.max = item.challan_qty;
                            select.appendChild(opt);
                        });

                    });
                })
                .catch(err => {
                    console.error(err);
                    alert('Unable to load challan products');
                });
        });

    });
</script>