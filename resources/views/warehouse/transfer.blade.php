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
                                    Warehouse-to-Warehouse Stock Transfer
                                </h4>
                                <div class="card-body">

                                    <form id="transferForm" method="POST" action="/warehouse-transfer">
                                        @csrf

                                        <!-- Row 1: FROM & TO -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="from_warehouse_id" class="form-label">From Warehouse</label>
                                                <select name="from_warehouse_id" id="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('from_warehouse_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="to_warehouse_id" class="form-label">To Warehouse</label>
                                                <select name="to_warehouse_id" id="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('to_warehouse_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: CATEGORY & PRODUCT -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label">Category</label>
                                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="product_id" class="form-label">Product</label>
                                                <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror">
                                                    <option value="">Select Product</option>
                                                </select>
                                                @error('product_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Row 3: BATCH & QTY -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="batch_id" class="form-label">Batch</label>
                                                <select name="batch_id" id="batch_id" class="form-select @error('batch_id') is-invalid @enderror">
                                                    <option value="">Select Batch</option>
                                                </select>
                                                @error('batch_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror">
                                                @if($errors->has('quantity'))
                                                    <small class="text-danger">{{ $errors->first('quantity') }}</small>
                                                @endif
                                                <small id="qtyError" class="text-danger" style="display:none;"></small>
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('transfer.index') }}" class="btn btn-outline-secondary">Back</a>
                                            <button type="submit" class="btn btn-primary">Transfer Warehouse</button>
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


<!-- get category wise product list and batch number -->
<script>
document.getElementById('category_id').addEventListener('change', function () {
    let categoryId = this.value;
    let product = document.getElementById('product_id');
    let batch = document.getElementById('batch_id');

    product.innerHTML = '<option>Loading...</option>';
    batch.innerHTML = '<option>Select Batch</option>';

    fetch(`/get-products-by-category/${categoryId}`)
        .then(res => res.json())
        .then(data => {
            product.innerHTML = '<option value="">Select Product</option>';
            data.forEach(p => {
                product.innerHTML += `<option value="${p.id}">${p.name}</option>`;
            });
        });
});

document.getElementById('product_id').addEventListener('change', function () {
    let productId = this.value;
    let batch = document.getElementById('batch_id');

    batch.innerHTML = '<option>Loading...</option>';

    fetch(`/get-batches-by-product/${productId}`)
        .then(res => res.json())
        .then(data => {
            batch.innerHTML = '<option value="">Select Batch</option>';
            data.forEach(b => {
                batch.innerHTML += `
                    <option value="${b.id}">
                        ${b.batch_no} | Exp: ${b.expiry_date ?? 'N/A'}
                    </option>`;
            });
        });
});
</script>

<!-- validation error massage qut. -->
<script>
const quantityInput = document.getElementById('quantity');
const batchSelect = document.getElementById('batch_id');
const fromWarehouseSelect = document.querySelector('select[name="from_warehouse_id"]');
const errorEl = document.getElementById('qtyError');
const form = document.getElementById('transferForm');

let maxQty = 0;

// Fetch stock whenever batch or warehouse changes
function fetchStock() {
    const batchId = batchSelect.value;
    const fromWarehouseId = fromWarehouseSelect.value;

    if (!batchId || !fromWarehouseId) {
        maxQty = 0;
        errorEl.style.display = 'none';
        return;
    }

    fetch(`/get-warehouse-stock/${fromWarehouseId}/${batchId}`)
        .then(res => res.json())
        .then(data => {
            maxQty = data.quantity || 0;
            validateQty();
        });
}

function validateQty() {
    const qty = parseInt(quantityInput.value) || 0;
    if (qty > maxQty) {
        errorEl.style.display = 'block';
        errorEl.innerText = `Cannot transfer more than available stock (${maxQty})`;
        return false;
    } else {
        errorEl.style.display = 'none';
        return true;
    }
}

// Event listeners
quantityInput.addEventListener('input', validateQty);
batchSelect.addEventListener('change', fetchStock);
fromWarehouseSelect.addEventListener('change', fetchStock);

// Prevent form submit if invalid
form.addEventListener('submit', function(e) {
    if (!validateQty()) {
        e.preventDefault();
        alert('Please fix the quantity before submitting.');
    }
});
</script>
