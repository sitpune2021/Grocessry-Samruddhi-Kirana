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

        <div class="row g-5">

            <!-- Billing Details -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">

                        <h4 class="mb-4">Billing Details</h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $address->first_name ?? '') }}">
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $address->last_name ?? '') }}">
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address *</label>
                            <input type="text" id="address" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $address->address ?? '') }}">
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City *</label>
                                <input type="text" id="city" name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $address->city ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" id="country" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $address->country ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postcode *</label>
                                <input type="text" name="postcode"
                                    class="form-control @error('postcode') is-invalid @enderror"
                                    value="{{ old('postcode', $address->postcode ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile *</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $address->phone ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $address->email ?? '') }}">
                        </div>

                        <!-- Current Location -->
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation()">
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
                                <tr>
                                    <th colspan="2">Subtotal</th>
                                    <th class="text-end">₹{{ $cart->subtotal }}</th>
                                </tr>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th class="text-end text-success">₹{{ $cart->total }}</th>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Payment -->
                        <div class="form-check my-3">
                            <input class="form-check-input" type="radio" name="payment_method" value="cod" checked>
                            <label class="form-check-label">Cash On Delivery</label>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" value="paypal">
                            <label class="form-check-label">Paypal</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3">
                            Place Order
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Location Script -->
<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation not supported");
        }
    }

    function showPosition(position) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('address').value = data.display_name || '';
                document.getElementById('city').value = data.address.city || data.address.town || '';
                document.getElementById('country').value = data.address.country || '';
            });
    }
</script>

@endsection