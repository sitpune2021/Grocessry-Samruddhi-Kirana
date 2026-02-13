`@extends('website.layout')
@section('title','Checkout')
@section('content')

<!-- <div class="container-fluid page-header py-5 bg-dark">
    <h1 class="text-center text-white display-6">Checkout</h1>
</div> -->

<div class="container py-5" style="margin-top:150px;">
    <form id="checkoutForm" action="{{ url('/place-order') }}" method="POST">
        @csrf
        <input type="hidden" id="address_id" name="address_id">
        <input type="hidden" id="address_type" name="type">
        <input type="hidden" id="selected_address" name="selected_address">
        <input type="hidden" id="payment_method" name="payment_method">
        <input type="hidden" id="coupon_code" name="coupon_code">
        <input type="hidden" id="applied_coupon" name="applied_coupon">
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
                        <!-- Saved Address List -->


                        <h4 class="mb-4">Your Order</h4>
                        <table class="table align-middle">
                            <tbody>
                                @if($cart && $cart->items->count())
                                @foreach($cart->items as $item)
                                <tr>
                                    <td><img src="{{ asset('storage/products/'.$item->product->product_images[0]) }}" width="60" class="rounded"></td>
                                    <td>{{ $item->product->name }} × {{ $item->qty }}</td>
                                    <td class="text-end">₹{{ $item->line_total }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Your cart is empty</td>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="2">Subtotal</th>
                                    <th class="text-end">₹<span id="subtotal">{{ $cart->subtotal }}</span></th>
                                </tr>
                                <tr id="discountRow" class="d-none">
                                    <th colspan="2">Coupon Discount</th>
                                    <th class="text-end text-danger">- ₹<span id="discountAmount">0</span></th>
                                </tr>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end text-success fw-bold">₹<span id="finalTotal">{{ $cart->subtotal }}</span></th>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Coupon -->
                        <div class="mb-3">
                            <select class="form-select" id="coupon_dropdown" onchange="applyCouponFromDropdown(this)">
                                <option value="">Select Offer Code</option>
                                @foreach($coupons as $coupon)
                                @php
                                $used = \App\Models\Order::where('user_id', auth()->id())->where('coupon_code', $coupon->code)->exists();
                                @endphp
                                @if(!$used)
                                <option value="{{ $coupon->code }}">{{ $coupon->code }}
                                    @if($coupon->discount_type=='flat') (₹{{ $coupon->discount_value }} OFF)
                                    @else ({{ $coupon->discount_value }}% OFF) @endif
                                </option>
                                @endif
                                @endforeach
                            </select>
                            <small id="coupon_msg" class="text-danger d-none"></small>
                        </div>

                        <!-- Payment -->
                        <div class="form-check my-3">
                            <input class="form-check-input" type="radio" name="payment_method" value="cash" checked>
                            <label class="form-check-label">Cash On Delivery</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" value="online">
                            <label class="form-check-label">Online Payment</label>
                        </div>

                        <button type="button" id="rzp-button" class="btn btn-primary w-100 py-3">Place Order</button>

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

<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- rezore pay -->
<script>
    let rzpProcessing = false;

    document.getElementById('rzp-button').addEventListener('click', async function(e) {
        e.preventDefault();

        if (rzpProcessing) return;
        rzpProcessing = true;

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const form = document.getElementById('checkoutForm');

        // 💰 CASH FLOW
        if (paymentMethod === 'cash') {
            form.submit();
            return;
        }

        try {
            // 1️⃣ Place Order
            const orderResponse = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: new FormData(form)
            });

            const orderData = await orderResponse.json();

            if (!orderData.status) {
                alert(orderData.message || "Order failed");
                rzpProcessing = false;
                return;
            }

            // 2️⃣ Create Razorpay Order (SERVER)
            const rzpResponse = await fetch("/create-razorpay-order", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    order_id: orderData.order_id,
                    amount: orderData.amount
                })
            });

            const rzpData = await rzpResponse.json();

            // 3️⃣ Open Razorpay (ONCE)
            const options = {
                key: "{{ config('services.razorpay.key') }}",
                amount: orderData.amount * 100,
                currency: "INR",
                order_id: rzpData.razorpay_order_id,
                name: "Your Store Name",
                description: "Order Payment",

                handler: function(response) {
                    fetch("{{ route('payment.success') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(response)
                        })
                        .then(res => res.json())
                        .then(res => {
                            console.log(res);
                            if (res.status) {
                                window.location.href = res.redirect_url;
                            } else {
                                alert("Payment verification failed");
                            }
                        });
                },
                modal: {
                    ondismiss: function() {
                        rzpProcessing = false;
                        alert("Payment cancelled");
                    }
                }
            };

            const rzp = new Razorpay(options);
            rzp.open(); // ✅ ONLY ONCE

        } catch (err) {
            console.error(err);
            rzpProcessing = false;
            alert("Order failed");
        }
    });
</script>

<script>
    const addresses = @json($userAddresses);

    document.addEventListener('DOMContentLoaded', function() {

        const selected = @json($defaultAddress);
        if (!selected) return;
        selectAddress(selected.id, selected.type);

        // set hidden inputs (MOST IMPORTANT)
        document.getElementById('address_id').value = selected.id;
        document.getElementById('address_type').value = selected.type;
        document.getElementById('selected_address').value = selected.id;

        if (addressIdInput && addressTypeInput) {
            addressIdInput.value = selected.id;
            addressTypeInput.value = selected.type;
        }

        // auto-check radio
        const radio = document.getElementById('address_' + selected.id);
        if (radio) {
            radio.checked = true;
        }

    });


    document.querySelectorAll('.address-tab').forEach(btn => {
        btn.addEventListener('click', function() {

            // reset buttons
            document.querySelectorAll('.address-tab').forEach(b => {
                b.classList.remove('btn-success', 'btn-primary', 'btn-warning', 'active');
                if (b.dataset.type == 1) b.classList.add('btn-outline-success');
                if (b.dataset.type == 2) b.classList.add('btn-outline-primary');
                if (b.dataset.type == 3) b.classList.add('btn-outline-warning');
            });

            let type = parseInt(this.dataset.type);

            // highlight selected
            this.classList.add('active');
            if (type === 1) this.classList.replace('btn-outline-success', 'btn-success');
            if (type === 2) this.classList.replace('btn-outline-primary', 'btn-primary');
            if (type === 3) this.classList.replace('btn-outline-warning', 'btn-warning');

            let addr = addresses.find(a => parseInt(a.type) === type);

            if (addr) {
                fillAddressFields(addr);
            } else {
                clearAddressForm(type);
            }
        });
    });

    function fillAddressFields(a) {
        document.getElementById('address_type').value = a.type;
        document.getElementById('address_id').value = a.id;

        document.querySelector('[name=first_name]').value = a.first_name ?? '';
        document.querySelector('[name=last_name]').value = a.last_name ?? '';
        document.querySelector('[name=flat_house]').value = a.flat_house ?? '';
        document.querySelector('[name=floor]').value = a.floor ?? '';
        document.querySelector('[name=area]').value = a.area ?? '';
        document.querySelector('[name=landmark]').value = a.landmark ?? '';
        document.querySelector('[name=city]').value = a.city ?? '';
        if (!pincodeManuallyChanged) {
            document.getElementById('pincode').value =
                a.postcode && a.postcode.trim() !== '' ?
                a.postcode :
                deliveryPincode;
        }


        document.querySelector('[name=phone]').value = a.phone ?? '';
    }

    function clearAddressForm(type) {
        document.getElementById('address_type').value = type;


        ['first_name', 'last_name', 'flat_house', 'floor', 'area', 'landmark', 'city', 'phone']
        .forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.value = '';
        });


        if (!pincodeManuallyChanged) {
            document.getElementById('pincode').value = deliveryPincode;
        }
    }

    function saveAddress() {
        const type = document.getElementById('address_type').value || 1;

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
            phone: document.querySelector('[name="phone"]').value,
        };

        fetch('/save-address', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json' // 🔥 REQUIRED
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    alert('Address saved successfully!');

                    // Close modal
                    let modalEl = document.getElementById('billingAddressModal');
                    let modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (!modalInstance) modalInstance = new bootstrap.Modal(modalEl);
                    modalInstance.hide();

                    // Optionally reload page to see new address
                    window.location.href = "{{ url('/checkout') }}";
                } else {
                    alert('Failed to save address');
                }
            })
            .catch(err => console.error(err));
    }

    function editAddress(event, type) {
        event.stopPropagation();

        // address type set (home/work)
        document.getElementById('address_type').value = type;

        // Bootstrap 5 modal open
        let modal = new bootstrap.Modal(
            document.getElementById('billingAddressModal')
        );
        modal.show();
    }

    function selectAddress(addressId, addressType) {

        document.getElementById('address_id').value = addressId;
        document.getElementById('address_type').value = addressType;
        document.getElementById('selected_address').value = addressId;

        // uncheck all radios
        document.querySelectorAll('.address-radio').forEach(r => r.checked = false);

        // check selected radio
        const radio = document.getElementById('address_' + addressId);
        if (radio) radio.checked = true;
    }

    function applyCouponFromDropdown(el) {
        let code = el.value;
        if (!code) return;
        document.getElementById('coupon_code').value = code;
        applyCoupon();
    }

    function applyCoupon() {
        let code = document.getElementById('coupon_code').value;
        let subtotal = parseFloat(document.getElementById('subtotal').innerText);

        fetch("{{ route('apply.coupon') }}", {
                method: 'POST',
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
                let msg = document.getElementById('coupon_msg');

                if (!data.status) {
                    msg.classList.remove('d-none', 'text-success');
                    msg.classList.add('text-danger');
                    msg.innerText = data.message;
                    return;
                }

                msg.classList.remove('d-none', 'text-danger');
                msg.classList.add('text-success');
                msg.innerText = 'Coupon applied successfully';

                document.getElementById('discountRow').classList.remove('d-none');
                document.getElementById('discountAmount').innerText = data.discount;
                document.getElementById('finalTotal').innerText = data.final_total;

                // ✅ store for order
                document.getElementById('applied_coupon').value = code;
                document.getElementById('coupon_discount').value = data.discount;
            });
    }
</script>


@endsection