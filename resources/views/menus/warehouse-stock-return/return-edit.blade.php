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
                                        <h4>Warehouse Stock Return</h4>
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form
                                            action="{{ route('stock-returns.update', $stockReturn->id) }}"
                                            method="POST"
                                            enctype="multipart/form-data">

                                            @csrf
                                            @if($mode === 'edit')
                                            @method('PUT')
                                            @endif

                                            <div class="card-body">

                                                {{-- ================= WAREHOUSES ================= --}}
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
                                                            <option value="{{ $warehouse->id }}"
                                                                {{ ($mode === 'edit' && $stockReturn->to_warehouse_id == $warehouse->id) ? 'selected' : '' }}>
                                                                {{ $warehouse->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- ================= REASON ================= --}}
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label>Return Reason <span class="text-danger">*</span></label>
                                                        <select name="return_reason" class="form-control" required>
                                                            <option value="">Select Reason</option>
                                                            @foreach([
                                                            'damaged' => 'Damaged Stock',
                                                            'excess_stock' => 'Excess Stock',
                                                            'wrong_item' => 'Wrong Item',
                                                            'near_expiry' => 'Near Expiry',
                                                            'quality_issue' => 'Quality Issue'
                                                            ] as $key => $label)
                                                            <option value="{{ $key }}"
                                                                {{ ($mode === 'edit' && $stockReturn->return_reason == $key) ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks"
                                                            class="form-control"
                                                            rows="2"
                                                            placeholder="Remarks">{{ old('remarks', $mode === 'edit' ? $stockReturn->remarks : '') }}</textarea>
                                                    </div>

                                                </div>

                                                {{-- ================= PRODUCTS ================= --}}
                                                <hr>
                                                <h5>Return Products</h5>

                                                <table class="table table-bordered" id="productTable">
                                                    <thead>
                                                        <tr>
                                                            <th width="22%">Product</th>
                                                            <th width="18%">Condition Image</th>
                                                            <th width="18%">Batch No</th>
                                                            <th width="12%">Available</th>
                                                            <th width="12%">Return Qty</th>
                                                            <th width="8%">Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        @php
                                                        $items = $mode === 'edit' ? $stockReturn->WarehouseStockReturnItem : [null];


                                                        @endphp

                                                        @foreach($items as $index => $item)
                                                        <tr>
                                                            {{-- PRODUCT --}}
                                                            <td>
                                                                <select name="items[{{ $index }}][product_id]"
                                                                    class="form-control product-select" required>

                                                                    <option value="">Select Product</option>

                                                                    @foreach($warehouseStocks->groupBy('product_id') as $productId => $batches)
                                                                    <option value="{{ $productId }}"
                                                                        {{ ($mode === 'edit' && $item && $item->product_id == $productId) ? 'selected' : '' }}
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
                                                                </select>
                                                            </td>

                                                            {{-- IMAGE --}}
                                                            <td class="text-center">
                                                                <input type="file"
                                                                    name="items[{{ $index }}][product_image]"
                                                                    class="form-control"
                                                                    accept="image/*">

                                                                @if($mode === 'edit' && $item && $item->product_image)
                                                                <img src="{{ asset('storage/'.$item->product_image) }}"
                                                                    class="img-thumbnail mt-1"
                                                                    width="70">
                                                                @endif
                                                            </td>

                                                            {{-- BATCH --}}
                                                            <td>
                                                                <select name="items[{{ $index }}][batch_id]"
                                                                    class="form-control batch-select"
                                                                    required>

                                                                    <option value="">Select Batch</option>

                                                                    @if($mode === 'edit' && $item && $item->batch)
                                                                    <option value="{{ $item->batch_id }}" selected>
                                                                        {{ $item->batch->batch_no }}
                                                                    </option>
                                                                    @endif

                                                                </select>


                                                            </td>

                                                            {{-- AVAILABLE --}}
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control available-stock"
                                                                    value="{{ ($mode === 'edit' && $item && $item->batch) ? $item->batch->quantity : '' }}"
                                                                    readonly>
                                                            </td>


                                                            {{-- RETURN QTY --}}
                                                            <td>
                                                                <input type="number"
                                                                    name="items[{{ $index }}][return_qty]"
                                                                    class="form-control return-qty"
                                                                    min="1"
                                                                    value="{{ $mode === 'edit' && $item ? $item->return_qty : '' }}"
                                                                    required>
                                                            </td>

                                                            {{-- ACTION --}}
                                                            <!-- <td class="text-center">
                                                                <button type="button" class="btn btn-danger removeRow">X</button>
                                                            </td> -->
                                                        </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>

                                                <!-- <button type="button" id="addRow" class="btn btn-secondary">
                                                    + Add Product
                                                </button> -->

                                            </div>

                                            {{-- ================= FOOTER ================= --}}
                                            <div class="card-footer text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ $mode === 'edit' ? 'Update Return' : '' }}

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
    $(document).ready(function() {

        $('.product-select').each(function() {

            if (!$(this).val()) return;

            const row = $(this).closest('tr');
            const batchSelect = row.find('.batch-select');
            const stockInput = row.find('.available-stock');

            // ✅ GET SELECTED BATCH ID FIRST
            const selectedBatchId = batchSelect.val();
            console.log(batchSelect)
            const batches = $(this).find(':selected').data('batches') || [];

            batchSelect.empty().append('<option value="">Select Batch</option>');

            $.each(batches, function(i, batch) {

                const option = $('<option>', {
                    value: batch.batch_id,
                    text: batch.batch_no,
                    'data-stock': batch.stock
                });

                // ✅ RESTORE SELECTED OPTION
                if (selectedBatchId == batch.batch_id) {
                    option.prop('selected', true);
                    stockInput.val(batch.stock);
                }

                batchSelect.append(option);
            });
        });

    });
</script>

<!-- <script>
    $(document).on('change', '.product-select', function() {

        const row = $(this).closest('tr');
        const batchSelect = row.find('.batch-select');
        const stockInput = row.find('.available-stock');

        const batches = $(this).find(':selected').data('batches') || [];

        batchSelect.empty().append('<option value="">Select Batch</option>');
        stockInput.val('');

        $.each(batches, function(i, batch) {
            batchSelect.append(
                $('<option>', {
                    value: batch.batch_id,
                    text: batch.batch_no,
                    'data-stock': batch.stock
                })
            );
        });
    });
</script> -->
<script>
    $(document).on('change', '.batch-select', function() {

        const row = $(this).closest('tr');
        const stockInput = row.find('.available-stock');

        const stock = $(this).find(':selected').data('stock') || '';
        stockInput.val(stock);
    });
</script>