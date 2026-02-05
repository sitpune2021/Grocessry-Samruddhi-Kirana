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
        <input type="hidden" name="address_id" id="address_id">
        <input type="hidden" name="final_total" id="final_total_input">



        <div class="row g-5">

            <!-- Saved Addresses -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Deliver to</h5>
                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            + Add New Address
                        </button>
                    </div>

                    @forelse($userAddresses as $addr)
                    <label class="address-card d-flex gap-3 p-3 rounded-3 border mb-2 cursor-pointer">
                        <input type="radio" name="selected_address"
                            onchange="fillAddress({{ $addr->id }})">

                        <div>
                            <div class="fw-semibold">
                                {{ $addr->type == 1 ? '🏠 Home' : ($addr->type == 2 ? '🏢 Work' : '📍 Other') }}
                            </div>
                            <small class="text-muted">
                                {{ $addr->address }}, {{ $addr->city }} - {{ $addr->postcode }}
                            </small>
                        </div>
                    </label>
                    @empty
                    <p class="text-muted mb-0">No saved addresses</p>
                    @endforelse
                </div>
            </div>

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
                            <input type="radio" name="payment_method" value="cash" checked>
                            <label class="form-check-label">Cash On Delivery</label>
                        </div>
                        <div class="form-check mb-4">
                            <input type="radio" name="payment_method" value="online">
                            <label class="form-check-label">Online Payment</label>
                        </div>
                        {{-- CASH FLOW ERROR --}}
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        {{-- CASH FLOW SUCCESS --}}
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif

                        <button type="button" id="rzp-button" class="btn btn-primary w-100 py-3">Place Order</button>
                        <small id="order_error" class="text-danger d-block mt-2"></small>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<div class="modal fade" id="addAddressModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5>Add New Address</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Address Type -->
                <div class="mb-3">
                    <label class="fw-semibold">Address Type</label>
                    <div class="d-flex gap-2">
                        <label class="btn btn-outline-success">
                            <input type="radio" name="type" value="1"> Home
                        </label>
                        <label class="btn btn-outline-success">
                            <input type="radio" name="type" value="2"> Work
                        </label>
                        <label class="btn btn-outline-success">
                            <input type="radio" name="type" value="3"> Other
                        </label>
                    </div>
                </div>

                <!-- Same fields you already use -->
                <input class="form-control mb-2" placeholder="Flat / House / Building">
                <input class="form-control mb-2" placeholder="Area / Locality">
                <input class="form-control mb-2" placeholder="/ area / town / village">
                <input class="form-control mb-2" placeholder="City">
                <input class="form-control mb-2" placeholder="Landmark (optional)">
                <input class="form-control mb-2" placeholder="Pincode">

                <button type="button" class="btn btn-success w-100 mt-3">
                    Save Address
                </button>

            </div>
        </div>
    </div>
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
    function applyCouponFromDropdown(el) {
        let code = el.value;
        if (!code) return;

        document.getElementById('coupon_code').value = code;
        document.getElementById('final_total_input').value = data.final_total;

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

<script>
    document.getElementById('rzp-button').addEventListener('click', function() {

        let paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
        let form = document.getElementById('checkoutForm');
        let errorBox = document.getElementById('order_error');

        errorBox.innerText = '';

        if (!paymentMethod) {
            errorBox.innerText = 'Please select payment method';
            return;
        }

        // CASH
        // ✅ CORRECT
        if (paymentMethod === 'cash') {
            form.submit();
            return;
        }


        // ONLINE
        fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: new FormData(form)
            })
            .then(res => {
                if (!res.ok) throw new Error('Order failed');
                return res.json();
            })
            .then(orderRes => {

                return fetch("/create-razorpay-order", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        amount: orderRes.amount,
                        order_id: orderRes.order_id
                    })
                }).then(res => res.json()).then(data => ({
                    orderRes,
                    data
                }));
            })
            .then(({
                orderRes,
                data
            }) => {

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
                                    errorBox.innerText = 'Payment verification failed';
                                }
                            });
                    }
                };

                new Razorpay(options).open();
            })
            .catch(err => {
                errorBox.innerText = err.message ?? 'Order failed';
            });


    });
</script>


<script>
    const addresses = @json($userAddresses);

    function fillAddress(id) {
        let a = addresses.find(x => x.id === id);
        if (!a) return;

        document.getElementById('address_id').value = id;

        document.querySelector('[name=first_name]').value = a.first_name;
        document.querySelector('[name=last_name]').value = a.last_name;
        document.getElementById('address').value = a.address;
        document.getElementById('city').value = a.city;
        document.getElementById('country').value = a.country;
        document.getElementById('pincode').value = a.postcode;
        document.querySelector('[name=phone]').value = a.phone;
        document.querySelector('[name=email]').value = a.email;
    }
</script>

@endsection