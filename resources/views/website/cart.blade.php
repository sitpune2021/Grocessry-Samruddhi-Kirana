@extends('website.layout')

@section('title', 'Cart')

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
</div>

<!-- Cart Page -->
<div class="container-fluid py-5">
    <div class="container">

        <div class="row g-4">

            <!-- CART ITEMS -->
            <div class="col-lg-8">

                @if($cart && $cart->items->count())
                @foreach($cart->items as $item)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">

                        <div class="row align-items-center g-3">

                            <!-- Image -->
                            <div class="col-3 col-md-2 text-center">
                                <img src="{{ asset('storage/products/'.$item->product->product_images[0]) }}"
                                    class="img-fluid rounded" style="max-height:90px;">
                            </div>

                            <!-- Details -->
                            <div class="col-9 col-md-5">
                                <h6 class="fw-semibold mb-1">{{ $item->product->name }}</h6>
                                <p class="text-muted small mb-1">Seller: Store</p>
                                <p class="text-success small mb-0">In Stock</p>
                            </div>

                            <!-- Price -->
                            <div class="col-4 col-md-2 text-md-center">
                                <strong>₹ {{ $item->price }}</strong>
                            </div>

                            <!-- Quantity -->
                            <div class="col-4 col-md-2 text-md-center">
                                <span class="badge bg-light text-dark px-3 py-2">Qty: {{ $item->qty }}</span>
                            </div>

                            <!-- Remove -->
                            <div class="col-4 col-md-1 text-end">
                                <form action="{{ route('remove_cart_item', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Item Total</span>
                            <strong>₹ {{ $item->line_total }}</strong>
                        </div>

                    </div>
                </div>
                @endforeach
                @else
                <div class="alert alert-info text-center">
                    Your cart is empty
                </div>
                @endif

            </div>

            <!-- PRICE DETAILS -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top:90px;">
                    <div class="card-body">

                        <h6 class="fw-bold text-uppercase text-muted mb-3">Price Details</h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₹ {{ $cart ? number_format($cart->subtotal,2) : '0.00' }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery</span>
                            <span class="text-success">FREE</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span>₹ {{ $cart ? number_format($cart->total,2) : '0.00' }}</span>
                        </div>

                        <a href="{{ route('checkout') }}"
                            class="btn btn-warning w-100 mt-4 fw-semibold text-uppercase">
                            Place Order
                        </a>

                        <p class="text-success small mt-3 mb-0">
                            You will save more on this order
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection