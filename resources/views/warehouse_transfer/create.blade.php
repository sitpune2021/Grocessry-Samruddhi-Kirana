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
                                    Purches Order Request
                                </h4>

                                <div class="card-body">
<div class="container">
    <div style="display:flex; gap:10px; margin-bottom:15px;">
    <select id="po_select">
    <option value="">Select Purchase Order</option>
    @foreach($purchaseOrders as $po)
        <option value="{{ $po->id }}">
            {{ $po->po_number }}
        </option>
    @endforeach
</select>


    {{-- PRODUCT SELECT --}}
    
        <select id="product_select">
    <option value="">Select Product</option>
</select>


        <input type="number" id="product_qty" value="1" min="1" style="width:80px">

        <button type="button" onclick="addProduct()">Add</button>
    </div>

    {{-- CART TABLE --}}
    <table border="1" width="100%" cellpadding="8">
        <thead>
            <tr>
                <th>Product</th>
                <th width="120">Qty</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody id="cart_table">
            <tr>
                <td colspan="3" align="center">No products added</td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('warehouse-transfer-request.store') }}"
          onsubmit="return submitTransferRequest()">
        @csrf

        <select name="to_warehouse_id" required>
            <option value="">Select Warehouse</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
        </select>

        <input type="hidden" name="items" id="items_input">

        <br><br>
        <button type="submit">Send Request</button>
    </form>
</div>
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
<script>
function submitTransferRequest() {

    if (Object.keys(cart).length === 0) {
        alert('Please add at least one product');
        return false;
    }

    // 🔥 cart → hidden input
    document.getElementById('items_input').value =
        JSON.stringify(Object.values(cart));

    return true; // allow submit
}
</script>

<script>
document.getElementById('product_select').addEventListener('change', function () {

    let selectedOption = this.selectedOptions[0];
    let qtyInput = document.getElementById('product_qty');

    if (!selectedOption) return;

    let poQty = selectedOption.dataset.qty;

    if (poQty) {
        qtyInput.value = poQty;     // ✅ auto fill
        qtyInput.max   = poQty;     // ✅ limit set
    } else {
        qtyInput.value = 1;
        qtyInput.removeAttribute('max');
    }
});
</script>

<script>
function renderCart() {

    let tbody = document.getElementById('cart_table');
    tbody.innerHTML = '';

    if (Object.keys(cart).length === 0) {
        tbody.innerHTML =
            `<tr><td colspan="3" align="center">No products added</td></tr>`;
        return;
    }

    Object.values(cart).forEach(item => {
        tbody.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>
                    <button type="button" onclick="removeItem(${item.product_id})">X</button>
                </td>
            </tr>
        `;
    });
}

function removeItem(productId) {
    delete cart[productId];
    renderCart();
}
</script>


<script>
let cart = {};

document.getElementById('po_select').addEventListener('change', function () {

    let poId = this.value;
    let productSelect = document.getElementById('product_select');

    productSelect.innerHTML = `<option value="">Select Product</option>`;
    cart = {};
    renderCart();

    if (!poId) return;

    fetch(`/warehouse-transfer-request/purchase-orders/${poId}/items`)
        .then(res => res.json())
        .then(data => {

            if (data.length === 0) {
                alert('No items found in this PO');
                return;
            }

            data.forEach(item => {
                let opt = document.createElement('option');
                opt.value = item.product_id;
                opt.text  = item.product.name;
                opt.dataset.qty = item.quantity; // PO qty

                productSelect.appendChild(opt);
            });
        })
        .catch(err => {
            console.error('Error loading PO items', err);
        });
});
</script>


<script>
function addProduct() {

    let select = document.getElementById('product_select');
    let productId = select.value;
    let productName = select.selectedOptions[0]?.text;
    let qty = parseInt(document.getElementById('product_qty').value);

    if (!productId) {
        alert('Select product first');
        return;
    }

    if (qty < 1) qty = 1;

    cart[productId] = {
        product_id: productId,
        name: productName,
        qty: qty
    };

    renderCart();
}
</script>


