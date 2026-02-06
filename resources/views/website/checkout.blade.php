@extends('website.layout')
@section('title','Checkout')
@section('content')
<style>
    .address-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .address-card:hover {
        border-color: #198754;
        background: #f6fffa;
    }

    .address-card input {
        margin-top: 4px;
    }

    .floating-group {
        position: relative;
    }

    .floating-input {
        width: 100%;
        padding: 14px 12px;
        font-size: 14px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        outline: none;
    }

    .floating-placeholder {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        padding: 0 6px;
        color: #6c757d;
        font-size: 14px;
        pointer-events: none;
        transition: 0.2s ease;
    }

    .floating-input:focus {
        border-color: #198754;
    }

    .floating-input:focus+.floating-placeholder,
    .floating-input:not(:placeholder-shown)+.floating-placeholder {
        top: -6px;
        font-size: 12px;
        color: #198754;
    }
</style>
<div class="container-fluid page-header py-5 bg-dark">
    <h1 class="text-center text-white display-6">Checkout</h1>
</div>



<div class="container py-5">
    <form id="checkoutForm" action="{{ url('/place-order') }}" method="POST">
        @csrf
        <input type="hidden" name="coupon_code" id="applied_coupon">
        <input type="hidden" name="coupon_discount" id="coupon_discount">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_amount" id="razorpay_amount">
      
        <input type="hidden" name="final_total" id="final_total_input">
      

        <div class="row g-5">
            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Billing Details</h4>
                        <label class="fw-semibold d-block mb-2">Saved Addresses *</label>

                        <div class="d-flex gap-2 mb-3" id="addressTabs">
                            <button type="button" class="btn btn-outline-success address-tab" data-type="1">
                                🏠 Home
                            </button>
                            <button type="button" class="btn btn-outline-primary address-tab" data-type="2">
                                🏢 Work
                            </button>
                            <button type="button" class="btn btn-outline-warning address-tab" data-type="3">
                                📍 Other
                            </button>
                        </div>
                        <input type="hidden" name="address_id" id="address_id">
                        <input type="hidden" name="type" id="address_type">

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
                        <!-- <div class="mb-3">
                            <label>Address *</label>
                            <input type="text" id="address" placeholder="Flat" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $address->address ?? '') }}" required>
                        </div> -->

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
                                <div class="floating-group">
                                    <input type="text" name="postcode" id="pincode" class="floating-input"
                                        placeholder=" "
                                        maxlength="6"
                                        value="{{ old('postcode', $address->postcode ?? '') }}" required>
                                    <span class="floating-placeholder">Pincode *</span>
                                </div>
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
                        <!-- <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $address->email ?? '') }}" required>
                        </div> -->

                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation(this)">
                            📍 Use Current Location
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

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
    </form>
</div>

<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Location Script -->
<script>
    function getLocation(btn) {
        btn.innerText = '📍 Detecting...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(pos => {
            showPosition(pos);
            btn.innerText = '📍 Location Added';
            btn.disabled = false;
        }, () => {
            btn.innerText = '📍 Use Current Location';
            btn.disabled = false;
            alert('Location access denied');
        });
    }
</script>

<script>
    function getLocation(btn) {
        btn.innerText = '📍 Detecting...';
        btn.disabled = true;

        if (!navigator.geolocation) {
            alert("Geolocation not supported");
            btn.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(showPosition, () => {
            btn.innerText = '📍 Use Current Location';
            btn.disabled = false;
            alert('Location access denied');
        });
    }

    function showPosition(position) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
            .then(res => res.json())
            .then(data => {
                let addr = data.address || {};

                document.getElementById('address').value =
                    `${addr.road || ''} ${addr.suburb || ''}`.trim();

                document.getElementById('city').value =
                    addr.city || addr.town || addr.municipality || 'Pune';

                document.getElementById('pincode').value =
                    addr.postcode || '';

                document.getElementById('country').value =
                    addr.country || '';

                // button text update
                document.querySelector('[onclick^="getLocation"]').innerText = '📍 Location Added';
                document.querySelector('[onclick^="getLocation"]').disabled = false;
            });
    }
</script>

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
</script>

<script>
    document.querySelectorAll('.address-tab').forEach(btn => {
        btn.addEventListener('click', function() {

            // reset all buttons
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

            // find address by type
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
        document.getElementById('address').value = a.address ?? '';
        document.getElementById('city').value = a.city ?? '';
        document.getElementById('country').value = a.country ?? '';
        document.getElementById('pincode').value = a.postcode ?? '';
        document.querySelector('[name=phone]').value = a.phone ?? '';
        document.querySelector('[name=email]').value = a.email ?? '';
    }


    function clearAddressForm(type) {
        document.getElementById('address_type').value = type;
        document.getElementById('address_id').value = '';

        document.querySelectorAll('#checkoutForm input[type=text], #checkoutForm input[type=email]')
            .forEach(i => i.value = '');

        // Optional: reset IDs like address, pincode, country
        document.getElementById('address').value = '';
        document.getElementById('pincode').value = '';
        document.getElementById('country').value = '';
    }
</script>

@endsection



