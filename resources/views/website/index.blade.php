@extends('website.layout')

@section('title', 'Home')


@section('content')


<!-- Featurs Section End -->

<style>
    .pagination {
        justify-content: center !important;
        flex-wrap: wrap;
    }

    .pagination .page-item {
        display: inline-flex !important;
    }

    .pagination .page-link {
        padding: 4px 8px;
        font-size: 12px;
        line-height: 1.2;
        min-width: 30px;
        height: 30px;
        border-radius: 4px;
    }


    /* Small product card () */
    .product-sm-card {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 10px;
        background: #fff;
        height: 100%;
        transition: box-shadow 0.2s ease;
    }

    .product-sm-card:hover {
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .product-sm-img {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-sm-img img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .product-sm-title {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
        margin: 8px 0 4px;
    }

    .product-sm-weight {
        font-size: 12px;
        color: #777;
    }

    .product-sm-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
    }

    .product-sm-price {
        font-size: 14px;
        font-weight: 700;
    }

    .btn-add-sm {
        border: 1px solid #28a745;
        color: #28a745;
        background: #fff;
        padding: 3px 14px;
        font-size: 13px;
        border-radius: 8px;
    }

    .btn-add-sm:hover {
        background: #28a745;
        color: #fff;
    }

    .counter:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
    }

    .hover-shadow {
        transition: all 0.3s ease;
    }

    .service-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
    }

    html,
    body {
        max-width: 100%;
        overflow-x: hidden;
    }

    .carousel,
    .carousel-inner,
    .carousel-item {
        overflow: hidden;
    }


    img {
        max-width: 100%;
        height: auto;
    }

    .product-slider {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding-bottom: 10px;
    }

    /* hide scrollbar */
    .product-slider::-webkit-scrollbar {
        display: none;
    }

    .product-slider {
        scrollbar-width: none;
    }

    /* 6 cards per row */
    .product-slide-item {
        flex: 0 0 calc(100% / 6 - 10px);
    }

    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #000;
        color: #fff;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        font-size: 22px;
        cursor: pointer;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.85;
    }

    .slider-arrow.left {
        left: -20px;
    }

    .slider-arrow.right {
        right: -20px;
    }

    .slider-arrow:hover {
        opacity: 1;
    }

    /* hide arrows on mobile */
    @media (max-width: 768px) {
        .slider-arrow {
            display: none;
        }
    }


    /* categery model  */
    .category-card {
        display: block;
        text-decoration: none;
        color: #000;
    }

    .category-img {
        background: #eef5ff;
        border-radius: 16px;
        padding: 14px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .category-img img {
        max-width: 100%;
        max-height: 90px;
        object-fit: contain;
    }

    .category-title {
        margin-top: 8px;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.3;
    }

    .category-card:hover .category-img {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .hero-banner {
        height: 380px;
        width: 100%;
        position: relative;
        overflow: hidden;
        /* 🔴 KEY LINE */
        border-radius: 16px;
        background: #000;

    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;

        object-position: center;
        display: block;
    }

    /* Overlay safe */
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right,
                rgba(0, 0, 0, 0.55),
                rgba(0, 0, 0, 0.1));
        z-index: 1;
    }


    .carousel-control-prev,
    .carousel-control-next {
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: rgba(0, 0, 0, 0.45);
        border-radius: 50%;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-size: 18px 18px;
    }


    /* Mobile */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 22px;
            margin-top: 20px;
        }

        .hero-btn {
            margin-bottom: 20px;
        }
    }

    /* ===== BETTER TAB SEARCH UI ===== */

    .nav-pills {
        gap: 10px;
    }

    /* TAB BUTTON */
    .nav-pills .nav-link,
    .nav-pills a {
        background: #f5f5f5 !important;
        border-radius: 20px !important;
        padding: 8px 18px !important;
        transition: all 0.25s ease;
        border: 1px solid #e0e0e0;
    }

    /* TAB TEXT */
    .nav-pills span {
        font-size: 14px;
        font-weight: 600;
    }

    /* ACTIVE TAB */
    .nav-pills .active {
        background: #28a745 !important;
        border-color: #28a745 !important;
    }

    .nav-pills .active span {
        color: #fff !important;
    }

    /* ===== CATEGORY SEARCH DROPDOWN ===== */

    form .form-select {
        height: 44px;
        border-radius: 10px;
        border: 1px solid #dcdcdc;
        font-size: 14px;
        font-weight: 600;
        padding-left: 14px;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    /* Hover */
    form .form-select:hover {
        border-color: #28a745;
    }

    /* Focus */
    form .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.25);
    }

    /* Optional container look */
    form .col-md-4 {
        position: relative;
    }

    /* Mobile full width */
    @media (max-width: 768px) {
        form .col-md-4 {
            width: 100%;
        }
    }

    /* ALIGN RIGHT NICELY */
    @media (min-width: 992px) {
        .nav-pills {
            justify-content: flex-end;
        }
    }
</style>

<body>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Hero Start -->

    <div class="container my-5">
        <div class="row">
            <div class="col-12">

                <div id="carouselId" class="carousel slide carousel-fade" data-bs-ride="carousel">

                    <div class="carousel-inner rounded-4 overflow-hidden" style="margin-top: 75px;">

                        @foreach($banners as $key => $banner)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <div class="hero-banner position-relative">
                                <img src="{{ asset('storage/'.$banner->image) }}"
                                    alt="{{ $banner->name }}"
                                    class="hero-img">
                                <div class="hero-overlay"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row g-4">

            @foreach($categoriestop as $category)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="{{ route('home', ['category_id' => $category->id]) }}"
                    class="category-card text-center">

                    <div class="category-img">
                        <img src="{{ asset($category->image ?? 'img/default.png') }}"
                            alt="{{ $category->name }}">
                    </div>

                    <p class="category-title">
                        {{ $category->name }}
                    </p>

                </a>
            </div>
            @endforeach

        </div>
    </div>

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite">
        <div class="container">
            <div class="tab-class text-center">

                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h4>Our Organic Products</h4>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill {{ empty($categoryId) ? 'active' : '' }}"
                                    data-bs-toggle="pill" href="#tab-1">
                                    <span class="text-dark" style="width: 130px;">All Products</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="d-flex py-2 m-2 bg-light rounded-pill {{ !empty($categoryId) ? 'active' : '' }}"
                                    data-bs-toggle="pill" href="#tab-2">
                                    <span class="text-dark" style="width: 130px;">Category Search</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">

                    <!-- TAB 1 : ALL PRODUCTS -->
                    <div id="tab-1" class="tab-pane fade show {{ empty($categoryId) ? 'active' : '' }}">
                        <div class="row g-4">
                            @foreach($allProducts as $product)
                            <div class="col-md-6 col-lg-3">
                                <div class="rounded position-relative fruite-item">
                                    @php
                                    $images = $product->product_images;
                                    $image = $images[0] ?? null;
                                    @endphp

                                    <div class="fruite-img">
                                        @if($image)
                                        <img
                                            src="{{ asset('storage/products/'.$image) }}"
                                            class="img-fluid w-100 rounded-top"
                                            alt="{{ $product->name }}"
                                            style="height: 200px; object-fit: cover;">
                                        @else
                                        <img
                                            src="{{ asset('website/img/no-image.png') }}"
                                            class="img-fluid w-100 rounded-top"
                                            alt="No Image"
                                            style="height: 200px; object-fit: cover;">
                                        @endif
                                    </div>

                                    <div class="p-4 border border-top-0  ">

                                        <form action="{{ route('add_cart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <h4>{{ $product->name }}</h4>
                                            <p>₹ {{ $product->mrp }}</p>
                                            <button type="submit" class="btn-add-sm">Add to cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex flex-column align-items-end">
                            {{-- Pagination --}}
                            {{ $allProducts->onEachSide(0)->links('pagination::bootstrap-5') }}
                        </div>

                    </div>

                    <!-- TAB 2 : CATEGORY SEARCH -->
                    <div id="tab-2" class="tab-pane fade show {{ !empty($categoryId) ? 'active' : '' }}">

                        <form method="GET" action="{{ route('home') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $categoryId == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>

                        <div class="row g-4">
                            @forelse($categoryProducts as $product)
                            <div class="col-md-2 col-lg-2">
                                <div class="rounded position-relative fruite-item">
                                    @php
                                    $images = $product->product_images; // Already array
                                    $image = $images[0] ?? null;
                                    @endphp

                                    <div class="fruite-img">
                                        @if($image)
                                        <img
                                            src="{{ asset('storage/products/'.$image) }}"
                                            class="img-fluid w-100 rounded-top"
                                            alt="{{ $product->name }}"
                                            style="height: 200px; object-fit: cover;">
                                        @else
                                        <img
                                            src="{{ asset('website/img/no-image.png') }}"
                                            class="img-fluid w-100 rounded-top"
                                            alt="No Image"
                                            style="height: 200px; object-fit: cover;">
                                        @endif
                                    </div>

                                    <div class="p-4 border border-top-0  ">

                                        <form action="{{ route('add_cart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <h4>{{ $product->name }}</h4>
                                            <p>₹ {{ $product->mrp }}</p>
                                            <button type="submit" class="btn-add-sm">Add to cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center">No products found</p>
                            @endforelse
                        </div>

                        <div class="mt-4 d-flex flex-column align-items-end">
                            {{-- Pagination --}}
                            {{ $categoryProducts->onEachSide(0)->links('pagination::bootstrap-5') }}


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- slide product  -->
    <div class="container py-2">
        @foreach($cate as $category)
        @if($category->products->count())
        <div class="row p-3">
            <div class="col text-start">
                <h4 class="fw-bold text-dark">{{ $category->name }}</h4>
            </div>
        </div>

        <div class="position-relative product-slider-wrapper">
            <button class="slider-arrow left">&#10094;</button>

            <div class="product-slider">
                @foreach($category->products as $product)
                @php
                $image = $product->product_images[0] ?? null;
                @endphp
                <div class="product-slide-item">
                    <div class="product-sm-card">
                        <div class="product-sm-img">
                            <img src="{{ $image 
                                    ? asset('storage/products/'.$image) 
                                    : asset('website/img/no-image.png') }}">
                        </div>
                        <div class="product-sm-title">
                            {{ Str::limit($product->name, 35) }}
                        </div>
                        <div class="product-sm-footer">

                            <form action="{{ route('add_cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <p>₹ {{ $product->mrp }}</p>
                                <button type="submit" class="btn-add-sm">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <button class="slider-arrow right">&#10095;</button>
        </div>
        @endif
        @endforeach

    </div>

    <div class="container py-2">

        <div class="row p-3">
            <div class="col text-start">
                <h4 class="fw-bold text-dark">Latest Products</h4>
            </div>
        </div>

        <div class="position-relative product-slider-wrapper">
            <button class="slider-arrow left">&#10094;</button>

            <div class="product-slider">
                @foreach($latestPro as $product)
                @php
                $image = $product->product_images[0] ?? null;
                @endphp

                <div class="product-slide-item">
                    <div class="product-sm-card">
                        <div class="product-sm-img">
                            <img src="{{ $image 
                                ? asset('storage/products/'.$image) 
                                : asset('website/img/no-image.png') }}">
                        </div>

                        <div class="product-sm-title">
                            {{ Str::limit($product->name, 35) }}
                        </div>

                        <div class="product-sm-footer">
                            <form action="{{ route('add_cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <p>₹ {{ $product->mrp }}</p>
                                <button type="submit" class="btn-add-sm">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <button class="slider-arrow right">&#10095;</button>
        </div>

    </div>


    <!-- Featurs Section Start -->
    <div class="container-fluid featurs">
        <div class="container py-4 p">
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-4 mx-auto" style="width:60px; height:60px; line-height:60px;">
                            <i class="fas fa-car-side fa-2x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Free Shipping</h5>
                            <p class="mb-0">Free on order over $300</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-4 mx-auto" style="width:60px; height:60px; line-height:60px;">
                            <i class="fas fa-user-shield fa-2x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>Security Payment</h5>
                            <p class="mb-0">100% security payment</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-4 mx-auto" style="width:60px; height:60px; line-height:60px;">
                            <i class="fas fa-exchange-alt fa-2x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>30 Day Return</h5>
                            <p class="mb-0">30 day money guarantee</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="featurs-item text-center rounded bg-light p-4">
                        <div class="featurs-icon btn-square rounded-circle bg-secondary mb-4 mx-auto" style="width:60px; height:60px; line-height:60px;">
                            <i class="fa fa-phone-alt fa-2x text-white"></i>
                        </div>
                        <div class="featurs-content text-center">
                            <h5>24/7 Support</h5>
                            <p class="mb-0">Support every time fast</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll(".product-slider-wrapper").forEach(wrapper => {

            const slider = wrapper.querySelector(".product-slider");
            const left = wrapper.querySelector(".slider-arrow.left");
            const right = wrapper.querySelector(".slider-arrow.right");

            if (!slider || !left || !right) return;

            const slideWidth = slider.clientWidth * 0.9;

            right.addEventListener("click", () => {
                slider.scrollBy({
                    left: slideWidth,
                    behavior: "smooth"
                });
            });

            left.addEventListener("click", () => {
                slider.scrollBy({
                    left: -slideWidth,
                    behavior: "smooth"
                });
            });

        });

    });
</script>





@endsection