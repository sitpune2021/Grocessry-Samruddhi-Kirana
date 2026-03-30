@extends('website.layout')

@section('title', $product->name)

@section('content')

<!-- Similar products -->
<!-- <div class="container-fluid page-header py-4 mb-5 bg-dark">
    <h1 class="text-center text-white display-6">Product Details</h1>
</div> -->

<!-- Product Detail -->

<style>
    .image-box {
        position: relative;
        width: 100%;
        max-width: 450px;
    }

    #productImage {
        width: 100%;
        display: block;
    }

    #lens {
        position: absolute;
        border: 1px solid #000;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.3);
        display: none;
        pointer-events: none;
        /* 🔥 important fix */
    }

    .product-image-wrapper {
        position: relative;
    }

    #zoomResult {
        position: absolute;
        left:34%;
        top: 0;
        /* adjust based on layout */
        width: 750px;
        height: 310px;
        border: 1px solid #ddd;
        background-repeat: no-repeat;
        display: none;
        z-index: 9999;

        background-color: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>
<div class="container mb-5" style="margin-top:220px;">
    <div class="row g-4 position-relative">

        <!-- Product Images -->
        <div class="col-lg-4">
            <div class="card shadow-sm product-image-wrapper d-flex">
                <div class="image-box position-relative">
                    <img id="productImage"
                        src="{{ asset('storage/products/'.$product->product_images[0]) }}"
                        class="img-fluid product-main-img"
                        alt="{{ $product->name }}">

                    <div id="lens"></div>

                </div>
            </div>
        </div>
        <!-- MOVE HERE -->
        <div id="zoomResult"></div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h3 class="fw-bold mt-3">{{ $product->name }}</h3>
                    <p class="text-muted mb-2">{{ $product->category->name ?? 'N/A' }}</p>

                    <!-- Unit -->
                    <div class="product-unit mb-2">
                        {{ rtrim(rtrim(number_format($product->unit_value,2),'0'),'.') }}
                        {{ Str::title(optional($product->unit)->name) }}
                    </div>
                    <!-- Price -->
                    @if(($product->available_stock ?? 0) > 0)
                    <div class="mb-2">

                        @if($product->sale)
                        {{-- SALE PRICE --}}
                        <span class="text-muted text-decoration-line-through">
                            ₹{{ number_format($product->sale->mrp, 0) }}
                        </span>

                        <span class="fs-4 fw-bold text-success ms-2">
                            ₹{{ number_format($product->sale->sale_price, 0) }}
                        </span>

                        @else
                        {{-- NORMAL PRICE --}}
                        @if($product->mrp > $product->final_price)
                        <span class="text-muted text-decoration-line-through">
                            ₹{{ number_format($product->mrp, 0) }}
                        </span>
                        @endif

                        <span class="fs-4 fw-bold text-success ms-2">
                            ₹{{ number_format($product->final_price, 0) }}
                        </span>
                        @endif

                    </div>
                    @endif

                    <!-- Discount -->
                    @if($product->mrp > $product->final_price)
                    @php
                    $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                    @endphp
                    <div class="offer-badge">{{ $discount }}% OFF</div>
                    @endif


                    <!-- Add To Cart Form -->
                    <form action="{{ route('add_cart') }}" method="POST" class="add-to-cart-form d-flex align-items-center gap-3 flex-wrap">
                        @csrf

                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" id="available-stock" value="{{ $availableStock }}">

                        @php
                        $cartQty = $cartItems[$product->id]->qty ?? 0;
                        @endphp

                        {{-- Warehouse not selected --}}
                        @if(!session('dc_warehouse_id'))

                        <button type="button" class="btn btn-secondary" disabled>
                            Check Availability
                        </button>

                        @else

                        {{-- Add to cart button component --}}
                        @include('website.partials.add-to-cart-btn', [
                        'product' => $product,
                        'cartItems' => $cartItems
                        ])

                        @endif

                    </form>

                    <hr>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link active"
                                data-bs-toggle="tab"
                                data-bs-target="#description">
                                Description
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link"
                                data-bs-toggle="tab"
                                data-bs-target="#reviews">
                                Reviews
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">

                        <div class="tab-pane fade show active" id="description">
                            <p>{{ $product->description }}</p>
                        </div>

                        <div class="tab-pane fade" id="reviews">
                            <p class="text-muted">No reviews available.</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mt-3">
        <h3 class="fw-bold mb-3">Similar products</h3>

        @foreach($relatedProducts as $related)
        <div class="col-md-6 col-lg-3 product-slide-item">
            <div class="rounded position-relative fruite-item display: inline-block;">

                {{-- DISCOUNT --}}
                @if($related->mrp > $related->final_price)
                @php
                $discount = round((($related->mrp - $related->final_price) / $related->mrp) * 100);
                @endphp
                <div class="offer-badge">{{ $discount }}% OFF</div>
                @endif

                @php
                $images = $related->product_images;
                $image = $images[0] ?? null;
                @endphp

                <div class="fruite-img">
                    <a href="{{ route('productdetails', $related->id) }}">
                        <img src="{{ $image ? asset('storage/products/'.$image) : asset('website/img/no-image.png') }}"
                            class="img-fluid w-100 rounded-top"
                            alt="{{ $related->name }}"
                            style="height: 150px; object-fit: cover;">
                    </a>
                </div>

                <div class="p-4 border border-top-0">
                    <form action="{{ route('add_cart') }}" method="POST" class="add-cart-form" onsubmit="return false;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $related->id }}">

                        <h6 class="product-title text-center">
                            {{ Str::limit(Str::title($related->name), 40) }}
                        </h6>

                        <p class="product-unit">
                            {{ rtrim(rtrim(number_format($related->unit_value, 2), '0'), '.') }}
                            {{ Str::title(optional($related->unit)->name) }}
                        </p>

                        <div class="price-row">
                            @if(($product->available_stock ?? 0) > 0)
                            <div class="price-box">

                                @if(isset($related->sale) && $related->sale)
                                {{-- SALE PRICE --}}
                                <span class="price-new">
                                    ₹{{ number_format($related->sale->sale_price, 0) }}
                                </span><br>

                                <span class="price-old">
                                    ₹{{ number_format($related->sale->mrp, 0) }}
                                </span>

                                @else
                                {{-- NORMAL PRICE --}}
                                <span class="price-new">
                                    ₹{{ number_format($related->final_price, 0) }}
                                </span><br>

                                <span class="price-old">
                                    ₹{{ number_format($related->mrp, 0) }}
                                </span>
                                @endif

                            </div>
                            @endif
                            @include('website.partials.add-to-cart-btn', ['product' => $related])
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    </div>

</div>
<div id="custom-alert-container"></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const img = document.getElementById("productImage");
    const lens = document.getElementById("lens");
    const result = document.getElementById("zoomResult");

    if (img) {

        img.addEventListener("mouseenter", () => {
            lens.style.display = "block";
            result.style.display = "block";
            result.style.backgroundImage = `url('${img.src}')`;
        });

        img.addEventListener("mouseleave", () => {
            lens.style.display = "none";
            result.style.display = "none";
        });

        img.addEventListener("mousemove", moveLens);
        lens.addEventListener("mousemove", moveLens);

        function moveLens(e) {
            e.preventDefault();

            const rect = img.getBoundingClientRect();

            let x = e.clientX - rect.left;
            let y = e.clientY - rect.top;

            const displayWidth = rect.width;
            const displayHeight = rect.height;

            const ratioX = img.naturalWidth / displayWidth;
            const ratioY = img.naturalHeight / displayHeight;

            // 👉 CENTER FIX (main issue solved here)
            let lensX = x - (lens.offsetWidth / 2);
            let lensY = y - (lens.offsetHeight / 2);

            // 👉 boundaries
            lensX = Math.max(0, Math.min(lensX, displayWidth - lens.offsetWidth));
            lensY = Math.max(0, Math.min(lensY, displayHeight - lens.offsetHeight));

            lens.style.left = lensX + "px";
            lens.style.top = lensY + "px";

            // 👉 ZOOM SCALE (important fix)
            const zoomLevel = 2; // 🔥 change this (2 = normal, 3 = more zoom)

            result.style.backgroundSize =
                (img.naturalWidth * zoomLevel) + "px " +
                (img.naturalHeight * zoomLevel) + "px";

            result.style.backgroundPosition =
                "-" + (lensX * ratioX * zoomLevel) + "px -" +
                (lensY * ratioY * zoomLevel) + "px";
        }
    }
</script>
<script>
    $(document).ready(function() {

        let availableStock = parseInt($('#available-stock').val()) || 0;

        // Quantity +
        $(document).on('click', '.qty-plus', function() {

            let form = $(this).closest('form');
            let input = form.find('.qty-input');
            let stockMsg = form.find('.stock-msg');

            let current = parseInt(input.val()) || 1;

            if (current >= availableStock) {
                stockMsg.removeClass('d-none');
                return;
            }

            stockMsg.addClass('d-none');
            input.val(current + 1);
        });

        // Quantity -
        $(document).on('click', '.qty-minus', function() {

            let form = $(this).closest('form');
            let input = form.find('.qty-input');
            let stockMsg = form.find('.stock-msg');

            let current = parseInt(input.val()) || 1;

            if (current > 1) {
                input.val(current - 1);
                stockMsg.addClass('d-none');
            }
        });

        // Manual typing validation
        $(document).on('input', '.qty-input', function() {

            let form = $(this).closest('form');
            let stockMsg = form.find('.stock-msg');

            let val = parseInt($(this).val()) || 1;

            if (val > availableStock) {
                $(this).val(availableStock);
                stockMsg.removeClass('d-none');
            } else if (val < 1) {
                $(this).val(1);
            } else {
                stockMsg.addClass('d-none');
            }
        });

        // Add to cart AJAX
        $(document).on('submit', '.add-to-cart-form', function(e) {

            e.preventDefault();

            let form = $(this);
            let qty = parseInt(form.find('input[name="qty"]').val()) || 1;
            let stockMsg = $('.stock-msg');

            if (qty > availableStock) {
                stockMsg.removeClass('d-none');
                return;
            }

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {

                    stockMsg.addClass('d-none');

                    if (res.cart_count > 0) {
                        $('#cart-count').text(res.cart_count).show();
                    } else {
                        $('#cart-count').hide();
                    }

                    alert('Product added to cart!');
                },
                error: function(xhr) {

                    if (xhr.status === 422 && xhr.responseJSON?.message) {
                        stockMsg.text(xhr.responseJSON.message)
                            .removeClass('d-none');
                    } else {
                        alert('Something went wrong!');
                    }
                }
            });
        });

    });
</script>



@endsection