@extends('website.layout')
@section('title','Checkout')
@section('content')

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

        <div class="row g-5">

            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Billing Details</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>First Name *</label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $address->first_name ?? '') }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Last Name *</label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $address->last_name ?? '') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Address *</label>
                            <input type="text" id="address" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $address->address ?? '') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>City *</label>
                                <input type="text" id="city" name="city"
                                    class="form-control" value="{{ old('city', $address->city ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Country *</label>
                                <input type="text" id="country" name="country"
                                    class="form-control" value="{{ old('country', $address->country ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Postcode *</label>
                                <input type="text" id="pincode" name="postcode" maxlength="6" pattern="[0-9]{6}"
                                    class="form-control" value="{{ old('postcode', $address->postcode ?? '') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mobile *</label>
                                <input type="text" name="phone" maxlength="10" pattern="[0-9]{10}"
                                    class="form-control" value="{{ old('phone', $address->phone ?? '') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $address->email ?? '') }}" required>
                        </div>

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
                            <input class="form-check-input" type="radio" name="payment_method" value="Cash" checked>
                            <label class="form-check-label">Cash On Delivery</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" value="online">
                            <label class="form-check-label">Online Payment</label>
                        </div>

                        <button type="button" id="rzp-button" class="btn btn-primary w-100 py-3">Place Order</button>
                        <small id="order_error" class="text-danger d-block mt-2"></small>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    function getLocation(btn) {
        btn.innerText = '📍 Detecting...';
        btn.disabled = true;
        if (!navigator.geolocation) {
            alert('Not supported');
            btn.disabled = false;
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`)
                .then(res => res.json())
                .then(data => {
                    let addr = data.address || {};
                    document.getElementById('address').value = `${addr.road||''} ${addr.suburb||''}`.trim();
                    document.getElementById('city').value = addr.city || addr.town || 'Pune';
                    document.getElementById('pincode').value = addr.postcode || '';
                    document.getElementById('country').value = addr.country || '';
                    btn.innerText = '📍 Location Added';
                    btn.disabled = false;
                });
        }, () => {
            btn.innerText = '📍 Use Current Location';
            btn.disabled = false;
            alert('Location denied');
        });
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
                document.getElementById('applied_coupon').value = code;
                document.getElementById('coupon_discount').value = data.discount;
            });
    }
</script>


<script>
    document.getElementById('rzp-button').addEventListener('click', function() {

        let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        let form = document.getElementById('checkoutForm');

        if (paymentMethod === 'Cash') {
            form.submit();
            return;
        }

        fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(orderRes => {

                fetch("/create-razorpay-order", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            amount: orderRes.amount,
                            order_id: orderRes.order_id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {

                        let options = {
                            key: "{{ config('services.razorpay.key') }}",
                            amount: orderRes.amount * 100,
                            currency: "INR",
                            order_id: data.razorpay_order_id,

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
                                        if (res.status) {
                                            window.location.href = res.redirect_url;
                                        } else {
                                            alert("Payment verification failed");
                                        }
                                    });
                            }
                        };

                        new Razorpay(options).open();
                    });
            });
    });
</script>
@endsection