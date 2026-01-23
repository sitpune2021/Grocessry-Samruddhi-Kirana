@extends('website.layout')

@section('title', $product->name)

@section('content')

<style>
    /* Product image hover effect */
    .product-image-wrapper {
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .product-main-img {
        transition: transform 0.4s ease;
    }

    .product-image-wrapper:hover .product-main-img {
        transform: scale(1.08);
    }

    .product-hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-hover-overlay span {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        border: 2px solid #fff;
        padding: 8px 18px;
        border-radius: 30px;
    }

    .product-image-wrapper:hover .product-hover-overlay {
        opacity: 1;
    }

    /* Related Products Card */
    .related-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        padding: 6px;
        background-color: #fff;
    }

    .related-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .related-card img {
        height: 150px;
        object-fit: contain;
        transition: transform 0.3s;
    }

    .related-card:hover img {
        transform: scale(1.05);
    }

    .related-card .card-body {
        padding: 8px;
    }

    .related-card h6 {
        font-size: 13px;
        margin-bottom: 4px;
    }

    .related-card p {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .related-card .btn {
        font-size: 13px;
        padding: 6px 10px;
    }

    .offer-badge {
        background: #253bdf;
        color: #fff;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-block;
        margin-left: 8px;
        vertical-align: middle;
    }



    .qty-box button {
        width: 32px;
        height: 32px;
        padding: 0;
    }

    .qty-input {
        height: 32px;
        font-size: 14px;
    }

    /* simi pro */

    .related-card img {
        height: 120px;
        object-fit: contain;
    }

    .related-card h6 {
        font-size: 13px;
        line-height: 1.2;
    }

    .related-card .btn {
        font-size: 12px;
        padding: 5px 10px;
    }
</style>

<style>
    .product-card {
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        transition: 0.3s;
        background: #fff;
    }

    .product-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .discount-ribbon {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #2563eb;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .delivery-time {
        font-size: 12px;
        color: #444;
        margin-top: 6px;
    }

    .product-img {
        height: 150px;
        object-fit: contain;
        padding: 10px;
    }

    .price {
        font-weight: 700;
        font-size: 16px;
    }

    .mrp {
        font-size: 13px;
        color: #888;
        text-decoration: line-through;
    }

    .add-btn {
        border: 1px solid #22c55e;
        color: #22c55e;
        font-weight: 700;
        border-radius: 8px;
        padding: 4px 14px;
        background: #fff;
    }

    .add-btn:hover {
        background: #22c55e;
        color: #fff;
    }
</style>


<!-- Page Header -->
<div class="container-fluid page-header py-4 mb-5 bg-dark">
    <h1 class="text-center text-white display-6">Product Details</h1>
</div>

<!-- Product Detail -->
<div class="container mb-5">
    <div class="row g-4">

        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="card shadow-sm product-image-wrapper">
                <img src="{{ asset('storage/products/'.$product->product_images[0]) }}"
                    class="img-fluid product-main-img" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h3 class="fw-bold mt-3">{{ $product->name }}</h3>
                    <p class="text-muted mb-2">Category: {{ $product->category->name ?? 'N/A' }}</p>

                    <div class="mb-2">
                        <span class="fs-4 fw-bold text-success">
                            ₹{{ number_format($product->final_price, 0) }}
                        </span>

                        @if($product->mrp > $product->final_price)
                        <span class="text-muted text-decoration-line-through ms-2">
                            ₹{{ number_format($product->mrp, 0) }}
                        </span>
                        @endif
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

                        <div class="qty-box d-inline-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">-</button>

                            <input type="number" name="qty" value="1" min="1"
                                class="form-control text-center qty-input" style="width:60px">

                            <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">+</button>
                        </div>

                        <button class="btn btn-primary rounded-pill px-4">
                            <i class="fa fa-shopping-bag me-2"></i>Add to Cart
                        </button>
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

    <!-- Similar products -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">Similar products</h3>

        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">

                <div class="card h-100 shadow-sm position-relative related-card">

                    <a href="{{ route('productdetails', $related->id) }}">

                        {{-- Category Badge --}}
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                            {{ $related->category->name ?? 'Category' }}
                        </span>

                        {{-- Product Image --}}
                        <img src="{{ asset('storage/products/'.$related->product_images[0]) }}"
                            class="card-img-top"
                            alt="{{ $related->name }}">

                        <div class="card-body d-flex flex-column">

                            <h6 class="fw-bold mb-1">{{ $related->name }}</h6>

                            {{-- PRICE + DISCOUNT --}}
                            <div class="d-flex align-items-center gap-2">

                                {{-- Final Price --}}
                                <span class="fw-bold text-primary">
                                    ₹{{ $related->final_price ?? $related->mrp }}
                                </span>

                                {{-- MRP + Discount --}}
                                @if($related->mrp > ($related->final_price ?? $related->mrp))
                                @php
                                $discount = round((($related->mrp - $related->final_price) / $related->mrp) * 100);
                                @endphp

                                <small class="text-muted text-decoration-line-through">
                                    ₹{{ $related->mrp }}
                                </small>

                                <span class="badge bg-danger">
                                    {{ $discount }}% OFF
                                </span>
                                @endif

                            </div>

                        </div>
                    </a>

                    {{-- ADD TO CART --}}
                    <form action="{{ route('add_cart') }}"
                        method="POST"
                        class="add-to-cart-form px-2 pb-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $related->id }}">

                        <button class="btn btn-primary w-100 rounded-pill">
                            <i class="fa fa-shopping-bag me-2"></i>Add to Cart
                        </button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        // Quantity increment
        $(document).on('click', '.qty-plus', function() {
            let input = $(this).siblings('.qty-input');
            let current = parseInt(input.val()) || 1;
            input.val(current + 1);
        });

        // Quantity decrement
        $(document).on('click', '.qty-minus', function() {
            let input = $(this).siblings('.qty-input');
            let current = parseInt(input.val()) || 1;
            if (current > 1) {
                input.val(current - 1);
            }
        });

        // Add to cart AJAX for all forms
        $(document).on('submit', '.add-to-cart-form', function(e) {
            e.preventDefault();

            let form = $(this);
            let productId = form.find('input[name="product_id"]').val();
            let qty = parseInt(form.find('input[name="qty"]').val()) || 1;

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                    qty: qty
                },
                success: function(res) {
                    // Update cart count dynamically
                    if (res.cart_count > 0) {
                        $('#cart-count').text(res.cart_count).show();
                    } else {
                        $('#cart-count').hide();
                    }
                    alert('Product added to cart!');
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        });

    });
</script>

@endsection