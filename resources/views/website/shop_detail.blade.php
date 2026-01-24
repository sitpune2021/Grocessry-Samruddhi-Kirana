@extends('website.layout')

@section('title', $product->name)

@section('content')




<!-- Similar products -->
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

    <div class="row g-3 mt-3">
        <h3 class="fw-bold mb-3">Similar products</h3>

        @foreach($relatedProducts as $related)
        <div class="col-6 col-sm-4 col-md-2"> <!-- 6 cards per row on large screens -->

            <div class="rounded position-relative fruite-item">

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

                <div class="p-3 border border-top-0">

                    <div class="delivery-time mb-1 text-muted" style="font-size:12px;">Free delivery</div>

                    <form action="{{ route('add_cart') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $related->id }}">

                        <h6 class="product-title" style="font-size:14px; margin-bottom:4px;">
                            {{ Str::limit(Str::title($related->name), 40) }}
                        </h6>

                        <p class="product-unit" style="font-size:12px; margin-bottom:6px;">
                            {{ rtrim(rtrim(number_format($related->unit_value, 2), '0'), '.') }}
                            {{ Str::title(optional($related->unit)->name) }}
                        </p>

                        <div class="price-row d-flex justify-content-between align-items-center">
                            <div class="price-box" style="font-size:14px;">
                                <span class="price-new fw-bold">₹{{ number_format($related->final_price, 0) }}</span><br>
                                <span class="price-old text-muted" style="text-decoration:line-through; font-size:12px;">
                                    ₹{{ number_format($related->mrp, 0) }}
                                </span>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary">ADD</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endforeach
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