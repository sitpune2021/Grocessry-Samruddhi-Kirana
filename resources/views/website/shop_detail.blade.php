@extends('website.layout')

@section('title', $product->name)

@section('content')

<!-- Similar products -->
<!-- <div class="container-fluid page-header py-4 mb-5 bg-dark">
    <h1 class="text-center text-white display-6">Product Details</h1>
</div> -->

<!-- Product Detail -->
<div class="container mb-5" style="margin-top:140px;">
    <div class="row g-4">

        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="card shadow-sm product-image-wrapper">
                <img src="{{ asset('storage/products/'.$product->product_images[0]) }}"
                    class="img-fluid product-main-img" alt="{{ $product->name }}">
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h3 class="fw-bold mt-3">{{ $product->name }}</h3>
                    <p class="text-muted mb-2">{{ $product->category->name ?? 'N/A' }}</p>

                    <div class="mb-2">

                        @if($product->mrp > $product->final_price)
                        <span class="text-muted text-decoration-line-through ms-2">
                            ₹{{ number_format($product->mrp, 0) }}
                        </span>

                        @endif
                        <span class="fs-4 ms-2 fw-bold text-success">
                            ₹{{ number_format($product->final_price, 0) }}
                        </span>
                    </div>
                    {{-- DISCOUNT --}}
                    @if($product->mrp > $product->final_price)
                    @php
                    $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                    @endphp
                    <div class="offer-badge">{{ $discount }}% OFF</div>
                    @endif
                    <div class="product-unit">
                        {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                        {{ Str::title(optional($product->unit)->name) }}
                    </div>
                    <form action="{{ route('add_cart') }}" method="POST" class="d-flex align-items-center gap-3 flex-wrap">
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
                        <div class="qty-wrapper">
                            {{-- QTY CONTROLS --}}
                            <div class="qty-box {{ $cartQty > 0 ? '' : 'd-none' }}">
                                <button type="button" onclick="changeQty(this, -1)">−</button>

                                <span class="qty">{{ $cartQty > 0 ? $cartQty : 1 }}</span>

                                <button type="button" onclick="changeQty(this, 1)">+</button>
                            </div>

                        </div>
                        @endif
                    </form>

                    <!-- <button class="btn btn-primary rounded-pill px-4">
                            <i class="fa fa-shopping-bag me-2"></i>Add to Cart
                        </button> -->

                    @include('website.partials.add-to-cart-btn', ['product' => $product])
                    </form>

                    <hr>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button>
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
                            <div class="price-box">
                                <span class="price-new">₹{{ number_format($related->final_price, 0) }}</span><br>
                                <span class="price-old">₹{{ number_format($related->mrp, 0) }}</span>
                            </div>

                            @include('website.partials.add-to-cart-btn', ['product' => $product])
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
    $(document).ready(function() {

        let availableStock = parseInt($('#available-stock').val()) || 0;

        // Quantity +
        $(document).on('click', '.qty-plus', function() {

            let input = $('.qty-input');
            let stockMsg = $('.stock-msg');
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

            let input = $('.qty-input');
            let stockMsg = $('.stock-msg');
            let current = parseInt(input.val()) || 1;

            if (current > 1) {
                input.val(current - 1);
                stockMsg.addClass('d-none');
            }
        });

        // Manual typing validation
        $(document).on('input', '.qty-input', function() {

            let stockMsg = $('.stock-msg');
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