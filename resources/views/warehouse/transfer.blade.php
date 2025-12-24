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
                                    
                                    <form id="transferForm" method="POST"
                                        action="{{ isset($transfer) 
                                            ? route('transfer.update', $transfer->id) 
                                            : route('transfer.store') }}">

                                        @csrf
                                        @if(isset($transfer))
                                            @method('PUT')
                                        @endif


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
                                                        {{ $w->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('to_warehouse_id')
                                                        <span class="text-danger mt-1">{{ $message }}</span>
                                                    @enderror
                                            </div>
                                        </div>

                                        <!-- Row 2: CATEGORY & PRODUCT -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
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

                                            <div class="col-md-6">
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
                                            </div>
                                        </div>

                                        <!-- Row 3: BATCH & QTY -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                                                <select name="batch_id" id="batch_id" class="form-select">
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
                                                <input type="number"
                                                name="quantity"
                                                id="quantity"
                                                min="1"  placeholder="Enter quantity"
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


<script>
document.getElementById('category_id').addEventListener('change', function () {
    let categoryId = this.value;
    let product = document.getElementById('product_id');
    let batch = document.getElementById('batch_id');

    const oldProductId = document.getElementById('old_product_id')?.value;

    product.innerHTML = '<option>Loading...</option>';
    batch.innerHTML = '<option>Select Batch</option>';

    if (!categoryId) return;

    fetch(`/get-products-by-category/${categoryId}`)
        .then(res => res.json())
        .then(data => {
            product.innerHTML = '<option value="">Select Product</option>';

            data.forEach(p => {
                const selected = oldProductId == p.id ? 'selected' : '';
                product.innerHTML += `
                    <option value="${p.id}" ${selected}>${p.name}</option>
                `;
            });

            // 🔥 auto trigger batch load
            if (oldProductId) {
                product.dispatchEvent(new Event('change'));
            }
        });
});
</script>

<!-- get category wise product list and batch number -->
<script>
document.getElementById('product_id').addEventListener('change', function () {
    let productId = this.value;
    let batch = document.getElementById('batch_id');
    let oldBatchId = document.getElementById('old_batch_id')?.value;

    batch.innerHTML = '<option>Loading...</option>';

    fetch(`/get-batches-by-product/${productId}`)
        .then(res => res.json())
        .then(data => {
            batch.innerHTML = '<option value="">Select Batch</option>';

            data.forEach(b => {
                let selected = (oldBatchId && oldBatchId == b.id) ? 'selected' : '';

                batch.innerHTML += `
                    <option value="${b.id}" ${selected}>
                        ${b.batch_no}
                    </option>
                `;
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

<!-- batch expired validation -->
<script>
batchSelect.addEventListener('change', function () {
    const batchId = this.value;

    if (!batchId) return;

    fetch(`/check-batch-validity/${batchId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.valid) {
                errorEl.style.display = 'block';
                errorEl.innerText = data.message;

                quantityInput.value = '';
                quantityInput.disabled = true;
            } else {
                errorEl.style.display = 'none';
                quantityInput.disabled = false;

                // stock bhi fetch karo
                fetchStock();
            }
        });
});
</script>

<!-- get warehouse wise category -->
<script>
document.getElementById('from_warehouse_id').addEventListener('change', function () {
    const warehouseId = this.value;

    const categorySelect = document.getElementById('category_id');
    const productSelect  = document.getElementById('product_id');
    const batchSelect    = document.getElementById('batch_id');

    const oldCategoryId = document.getElementById('old_category_id')?.value;

    categorySelect.innerHTML = '<option value="">Loading categories...</option>';
    productSelect.innerHTML  = '<option value="">Select Product</option>';
    batchSelect.innerHTML    = '<option value="">Select Batch</option>';

    if (!warehouseId) return;

    fetch(`/ajax/warehouse/${warehouseId}/categories`)
        .then(res => res.json())
        .then(data => {
            categorySelect.innerHTML = '<option value="">Select Category</option>';

            data.forEach(c => {
                const selected = oldCategoryId == c.id ? 'selected' : '';
                categorySelect.innerHTML += `
                    <option value="${c.id}" ${selected}>${c.name}</option>
                `;
            });

            // 🔥 auto trigger product load in edit
            if (oldCategoryId) {
                categorySelect.dispatchEvent(new Event('change'));
            }
        });
});
</script>

<!-- same warehouse not selected in from and to warehouse dropdown -->
<script>
const fromWarehouse = document.getElementById('from_warehouse_id');
const toWarehouse   = document.getElementById('to_warehouse_id');
const oldToWarehouse = document.getElementById('old_to_warehouse_id')?.value;

fromWarehouse.addEventListener('change', function () {
    const selectedFrom = this.value;

    Array.from(toWarehouse.options).forEach(option => {
        if (!option.value) return;

        // same warehouse hide
        if (option.value === selectedFrom) {
            option.disabled = true;
            option.style.display = 'none';
        } else {
            option.disabled = false;
            option.style.display = 'block';
        }
    });

    // ✅ Edit mode → restore selection
    if (oldToWarehouse && selectedFrom !== oldToWarehouse) {
        toWarehouse.value = oldToWarehouse;
    }

    // ✅ Create mode safety
    if (!oldToWarehouse && toWarehouse.value === selectedFrom) {
        toWarehouse.value = '';
    }
});
</script>

<!-- edit mode get warehouse wise category -->
<script>
document.addEventListener('DOMContentLoaded', function () {
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
const toWarehouseEl   = document.getElementById('to_warehouse_id');
const categoryEl      = document.getElementById('category_id');
const productEl       = document.getElementById('product_id');
const batchEl         = document.getElementById('batch_id');
const qtyEl           = document.getElementById('quantity');
const tableWrapper    = document.getElementById('workOrderTableWrapper');
const tableBody       = document.querySelector('#workOrderTable tbody');
const itemsContainer  = document.getElementById('itemsContainer');

document.getElementById('addItemBtn').addEventListener('click', function () {

    const fromWarehouse = fromWarehouseEl.value;
    const toWarehouse   = toWarehouseEl.value;
    const categoryId    = categoryEl.value;
    const productId     = productEl.value;
    const batchId       = batchEl.value;
    const quantity      = qtyEl.value;

    if (!fromWarehouse || !toWarehouse || !categoryId || !productId || !batchId || !quantity) {
        alert('Please fill all fields');
        return;
    }

    if (!validateQty()) return;

    const rowIndex = editingIndex !== null ? editingIndex : index;

    if (editingIndex !== null) {
        removeRow(editingIndex);
        editingIndex = null;
    }

    const row = `
        <tr id="row_${rowIndex}">
            <td>${fromWarehouseEl.options[fromWarehouseEl.selectedIndex].text}</td>
            <td>${toWarehouseEl.options[toWarehouseEl.selectedIndex].text}</td>
            <td>${categoryEl.options[categoryEl.selectedIndex].text}</td>
            <td>${productEl.options[productEl.selectedIndex].text}</td>
            <td>${batchEl.options[batchEl.selectedIndex].text}</td>
            <td>${quantity}</td>
            <td>
                <button type="button" class="btn btn-warning btn-sm me-1" onclick="editRow(${rowIndex})">Edit</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowIndex})">Remove</button>
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
        <input type="hidden" name="items[${rowIndex}][quantity]" value="${quantity}">
    `);

    if (rowIndex === index) index++;

    resetFullForm();
});

/* ================= EDIT ================= */

async function editRow(i) {
    editingIndex = i;

    const getVal = name =>
        document.querySelector(`[name="items[${i}][${name}]"]`)?.value;

    fromWarehouseEl.value = getVal('from_warehouse_id');
    toWarehouseEl.value   = getVal('to_warehouse_id');

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

    qtyEl.value = getVal('quantity');
    fetchStock();

    removeRow(i);
}

/* ================= HELPERS ================= */

function removeRow(i) {
    document.getElementById(`row_${i}`)?.remove();
    document.querySelectorAll(`[name^="items[${i}]"]`).forEach(el => el.remove());
}

function resetFullForm() {
    productEl.innerHTML = '<option value="">Select Product</option>';
    batchEl.innerHTML   = '<option value="">Select Batch</option>';
    qtyEl.value         = '';
    categoryEl.value    = '';
    fromWarehouseEl.value = '';
    toWarehouseEl.value   = '';
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


<!-- multiple product Transfer without edit-->
<!-- <script>
let index = 0;

// cache all required elements
const fromWarehouseEl = document.getElementById('from_warehouse_id');
const toWarehouseEl   = document.getElementById('to_warehouse_id');
const categoryEl      = document.getElementById('category_id');
const productEl       = document.getElementById('product_id');
const batchEl         = document.getElementById('batch_id');
const qtyEl           = document.getElementById('quantity');
const tableWrapper    = document.getElementById('workOrderTableWrapper');
const tableBody       = document.querySelector('#workOrderTable tbody');
const itemsContainer  = document.getElementById('itemsContainer');

document.getElementById('addItemBtn').addEventListener('click', function () {

    const fromWarehouse = fromWarehouseEl.value;
    const toWarehouse   = toWarehouseEl.value;
    const categoryId    = categoryEl.value;
    const productId     = productEl.value;
    const batchId       = batchEl.value;
    const quantity      = qtyEl.value;

    if (!fromWarehouse || !toWarehouse || !categoryId || !productId || !batchId || !quantity) {
        alert('Please fill all fields');
        return;
    }

    if (!validateQty()) return;

    const row = `
        <tr id="row_${index}">
            <td>${fromWarehouseEl.options[fromWarehouseEl.selectedIndex].text}</td>
            <td>${toWarehouseEl.options[toWarehouseEl.selectedIndex].text}</td>
            <td>${categoryEl.options[categoryEl.selectedIndex].text}</td>
            <td>${productEl.options[productEl.selectedIndex].text}</td>
            <td>${batchEl.options[batchEl.selectedIndex].text}</td>
            <td>${quantity}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${index})">
                    Remove
                </button>
            </td>
        </tr>
    `;

    tableWrapper.style.display = 'block';
    tableBody.insertAdjacentHTML('beforeend', row);

    itemsContainer.insertAdjacentHTML('beforeend', `
        <input type="hidden" name="items[${index}][from_warehouse_id]" value="${fromWarehouse}">
        <input type="hidden" name="items[${index}][to_warehouse_id]" value="${toWarehouse}">
        <input type="hidden" name="items[${index}][category_id]" value="${categoryId}">
        <input type="hidden" name="items[${index}][product_id]" value="${productId}">
        <input type="hidden" name="items[${index}][batch_id]" value="${batchId}">
        <input type="hidden" name="items[${index}][quantity]" value="${quantity}">
    `);

    index++;

    // reset product fields only
    productEl.value = '';
    batchEl.value   = '';
    qtyEl.value     = '';
});

function removeRow(i) {
    document.getElementById(`row_${i}`)?.remove();
    document.querySelectorAll(`[name^="items[${i}]"]`).forEach(el => el.remove());
}
</script> -->


