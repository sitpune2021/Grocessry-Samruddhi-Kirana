`@extends('website.layout')
@section('title','Checkout')
@section('content')
<style>
    .payment-option {
        padding: 14px;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        cursor: pointer;
        margin-bottom: 10px;
        transition: 0.3s;
        font-weight: 500;
    }

    .payment-option:hover {
        background: #f8f9fa;
    }

    .payment-option.active {
        border-color: #198754;
        background: #e9f7ef;
    }

    .address-box {
        cursor: pointer;
        transition: 0.3s;
    }

    .address-box:hover {
        transform: translateY(-2px);
        border-color: #198754 !important;
    }

    .address-box.active {
        border: 2px solid #198754 !important;
        background: #f8fff9;
    }
</style>

<!-- <div class="container-fluid page-header py-5 bg-dark">
    <h1 class="text-center text-white display-6">Checkout</h1>
</div> -->
<div class="container py-5" style="margin-top:150px;">
    <form id="checkoutForm" action="{{ url('/place-order') }}" method="POST">
        @csrf

        <input type="hidden" id="address_id" name="address_id">
        <input type="hidden" id="address_type" name="type">
        <input type="hidden" id="selected_address" name="selected_address">
        <input type="hidden" id="payment_method" name="payment_method" value="cash">
        <input type="hidden" id="coupon_code" name="coupon_code">
        <!-- <input type="hidden" id="applied_coupon" name="applied_coupon"> -->
        <input type="hidden" id="coupon_discount" name="coupon_discount">
        <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">
        <input type="hidden" id="razorpay_order_id" name="razorpay_order_id">
        <input type="hidden" id="razorpay_signature" name="razorpay_signature">
        <div class="row g-5">
            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Delivery Address</h4>

                    <button type="button"
                        class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#billingAddressModal">
                        Add Address
                    </button>
                </div>
                <p class="text-muted small">
                    Select or add delivery address to continue
                </p>

                <div id="addressBoxList" class="mb-4">

                    @foreach($userAddresses as $address)
                    <div class="card rounded-4 shadow-sm mb-2 address-box">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="form-check">

                                    <input class="form-check-input"
                                        type="radio"
                                        id="address_{{ $address->id }}"
                                        @checked($defaultAddress && $defaultAddress->id == $address->id)
                                    onclick="selectAddress({{ $address->id }}, {{ $address->type }})">

                                    <label class="form-check-label w-100" for="address_{{ $address->id }}">

                                        @if($address->type == 1)
                                        <span class="badge bg-success mb-1">HOME</span>
                                        @elseif($address->type == 2)
                                        <span class="badge bg-primary mb-1">WORK</span>
                                        @else
                                        <span class="badge bg-warning mb-1">OTHER</span>
                                        @endif

                                        <p class="mb-1 fw-semibold">
                                            {{ $address->first_name }} {{ $address->last_name }}
                                        </p>

                                        <p class="mb-0 text-muted small">
                                            {{ $address->flat_house }},
                                            {{ $address->area }},
                                            {{ $address->city }} - {{ $address->postcode }}
                                        </p>

                                        <p class="mb-0 text-muted small">
                                            📞 {{ $address->phone }}
                                        </p>

                                    </label>
                                </div>
                                <i class="bi bi-pencil text-primary fs-5"
                                    onclick="editAddress(event, {{ $address->id }})"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Order Summary -->
            <div class="col-lg-5">

                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

                        <h4 class="mb-4 fw-bold">Order Summary</h4>

                        <table class="table align-middle">
                            <tbody>
                                @if($cart && $cart->items->count())
                                @foreach($cart->items as $item)
                                <tr>
                                    <td width="70">
                                        <img src="{{ asset('storage/products/'.$item->product->product_images[0]) }}"
                                            width="60"
                                            class="rounded-3 shadow-sm">
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <small class="text-muted">Qty: {{ $item->qty }}</small>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        ₹{{ $item->line_total }}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Your cart is empty
                                    </td>
                                </tr>
                                @endif

                                <tr>
                                    <th colspan="2">Subtotal</th>
                                    <th class="text-end">₹<span id="subtotal">{{ $cart->subtotal }}</span></th>
                                </tr>

                                <tr id="discountRow" class="d-none">
                                    <th colspan="2" class="text-success">Coupon Discount</th>
                                    <th class="text-end text-danger">
                                        - ₹<span id="discountAmount">0</span>
                                    </th>
                                </tr>

                                <tr class="border-top">
                                    <th colspan="2" class="fs-5">Total</th>
                                    <th class="text-end text-success fs-5">
                                        ₹<span id="finalTotal">{{ $cart->subtotal }}</span>
                                    </th>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Coupon Professional -->
                        <div class="card bg-light border-0 rounded-4 mb-3">
                            <div class="card-body p-3">
                                <label class="fw-semibold mb-2 d-block">
                                    Have a Promo Code?
                                </label>

                                <div class="input-group">

                                    <input type="text"
                                        id="coupon_input"
                                        class="form-control"
                                        placeholder="Enter coupon code">

                                    <button class="btn btn-dark"
                                        type="button"
                                        onclick="applyCoupon()">
                                        Apply
                                    </button>
                                </div>

                                <small id="coupon_msg" class="d-none mt-2"></small>
                            </div>
                        </div>

                        <!-- Payment Professional -->
                        <div class="mb-4">

                            <label class="fw-semibold mb-3 d-block">
                                Select Payment Method
                            </label>

                            <div class="payment-option active"
                                onclick="selectPayment('cash', this)">
                                <i class="bi bi-cash-stack me-2"></i>
                                Cash On Delivery
                            </div>

                            <div class="payment-option"
                                onclick="selectPayment('online', this)">
                                <i class="bi bi-credit-card me-2"></i>
                                Online Payment (Razorpay)
                            </div>

                        </div>

                        <button type="button"
                            id="rzp-button"
                            class="btn btn-success w-100 py-3 fw-semibold">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="billingAddressModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4">

                    <div class="modal-header">
                        <h5 class="modal-title">Billing Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body p-4">

                                <label class="fw-semibold d-block mb-2">Saved Addresses *</label>

                                <div class="d-flex gap-2 mb-3" id="addressTabs">
                                    <button type="button" class="btn btn-outline-success address-tab" data-type="1">🏠 Home</button>
                                    <button type="button" class="btn btn-outline-primary address-tab" data-type="2">🏢 Work</button>
                                    <button type="button" class="btn btn-outline-warning address-tab" data-type="3">📍 Other</button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-group">
                                            <input type="text" name="first_name" class="floating-input"
                                                placeholder=" "
                                                value="{{ old('first_name', $address->first_name ?? '') }}" required>
                                            <span class="floating-placeholder">First Name *</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="floating-group">
                                            <input type="text" name="last_name" class="floating-input"
                                                placeholder=" "
                                                value="{{ old('last_name', $address->last_name ?? '') }}" required>
                                            <span class="floating-placeholder">Last Name *</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="floating-group">
                                        <input type="text" name="flat_house" class="floating-input"
                                            placeholder=" "
                                            value="{{ old('flat_house', $address->flat_house ?? '') }}" required>
                                        <span class="floating-placeholder">Flat / House no / Building *</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="floating-group">
                                        <input type="text" name="floor" class="floating-input"
                                            placeholder=" "
                                            value="{{ old('floor', $address->floor ?? '') }}">
                                        <span class="floating-placeholder">Floor (optional)</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="floating-group">
                                        <input type="text" name="area" class="floating-input"
                                            placeholder=" "
                                            value="{{ old('area', $address->area ?? '') }}" required>
                                        <span class="floating-placeholder">Area / Sector / Locality *</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="floating-group">
                                        <input type="text" name="landmark" class="floating-input"
                                            placeholder=" "
                                            value="{{ old('landmark', $address->landmark ?? '') }}">
                                        <span class="floating-placeholder">Nearby Landmark</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="floating-group">
                                            <input type="text" name="city" class="floating-input"
                                                placeholder=" "
                                                value="{{ old('city', $address->city ?? '') }}" required>
                                            <span class="floating-placeholder">City *</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label>Postcode *</label>
                                        <input type="text" id="pincode" name="postcode" maxlength="6" pattern="[0-9]{6}"
                                            class="form-control" value="{{ old('postcode', $address->postcode ?? '') }}" required>
                                        <span class="floating-placeholder">Pincode *</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="floating-group">
                                        <input type="text" name="phone" class="floating-input"
                                            placeholder=" "
                                            maxlength="10"
                                            value="{{ old('phone', $address->phone ?? '') }}" required>
                                        <span class="floating-placeholder">Mobile *</span>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation(this)">
                                    📍 Use Current Location
                                </button>

                                <div class="modal-footer">
                                    <button type="button"
                                        class="btn btn-success w-100"
                                        onclick="saveAddress()">
                                        Save Address
                                    </button>
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
@endsection

<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


<script>
    let rzpProcessing = false;

    document.addEventListener("DOMContentLoaded", function() {

        const form = document.getElementById("checkoutForm");
        const rzpBtn = document.getElementById("rzp-button");

        rzpBtn.addEventListener("click", async function(e) {

            e.preventDefault();
            if (rzpProcessing) return;

            rzpProcessing = true;
            rzpBtn.disabled = true;
            rzpBtn.innerHTML = "Processing <span class='spinner-border spinner-border-sm'></span>";

            const paymentMethod = document.getElementById("payment_method").value;

            if (!document.getElementById("selected_address").value) {

                alert("Please select delivery address");

                rzpProcessing = false;
                rzpBtn.disabled = false;
                rzpBtn.innerHTML = "Place Order";
                return;
            }

            if (paymentMethod === "cash") {
                form.submit();
                return;
            }
            try {
                /* STEP 1 CREATE ORDER */

                const orderResponse = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: new FormData(form)
                });

                console.log("STATUS:", orderResponse.status);

                const orderData = await orderResponse.json();

                console.log("ORDER DATA:", orderData);


                if (!orderData.status) {

                    alert(orderData.message || "Order failed");

                    rzpProcessing = false;
                    rzpBtn.disabled = false;
                    rzpBtn.innerHTML = "Place Order";
                    return;
                }

                /* STEP 2 CREATE RAZORPAY ORDER */

                // const rzpResponse = await fetch("{{ route('checkout.razorpay.create') }}", {

                //     method: "POST",

                //     credentials: "same-origin", // ⭐ IMPORTANT

                //     headers: {
                //         "Content-Type": "application/json",
                //         "Accept": "application/json",
                //         "X-CSRF-TOKEN": "{{ csrf_token() }}"
                //     },

                //     body: JSON.stringify({
                //         order_id: orderData.order_id,
                //         amount: orderData.amount

                //     })

                // });

                const rzpResponse = await fetch("{{ route('checkout.razorpay.create') }}", {
                    method: "POST",
                    credentials: "include",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        order_id: orderData.order_id,
                        amount: orderData.amount
                    })
                });

                const rzpData = await rzpResponse.json();

                console.log("RAZORPAY DATA:", rzpData);

                const options = {

                    key: "{{ config('services.razorpay.key') }}",

                    amount: rzpData.amount,
                    currency: "INR",
                    order_id: rzpData.razorpay_order_id,

                    name: "Your Store",
                    description: "Order Payment",

                    handler: function(response) {

                        fetch("{{ route('payment.success') }}", {

                                method: "POST",

                                credentials: "same-origin", // ⭐ IMPORTANT

                                headers: {
                                    "Content-Type": "application/json",
                                    "Accept": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },

                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    order_id: orderData.order_id
                                })

                            })
                            .then(res => res.json())
                            .then(data => {

                                if (data.status) {

                                  window.location.href = "{{ route('my_orders') }}";

                                } else {

                                    alert(data.message || "Payment verification failed");

                                }

                            });

                    },

                    theme: {
                        color: "#198754"
                    }

                };

                const rzp = new Razorpay(options);
                rzp.open();

            } catch (error) {

                console.error("Checkout Error:", error);

                alert("Checkout failed");

                rzpProcessing = false;
                rzpBtn.disabled = false;
                rzpBtn.innerHTML = "Place Order";

            }

        });

    });
</script>

<script>
    const addresses = @json($userAddresses);
    const deliveryPincode = @json($deliveryPincode);

    let pincodeManuallyChanged = false;

    /*
       DOM READY
    */
    document.addEventListener("DOMContentLoaded", function() {

        const selected = @json($defaultAddress);

        if (selected) {
            selectAddress(selected.id, selected.type);
        }

        /* ADDRESS TAB SWITCH */
        document.querySelectorAll(".address-tab").forEach(btn => {

            btn.addEventListener("click", function() {

                document.querySelectorAll(".address-tab").forEach(b => {

                    b.classList.remove(
                        "btn-success",
                        "btn-primary",
                        "btn-warning",
                        "active"
                    );

                    if (b.dataset.type == 1) b.classList.add("btn-outline-success");
                    if (b.dataset.type == 2) b.classList.add("btn-outline-primary");
                    if (b.dataset.type == 3) b.classList.add("btn-outline-warning");

                });

                const type = parseInt(this.dataset.type);

                this.classList.add("active");

                if (type === 1)
                    this.classList.replace("btn-outline-success", "btn-success");

                if (type === 2)
                    this.classList.replace("btn-outline-primary", "btn-primary");

                if (type === 3)
                    this.classList.replace("btn-outline-warning", "btn-warning");

                let addr = addresses.find(a => parseInt(a.type) === type);

                if (addr) fillAddressFields(addr);
                else clearAddressForm(type);

            });
        });

        /* PINCODE CHANGE */
        const pincodeInput = document.getElementById("pincode");

        if (pincodeInput) {

            pincodeInput.addEventListener("input", function() {

                pincodeManuallyChanged = true;
            });
        }
    });
    /*
       SELECT ADDRESS
    */
    function selectAddress(addressId, addressType) {

        document.querySelectorAll(".address-box")
            .forEach(box => box.classList.remove("active"));

        const radio = document.getElementById("address_" + addressId);

        if (radio) {

            radio.checked = true;

            radio.closest(".address-box")
                ?.classList.add("active");

        }

        document.getElementById("address_id").value = addressId;
        document.getElementById("address_type").value = addressType;
        document.getElementById("selected_address").value = addressId;

    }

    /*
       FILL ADDRESS FORM
    */
    function fillAddressFields(a) {

        document.querySelector('[name=first_name]').value = a.first_name ?? "";
        document.querySelector('[name=last_name]').value = a.last_name ?? "";
        document.querySelector('[name=flat_house]').value = a.flat_house ?? "";
        document.querySelector('[name=floor]').value = a.floor ?? "";
        document.querySelector('[name=area]').value = a.area ?? "";
        document.querySelector('[name=landmark]').value = a.landmark ?? "";
        document.querySelector('[name=city]').value = a.city ?? "";
        document.querySelector('[name=phone]').value = a.phone ?? "";

        if (!pincodeManuallyChanged) {

            document.getElementById("pincode").value =
                a.postcode && a.postcode.trim() !== "" ?
                a.postcode :
                deliveryPincode;
        }
    }
    /*
       CLEAR ADDRESS FORM
    */
    function clearAddressForm(type) {

        document.getElementById("address_type").value = type;

        [
            "first_name",
            "last_name",
            "flat_house",
            "floor",
            "area",
            "landmark",
            "city",
            "phone"
        ].forEach(name => {

            const el = document.querySelector(`[name="${name}"]`);

            if (el) el.value = "";

        });

        if (!pincodeManuallyChanged)
            document.getElementById("pincode").value = deliveryPincode;

    }
    /*
       SAVE ADDRESS
    */
    function saveAddress() {

        const type = document.getElementById("address_type").value || 1;

        let data = {

            type: type,

            first_name: document.querySelector('[name="first_name"]').value,
            last_name: document.querySelector('[name="last_name"]').value,
            flat_house: document.querySelector('[name="flat_house"]').value,
            floor: document.querySelector('[name="floor"]').value,
            area: document.querySelector('[name="area"]').value,
            landmark: document.querySelector('[name="landmark"]').value,
            city: document.querySelector('[name="city"]').value,
            postcode: document.querySelector('[name="postcode"]').value,
            phone: document.querySelector('[name="phone"]').value

        };

        fetch("{{ route('save.address') }}", {

                method: "POST",

                headers: {

                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "Content-Type": "application/json"

                },

                body: JSON.stringify(data)

            })
            .then(res => res.json())
            .then(res => {

                if (res.status) {

                    alert("Address saved successfully");

                    const modalEl = document.getElementById("billingAddressModal");

                    const modalInstance =
                        bootstrap.Modal.getInstance(modalEl) ||
                        new bootstrap.Modal(modalEl);

                    modalInstance.hide();

                    location.reload();

                } else {

                    alert(res.message || "Address save failed");

                }

            })
            .catch(() => alert("Server error"));

    }

    /*
       EDIT ADDRESS
    */
    function editAddress(event, addressId) {

        event.stopPropagation();

        const addr = addresses.find(a => a.id == addressId);

        if (!addr) return;

        fillAddressFields(addr);

        document.getElementById("address_id").value = addr.id;
        document.getElementById("address_type").value = addr.type;

        const modal = new bootstrap.Modal(
            document.getElementById("billingAddressModal")
        );

        modal.show();

    }

    /*
       APPLY COUPON
    */
    function applyCoupon() {

        let code = document.getElementById("coupon_input").value.trim();

        let subtotal = parseFloat(
            document.getElementById("subtotal").innerText
        );

        if (!code) {

            showCouponMsg("Enter coupon code", false);

            return;

        }

        fetch("{{ route('apply.coupon') }}", {

                method: "POST",

                headers: {

                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"

                },

                body: JSON.stringify({

                    coupon_code: code,
                    subtotal: subtotal

                })

            })
            .then(res => res.json())
            .then(data => {

                if (!data.status) {

                    showCouponMsg(data.message, false);
                    return;

                }

                document.getElementById("discountRow")
                    .classList.remove("d-none");

                document.getElementById("discountAmount")
                    .innerText = data.discount;

                document.getElementById("finalTotal")
                    .innerText = data.final_total;

                document.getElementById("coupon_code").value = code;
                document.getElementById("coupon_discount").value = data.discount;

                showCouponMsg("Coupon applied successfully", true);

            })
            .catch(() => {

                showCouponMsg("Server error", false);

            });

    }

    /*  COUPON MESSAGE
     */
    function showCouponMsg(message, success) {

        let msg = document.getElementById("coupon_msg");

        msg.classList.remove("d-none", "text-danger", "text-success");

        msg.classList.add(
            success ? "text-success" : "text-danger"
        );

        msg.innerText = message;

    }

    /*  PAYMENT SELECT
     */
    function selectPayment(method, el) {

        document.querySelectorAll(".payment-option")
            .forEach(option => option.classList.remove("active"));

        el.classList.add("active");

        const paymentInput = document.getElementById("payment_method");

        if (paymentInput)
            paymentInput.value = method;

    }
</script>