@extends('website.layout')

@section('title', 'Checkout')

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-5 bg-dark">
    <h1 class="text-center text-white display-6">Checkout</h1>
</div>

<!-- Checkout Start -->
<div class="container py-5">
    <form action="{{ url('/place-order') }}" method="POST">
        @csrf
        <input type="hidden" name="coupon_code" id="coupon_code_hidden">

        <div class="row g-5">

            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

                        <h4 class="mb-4">Billing Details</h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="hidden" name="coupon_code" id="applied_coupon">
                                <input type="hidden" name="coupon_discount" id="coupon_discount">

                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $address->first_name ?? '') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $address->last_name ?? '') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address *</label>
                            <input type="text" id="address" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $address->address ?? '') }}" required>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" id="city" name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $address->city ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" id="country" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $address->country ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postcode *</label>
                                <input type="text" id="pincode" name="postcode" maxlength="6"
                                    pattern="[0-9]{6}"
                                    class="form-control @error('postcode') is-invalid @enderror"
                                    value="{{ old('postcode', $address->postcode ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile *</label>
                                <input type="text" name="phone" maxlength="10"
                                    pattern="[0-9]{10}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $address->phone ?? '') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
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
                                    <td>
                                        <img src="{{ asset('storage/products/'.$item->product->product_images[0]) }}"
                                            width="60" class="rounded">
                                    </td>
                                    <td>{{ $item->product->name }} × {{ $item->qty }}</td>
                                    <td class="text-end">₹{{ $item->line_total }}</td>
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
                                    <th class="text-end">
                                        ₹<span id="subtotal">{{ $cart->subtotal }}</span>
                                    </th>
                                </tr>

                                <tr id="discountRow" class="d-none">
                                    <th colspan="2">Coupon Discount</th>
                                    <th class="text-end text-danger">
                                        - ₹<span id="discountAmount">0</span>
                                    </th>
                                </tr>

                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end text-success fw-bold">
                                        ₹<span id="finalTotal">{{ $cart->total }}</span>
                                    </th>
                                </tr>

                            </tbody>
                        </table>

                        <!-- Coupon Apply -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="coupon_code" class="form-control" placeholder="Enter coupon code">
                                <button type="button" class="btn btn-outline-primary d-none">
                                    Apply
                                </button>
                            </div>

                            <small id="coupon_msg" class="text-danger d-none"></small>

                            <!-- Offer Codes -->
                            <div class="mt-2">
                                <small class="text-muted">Available Offers:</small>

                                <select class="form-select mt-1" id="coupon_dropdown" onchange="applyCouponFromDropdown(this)">
                                    <option value="">Select Offer Code</option>

                                    @foreach($coupons as $coupon)
                                    @php
                                    // Check if user already used this coupon
                                    $used = \App\Models\Order::where('user_id', auth()->id())
                                    ->where('coupon_code', $coupon->code)
                                    ->exists();
                                    @endphp

                                    @if(!$used)
                                    <option value="{{ $coupon->code }}">
                                        {{ $coupon->code }}
                                        @if($coupon->discount_type == 'flat')
                                        (₹{{ $coupon->discount_value }} OFF)
                                        @else
                                        ({{ $coupon->discount_value }}% OFF)
                                        @endif
                                    </option>
                                    @endif
                                    @endforeach
                                </select>

                            </div>
                        </div>



                        <!-- Payment -->
                        <div class="form-check my-3">
                            <input class="form-check-input" type="radio"
                                name="payment_method" value="Cash" checked>
                            <label class="form-check-label">Cash On Delivery</label>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio"
                                name="payment_method" value="online">
                            <label class="form-check-label">Online Payment</label>
                        </div>


                        <button type="submit" class="btn btn-primary w-100 py-3">
                            Place Order
                        </button>

                    </div>
                </div>
            </div>

        </div>

</div>

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

                let msg = document.getElementById('coupon_msg');

                if (!data.status) {
                    msg.classList.remove('d-none', 'text-success');
                    msg.classList.add('text-danger');
                    msg.innerText = data.message;
                    return;
                }

                // ✅ UI UPDATE
                msg.classList.remove('d-none', 'text-danger');
                msg.classList.add('text-success');
                msg.innerText = 'Coupon applied successfully';

                document.getElementById('discountRow').classList.remove('d-none');
                document.getElementById('discountAmount').innerText = data.discount;
                document.getElementById('finalTotal').innerText = data.final_total;

                // ✅🔥 VERY IMPORTANT (PLACE ORDER SATHI)
                document.getElementById('applied_coupon').value = code;
                document.getElementById('coupon_discount').value = data.discount;
            });
    }
</script>



@endsection