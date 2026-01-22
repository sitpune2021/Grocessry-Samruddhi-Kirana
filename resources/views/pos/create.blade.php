@include('layouts.header')
<!-- 
<style>
    .table-wrapper {
        overflow-x: auto;
        white-space: nowrap;
        width: 100%;
    }

    .table-wrapper table {
        min-width: 600px; /* adjust if needed */
    }

    .table-wrapper th,
    .table-wrapper td {
        white-space: nowrap;
    }
</style> -->

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- Sidebar --}}
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="container-xxl flex-grow-1 container-p-y">
                    @if ($errors->any())
                    <div class="alert alert-danger mb-2">{{ $errors->first() }}</div>
                    @endif
                    @if (session('success'))
                    <div class="alert alert-success mb-2">{{ session('success') }}</div>
                    @endif

                    <div class="card mb-4">
                        <h4 class="card-header text-center">POS Billing Desk</h4>

                        <div class="card-body">
                            <div class="">

                                {{-- LEFT : PRODUCTS --}}
                                <div class="col-12 col-md-12">

                                    {{-- BARCODE --}}
                                    <div class="mb-3 ">
                                        <input type="text"
                                            id="barcode"
                                            autofocus
                                            class="form-control form-control-lg"
                                            placeholder="Scan barcode / type product name">

                                        <div id="suggestions"
                                            class="list-group  w-100 shadow"
                                            style="z-index:999; display:none; max-height:280px; overflow-y:auto;">
                                        </div>
                                    </div>


                                    {{-- FILTERS --}}
                                    <div class="row mb-3 align-items-end">


                                        {{-- RIGHT : CART --}}
                                        <div class="col-12 col-md-12 mt-5" style="margin-top: 50px !important;">
                                            <form id="posForm" method="POST" action="{{ route('pos.store') }}">
                                                @csrf

                                                <div class="border p-4 rounded bg-white shadow sticky-top">

                                                    <h3 class="fw-bold mb-3">Billing</h3>
                                                    <div class="table-wrapper">
                                                        <table class="table table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Price</th>
                                                                    <th>Qty</th>
                                                                    <th>Value</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="bill-items"></tbody>
                                                        </table>
                                                    </div>
                                                    <hr>

                                                    <p class="fw-bold">Subtotal ₹ <span id="subtotal">0</span></p>
                                                    <input type="hidden" name="discount" id="discount_input" value="0">
                                                    <p class="fw-bold text-success">
                                                        Total Discount ₹ <span id="discount-total">0</span>
                                                    </p>
                                                    <p class="fw-bold">GST ₹ <span id="gst">0</span></p>

                                                    <h4 class="fw-bold">
                                                        Grand Total ₹ <span id="grand-total">0</span>
                                                    </h4>

                                                    {{-- Hidden --}}
                                                    <input type="hidden" name="items" id="items_input">
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">Payment Mode</label>

                                                        <div class="d-flex gap-3">
                                                            <label>
                                                                <input type="radio" name="payment_method" value="cash" checked>
                                                                Cash
                                                            </label>

                                                            <label>
                                                                <input type="radio" name="payment_method" value="upi">
                                                                UPI
                                                            </label>

                                                            <label>
                                                                <input type="radio" name="payment_method" value="card">
                                                                Card
                                                            </label>
                                                        </div>
                                                    </div>


                                                    <div class="">
                                                        <button type="button"
                                                            onclick="submitPosOrder()"
                                                            class="btn btn-success block btn-lg  mt-3">
                                                            Pay & Print
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
</body>
<script>
    let cart = {};
    let searchTimer = null;

    const barcodeInput = document.getElementById('barcode');
    const suggestions = document.getElementById('suggestions');

    /* ---------------- CART ---------------- */

    function addToCart(p) {
        if (cart[p.id]) {
            cart[p.id].qty++;
        } else {
            cart[p.id] = {
                product_id: p.id,
                name: p.name,
                unit: `${p.unit_value} ${p.unit}`,
                mrp: Number(p.mrp),
                price: Number(p.final_price),
                gst_percent: p.gst_percentage ?? 0,
                qty: 1
            };
        }

        barcodeInput.value = '';
        hideSuggestions();
        renderCart();
    }

    function changeQty(id, type) {
        if (!cart[id]) return;

        cart[id].qty += (type === 'plus' ? 1 : -1);
        if (cart[id].qty <= 0) delete cart[id];

        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('bill-items');
        tbody.innerHTML = '';

        let subtotal = 0;
        // let gstTotal = 0;
        let discountTotal = 0;

        Object.values(cart).forEach(item => {
            const line = item.qty * item.price;
            const mrpLine = item.qty * item.mrp;
            const discount = Math.max(0, mrpLine - line);

            subtotal += line;
            discountTotal += discount;

            tbody.innerHTML += `
        <tr>
            <td>
                ${item.name}
                <br>
                <small class="text-muted">${item.unit}</small>
            </td>
            <td>₹${item.price.toFixed(2)}</td>
            <td class="text-center" style="display:flex; gap:12px; align-items:center;" >
                <button onclick="changeQty(${item.product_id}, 'minus')" class="btn btn-danger p-1"><i class="bx bx-minus"></i></button>
                    ${item.qty}
                <button onclick="changeQty(${item.product_id}, 'plus')" class="btn btn-success p-1"><i class="bx bx-plus"></i></button>
            </td>
            <td>₹${line.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm"
                    onclick="delete cart[${item.product_id}]; renderCart()">✕</button>
            </td>
        </tr>`;
        });
        
        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('discount-total').innerText = discountTotal.toFixed(2);
        document.getElementById('grand-total').innerText = subtotal.toFixed(2);
        document.getElementById('gst').innerText = 'Included';

        document.getElementById('items_input').value =
            JSON.stringify(Object.values(cart).map(i => ({
                product_id: i.product_id,
                qty: i.qty
            })));

        document.getElementById('discount_input').value = discountTotal.toFixed(2);

    }

    /* ---------------- AUTO SEARCH ---------------- */

    barcodeInput.addEventListener('input', function() {
        console.log('typing:', this.value);
        isBarcodeScan = false; // typing = NOT barcode

        const q = this.value.trim();
        clearTimeout(searchTimer);

        if (q.length < 2) {
            hideSuggestions();
            return;
        }

        searchTimer = setTimeout(() => {
            fetchProducts(q);
        }, 300);
    });



    function fetchProducts(query) {

        if (isBarcodeScan) return;
        fetch(`/pos/search-products?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {

                suggestions.innerHTML = '';
                if (!Array.isArray(data) || !data.length) {
                    hideSuggestions();
                    return;
                }

                data.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action';

                    btn.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <span>${p.name}</span>
                        <strong>₹${Number(p.final_price).toFixed(2)}</strong>
                    </div>
                    <small class="text-muted">
                        ${p.unit_value} ${p.unit} | MRP ₹${p.mrp}
                    </small>
                `;

                    btn.onclick = () => addToCart(p);
                    suggestions.appendChild(btn);
                });

                suggestions.style.display = 'block';
            })
            .catch(() => hideSuggestions());
    }


    function hideSuggestions() {
        suggestions.style.display = 'none';
        suggestions.innerHTML = '';
    }

    /* ---------------- BARCODE ---------------- */



    barcodeInput.addEventListener('keydown', function(e) {

        if (e.key !== 'Enter') return;

        e.preventDefault();

        const code = barcodeInput.value.trim();
        if (!code) return;

        isBarcodeScan = true; // 🔥 mark barcode mode
        hideSuggestions(); // close auto search

        fetch(`/pos/product-by-barcode/${encodeURIComponent(code)}`)
            .then(res => res.ok ? res.json() : Promise.reject())
            .then(p => {
                addToCart(p);
                barcodeInput.value = '';
            })
            .catch(() => {
                // fallback ONLY if barcode scan fails
                if (code.length >= 2) {
                    fetchProducts(code);
                }
            });
    });


    /* ---------------- SUBMIT ---------------- */

    let submitted = false;

    function submitPosOrder() {

        // Prevent double submit
        if (submitted) return;

        if (!Object.keys(cart).length) {
            alert('Add at least one product');
            return;
        }

        submitted = true;

        const btn = event?.target;
        if (btn) {
            btn.disabled = true;
            btn.innerText = 'Processing...';
        }
        document.getElementById('posForm').submit();
    }
</script>