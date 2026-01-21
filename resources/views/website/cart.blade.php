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
                            <!-- <div class="col-4 col-md-2 text-md-center">
                                <span class="badge  text-dark px-3 py-2">Qty: {{ $item->qty }}</span>
                            </div> -->

                            <!-- Quantity -->
                            <div class="col-4 col-md-2 text-md-center">
                                <div class="qty-box d-inline-flex align-items-center gap-2"
                                    data-id="{{ $item->id }}">

                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">
                                        -
                                    </button>

                                    <span class="badge bg-light text-dark px-3 py-2 qty-text">
                                        {{ $item->qty }}
                                    </span>

                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">
                                        +
                                    </button>

                                </div>
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
                            <strong id="item-total-{{ $item->id }}">
                                ₹ {{ $item->line_total }}
                            </strong>
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
                <div class="card shadow-sm sticky-top " id="price-details " style="top:90px;">
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
                            <span id="cart-total">
                                ₹ {{ $cart ? number_format($cart->total,2) : '0.00' }}
                            </span>
                        </div>

                        <a href="{{ route('checkout') }}"
                            id="place-order-btn"
                            class="btn btn-warning w-100 mt-4 fw-semibold text-uppercase">
                            Check Out
                        </a>

                        <button id="playVoiceBtn" class="btn btn-danger mt-2 d-none">
                            Check Out
                        </button>

                        <p id="order-msg" class="text-danger small mt-2 d-none text-center">

                            Online orders are currently closed.<br>

                            Orders will resume tomorrow at <strong>6:00 AM</strong>.
                        </p>
                        <p class="text-success small mt-3 mb-0">
                            You will save more on this order
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).on('click', '.qty-plus, .qty-minus', function() {

        let box = $(this).closest('.qty-box');
        let itemId = box.data('id');
        let qtyText = box.find('.qty-text');
        let currentQty = parseInt(qtyText.text());

        let newQty = $(this).hasClass('qty-plus') ?
            currentQty + 1 :
            currentQty - 1;

        if (newQty < 1) return;

        $.ajax({
            url: "/cart/update/" + itemId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT",
                qty: newQty
            },
            success: function(res) {

                if (res.success) {

                    // Qty update
                    qtyText.text(res.qty);

                    // Item total update
                    $('#item-total-' + itemId).text('₹ ' + res.line_total);

                    // Cart total update
                    $('#cart-total').text('₹ ' + res.cart_total);

                    // 🔥 Header cart count update
                    if (res.cart_count > 0) {
                        $('#cart-count').text(res.cart_count).show();
                    } else {
                        $('#cart-count').hide();
                    }
                }
            },
            error: function() {
                alert('Something went wrong!');
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const checkoutBtn = document.getElementById("place-order-btn");
        const voiceBtn = document.getElementById("playVoiceBtn");
        const orderMsg = document.getElementById("order-msg");

        if (!checkoutBtn || !voiceBtn || !orderMsg) return;

        const now = new Date();
        const hour = now.getHours(); // 0–23

        /*
            OPEN  : 06:00 – 18:59
            CLOSED: 19:00 – 05:59
        */

        const isOpenTime = hour >= 6 && hour < 19;

        if (isOpenTime) {
            // 🟢 OPEN
            checkoutBtn.classList.remove("d-none");
            voiceBtn.classList.add("d-none");
            orderMsg.classList.add("d-none");
        } else {
            // 🔴 CLOSED
            checkoutBtn.classList.add("d-none");
            voiceBtn.classList.remove("d-none");
            orderMsg.classList.remove("d-none");
        }

        // 🔊 Voice on click
        voiceBtn.addEventListener("click", function() {

            if (!('speechSynthesis' in window)) {
                alert("Voice not supported in this browser");
                return;
            }

            const msg = new SpeechSynthesisUtterance(
                "कृपया लक्ष द्या. सध्या ऑनलाइन ऑर्डर बंद आहेत. ऑर्डर उद्या सकाळी सहा वाजता सुरू होतील."
            );

            // Hindi voice works everywhere (Marathi fallback)
            msg.lang = "hi-IN";
            msg.rate = 0.9;
            msg.pitch = 1;
            msg.volume = 1;

            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(msg);
        });
    });
</script>





@endsection