@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>



            {{-- Page --}}
            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">


                    @if(session('error'))
                    <div class="alert alert-danger col-6 ms-6 mt-6">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger col-6 ms-6 mt-6">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success col-6 ms-6 mt-6">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="row justify-content-center">
                            <div class="col-md-12">

                                <div class="card">
                                    <h4 class="card-header">
                                        🛒 Near Expiry Online Sale
                                    </h4>

                                    <div class="card-body">
                                        <form method="POST" action="{{ route('sale.store') }}">
                                            @csrf

                                            {{-- hidden batch --}}
                                            <input type="hidden" name="product_batch_id" value="{{ $batch->id }}">

                                            {{-- Product Info --}}
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Product</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $batch->product->name }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Batch No</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $batch->batch_no }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Available Qty</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $batch->quantity }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            {{-- Dates --}}
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Expiry Date</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Days Left</label>
                                                    <input type="text" class="form-control text-danger fw-bold"
                                                        value="{{ (int)$daysLeft }} days"
                                                        readonly>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Original Price (₹)</label>
                                                    <input type="text" class="form-control"
                                                        id="original_price"
                                                        value="{{ $batch->product->final_price }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            {{-- Discount --}}

                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    Discount % <span class="text-danger">*</span>
                                                </label>
                                                <input type="number"
                                                    name="discount_percent"
                                                    id="discount_percent"
                                                    class="form-control"
                                                    min="5" max="80"
                                                    required>
                                                @error('discount_percent')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <hr>

                                            {{-- Prices --}}
                                            <div class="row mb-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">MRP (₹)</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="mrp"
                                                        value="{{ $batch->product->mrp }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Selling Price (₹)</label>
                                                    <input type="text"
                                                        class="form-control"
                                                        id="original_price"
                                                        value="{{ $batch->product->final_price }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label text-success">Discount on MRP (₹)</label>
                                                    <input type="text"
                                                        class="form-control text-success fw-bold"
                                                        id="discount_on_mrp"
                                                        readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label text-danger fw-bold">Final Sale Price (₹)</label>
                                                    <input type="text"
                                                        name="sale_price"
                                                        id="sale_price"
                                                        class="form-control text-danger fw-bold"
                                                        readonly>
                                                </div>
                                            </div>


                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    Sale End Date <span class="text-danger">*</span>
                                                </label>
                                                {{-- Visible (for user) --}}
                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ \Carbon\Carbon::parse($batch->expiry_date)->subDay()->format('d/m/Y') }}"
                                                    readonly>

                                                {{-- Hidden (for backend) --}}
                                                <input type="hidden"
                                                    name="sale_end_date"
                                                    value="{{ \Carbon\Carbon::parse($batch->expiry_date)->subDay()->format('Y-m-d') }}">
                                                @error('sale_end_date')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>


                                            {{-- Note --}}
                                            <!-- <div class="alert alert-warning small">
                                                ⚠ This product will be visible <b>ONLINE ONLY</b> (Website / App).
                                                It will NOT appear in POS or offline sales.
                                            </div> -->

                                            {{-- Actions --}}
                                            <div class="text-end mt-6">
                                                <a href="{{ route('batches.expiry') }}" class="btn btn-outline-success">
                                                    Back
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    Put on Sale
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
</body>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#discount_percent').on('input', function() {

        let discountPercent = parseFloat($(this).val());
        let mrp = parseFloat($('#mrp').val());
        let sellingPrice = parseFloat($('#original_price').val());
        let basePrice = {{ (float) $batch->product->base_price }};

        if (!discountPercent || discountPercent <= 0) {
            $('#sale_price').val('');
            $('#discount_on_mrp').val('');
            return;
        }

        // ✅ Discount calculated on MRP
        let discountAmount = mrp * discountPercent / 100;

        // ✅ Sale price derived from MRP
        let salePrice = mrp - discountAmount;

        // ❌ Prevent below base price
        if (salePrice < basePrice) {
            alert(
                'Discount too high!\n' +
                'Sale price cannot be less than base price (₹' + basePrice + ')'
            );
            $(this).val('');
            $('#sale_price').val('');
            $('#discount_on_mrp').val('');
            return;
        }

        $('#sale_price').val(salePrice.toFixed(2));
        $('#discount_on_mrp').val(discountAmount.toFixed(2));
    });
</script>