@include('layouts.header')

{{-- Payment Failed Overlay --}}
<div id="payment-failed-box"
    class="position-fixed top-50 start-50 translate-middle bg-white shadow p-4 rounded text-center"
    style="z-index:9999; width:420px; display:none;">

    <h2 class="text-danger mb-3">Payment Failed</h2>

    <p class="mb-2">
        Order #: <strong id="failed-order-number"></strong>
    </p>

    <p class="text-muted mb-4">
        Payment could not be completed. Please retry.
    </p>

    <div class="d-flex justify-content-center gap-3">
        <button class="btn btn-warning" onclick="retryPayment()">
            Retry Payment
        </button>

        <button class="btn btn-secondary" onclick="closePaymentFailedBox()">
            Continue Billing
        </button>
    </div>
</div>


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
                                <form id="posForm" method="POST" action="{{ route('pos.store') }}">
                                    @csrf
                                    {{-- LEFT : PRODUCTS --}}
                                    <div class="col-12 col-md-12">
                                        <div class="row g-3 mb-3">

                                            {{-- CUSTOMER SEARCH --}}
                                            <div class="col-12 col-md-4 position-relative">
                                                <label class="form-label fw-bold">Customer</label>

                                                <input type="text"
                                                    id="customerSearch"
                                                    class="form-control form-control-lg"
                                                    placeholder="Search customer by name or mobile">

                                                <input type="hidden" name="customer_id" id="customer_id">

                                                <div id="customerSuggestions"
                                                    class="list-group position-absolute w-100 shadow"
                                                    style="z-index: 1050; display: none; max-height: 280px; overflow-y: auto;">
                                                </div>
                                            </div>

                                            {{-- PRODUCT / BARCODE SEARCH --}}
                                            <div class="col-12 col-md-8 position-relative">
                                                <label class="form-label fw-bold">Product</label>

                                                <input type="text"
                                                    id="barcode"
                                                    autofocus
                                                    class="form-control form-control-lg"
                                                    placeholder="Scan barcode / type product name">

                                                <div id="suggestions"
                                                    class="list-group position-absolute w-100 shadow"
                                                    style="z-index: 1050; display: none; max-height: 280px; overflow-y: auto;">
                                                </div>
                                            </div>

                                        </div>
                                        {{-- FILTERS --}}
                                        <div class="row mb-3 align-items-end">
                                            {{-- RIGHT : CART --}}
                                            <div class="col-12 col-md-12 mt-5" style="margin-top: 50px !important;">
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
                                                    <div class="row">
                                                        <div class="col-md-8"></div>
                                                        <div class="col-md-4 row mb-2 align-items-end ">
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
                                                            <div class="mt-2">
                                                                <label class="form-label fw-bold">Payment Mode</label>

                                                                <div class="gap-3 payment-options">
                                                                    <label class="payment-radio">
                                                                        <input type="radio" name="payment_method" value="cash" checked>
                                                                        <span>Cash</span>
                                                                    </label>

                                                                    <label class="payment-radio">
                                                                        <input type="radio" name="payment_method" value="online">
                                                                        <span>Online</span>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            {{-- Action Button --}}
                                                            <div class="">
                                                                <button type="button"
                                                                    onclick="submitPosOrder(event)"
                                                                    class="btn btn-success block btn-lg  mt-3">
                                                                    Pay & Print
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
</body>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    let lastOrderId = null;
    let lastOrderNumber = null;

    function showPaymentFailed(orderId, orderNumber) {
        lastOrderId = orderId;
        lastOrderNumber = orderNumber;

        document.getElementById('failed-order-number').innerText = orderNumber;
        document.getElementById('payment-failed-box').style.display = 'block';

        enablePayButton();
    }

    function closePaymentFailedBox() {
        document.getElementById('payment-failed-box').style.display = 'none';
    }

    function retryPayment() {
        closePaymentFailedBox();
        openRazorpay(lastOrderId);
    }


    let cart = {};
    let searchTimer = null;
    let isBarcodeScan = false;

    function enablePayButton() {
        const btn = document.querySelector('button[onclick="submitPosOrder(event)"]');
        btn.disabled = false;
        btn.innerText = 'Pay & Print';
        paymentInProgress = false;
    }

    function showPaymentError(msg) {
        alert(msg || 'Payment failed. Please try again.');
    }


    function looksLikeBarcode(value) {
        return /^[0-9]{6,14}$/.test(value); // numeric 6–14 digits
    }

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
        // console.log('typing:', this.value);
        isBarcodeScan = false; // typing = NOT barcode

        const q = this.value.trim();
        clearTimeout(searchTimer);

        if (looksLikeBarcode(q)) {
            hideSuggestions();
            return;
        }

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

        isBarcodeScan = true;
        hideSuggestions();

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

    /* ---------- Customer Search ------- */

    let customerTimer = null;

    const customerInput = document.getElementById('customerSearch');
    const customerSuggestions = document.getElementById('customerSuggestions');

    customerInput.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(customerTimer);

        if (q.length < 2) {
            hideCustomerSuggestions();
            return;
        }

        customerTimer = setTimeout(() => {
            fetchCustomers(q);
        }, 300);
    });

    function fetchCustomers(query) {
        fetch(`/pos/search-customers?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                customerSuggestions.innerHTML = '';

                if (!Array.isArray(data) || !data.length) {
                    hideCustomerSuggestions();
                    return;
                }

                data.forEach(c => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action';

                    btn.innerHTML = `
                    <strong>${c.name}</strong><br>
                    <small class="text-muted">${c.mobile}</small>
                `;

                    btn.onclick = () => selectCustomer(c);
                    customerSuggestions.appendChild(btn);
                });

                customerSuggestions.style.display = 'block';
            });
    }

    function selectCustomer(customer) {
        customerInput.value = `${customer.name} (${customer.mobile})`;
        document.getElementById('customer_id').value = customer.id;


        hideCustomerSuggestions();
    }

    function hideCustomerSuggestions() {
        customerSuggestions.style.display = 'none';
        customerSuggestions.innerHTML = '';
    }

    let razorpayOrderId = null;

    function openRazorpay(orderId) {

        fetch('/razorpay/create-order', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to create Razorpay order');
                return res.json();
            })
            .then(rzp => {

                razorpayOrderId = rzp.razorpay_order_id;

                const options = {
                    key: rzp.key,
                    amount: rzp.amount,
                    currency: "INR",
                    name: "Samruddh Kirana",
                    description: "POS Payment",
                    order_id: razorpayOrderId,

                    handler: function(response) {
                        // ✅ PAYMENT SUCCESS
                        fetch('/razorpay/verify', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature,
                                    order_id: orderId
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = data.redirect;
                                } else {
                                    showPaymentFailed(orderId, lastOrderNumber);
                                }
                            })
                            .catch(() => {
                                showPaymentFailed(orderId, lastOrderNumber);
                            });
                    },

                    modal: {
                        ondismiss: function() {
                            // ❌ USER CLOSED POPUP
                            showPaymentFailed(orderId, lastOrderNumber);
                        }
                    }
                };

                const rzpInstance = new Razorpay(options);

                // ❌ BANK / CARD FAILURE
                rzpInstance.on('payment.failed', function(response) {
                    console.error('Payment failed:', response.error);
                    showPaymentFailed(orderId, lastOrderNumber);
                });

                rzpInstance.open();
            })
            .catch(err => {
                console.error(err);
                alert('Unable to start payment');
                enablePayButton();
                paymentInProgress = false;
            });
    }


    /* ---------------- SUBMIT ---------------- */

    let submitted = false;
    let paymentInProgress = false;

    function submitPosOrder(e) {

        if (paymentInProgress) return;
        paymentInProgress = true;

        const btn = e.target;
        btn.disabled = true;
        btn.innerText = 'Opening Payment...';

        if (!Object.keys(cart).length) {
            alert('Add at least one product');
            btn.disabled = false;
            btn.innerText = 'Pay & Print';
            return;
        }

        const paymentMethod =
            document.querySelector('input[name="payment_method"]:checked').value;

        const payload = {
            items: Object.values(cart).map(i => ({
                product_id: i.product_id,
                qty: i.qty
            })),
            discount: document.getElementById('discount_input').value,
            payment_method: paymentMethod,
            customer_id: document.getElementById('customer_id').value || null
        };

        // CASH → normal submit
        if (paymentMethod === 'cash') {
            document.getElementById('items_input').value = JSON.stringify(payload.items);
            document.getElementById('posForm').submit();
            return;
        }

        // UPI / CARD → AJAX
        fetch("{{ route('pos.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                const text = await res.text();
                if (!text) throw new Error('Empty response');

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Non-JSON response:', text);
                    throw new Error('Invalid server response');
                }
            })

            .then(data => {
                lastOrderId = data.order_id;
                lastOrderNumber = data.order_number ?? `POS-${data.order_id}`;
                openRazorpay(data.order_id);
            })
            .catch(err => {
                console.error(err);
                alert('Payment initiation failed');
                btn.disabled = false;
                btn.innerText = 'Pay & Print';
            });
    }
</script>