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
        /* less padding */
        background-color: #fff;
    }

    .related-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    /* Card Image */
    .related-card img {
        height: 150px;
        /* smaller image */
        object-fit: contain;
        transition: transform 0.3s;
    }

    .related-card:hover img {
        transform: scale(1.05);
    }

    /* Card Body */
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

    /* Add to cart button */
    .related-card .btn {
        font-size: 13px;
        padding: 6px 10px;
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
                    <h3 class="fw-bold">{{ $product->name }}</h3>
                    <p class="text-muted mb-2">Category: {{ $product->category->name ?? 'N/A' }}</p>

                    <h4 class="text-primary   mb-3">₹{{ $product->mrp }}</h4>

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

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">

                <div class="card h-100 shadow-sm position-relative related-card">
                    <a href="{{ route('productdetails', $related->id) }}">
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                            {{ $related->category->name ?? 'Category' }}
                        </span>

                        <img src="{{ asset('storage/products/'.$related->product_images[0]) }}"
                            class="card-img-top" alt="{{ $related->name }}">

                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold">{{ $related->name }}</h6>
                            <p class="text-primary fw-bold mb-3">₹{{ $related->mrp }}</p>

                            <form action="{{ route('add_cart') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $related->id }}">
                                <button class="btn btn-outline-primary w-50 rounded-pill">
                                    <i class="fa fa-shopping-bag me-2"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                    </a>
                </div>

            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.qty-plus', function() {
        let input = $(this).siblings('.qty-input');
        input.val(parseInt(input.val()) + 1);
    });

    $(document).on('click', '.qty-minus', function() {
        let input = $(this).siblings('.qty-input');
        let val = parseInt(input.val());
        if (val > 1) input.val(val - 1);
    });
</script>


@endsection