@extends('website.layout')

@section('title', 'Home')

@section('content')
<style>
    /* Product Slider Base */
    .product-slider {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 10px 5px;
    }

    .product-slide-item {
        flex: 0 0 180px;
    }



    /* Image */
    .product-sm-img img {
        width: 100%;
        height: 140px;
        object-fit: contain;
    }

    /* Title */
    .product-sm-title {
        font-size: 14px;
        font-weight: 600;
        margin: 6px 0;
        height: 40px;
        overflow: hidden;
    }

    /* Footer */
    .product-sm-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Arrows */
    .slider-arrow {
        position: absolute;
        top: 40%;
        background: #fff;
        border: none;
        font-size: 20px;
        padding: 5px 10px;
        z-index: 10;
    }

    /* 🔥 MOBILE FIX */
    @media (max-width: 576px) {
        .product-slide-item {
            flex: 0 0 140px;
            /* Smaller cards */
        }

        .product-sm-img img {
            height: 110px;
        }

        .product-sm-title {
            font-size: 13px;
            height: 36px;
        }

        .btn-add-sm {
            font-size: 12px;
            padding: 5px 8px;
        }
    }
</style>

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
        border: 1px solid #e9e7e7;
        border-radius: 12px;
        padding: 10px;
        background: #ffffff;
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
        background: #b0a5a5ff;
        color: #fff;
        border: none;
        width: 32px;
        height: 32px;
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


    /* CATEGORY CARD */


    /* HOVER */
    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
    }

    /* IMAGE WRAPPER */
    .category-img {
        width: 80%;
        aspect-ratio: 1 / 1;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* IMAGE FULL FIT */
    .category-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* 🔥 FULL FIT */
        border-radius: 50%;
    }


    .category-card:hover .category-img {
        transform: scale(1.05);
    }

    /* TITLE */
    .category-title {
        margin-top: 6px;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
        line-height: 1.2;
        color: #333;
    }

    .category-row {
        display: flex;
        flex-wrap: wrap;
    }

    .category-col {
        width: 10%;
        /* 🔥 10 in one row */
        padding: 8px;
    }

    /* RESPONSIVE FIX */
    @media (max-width: 1200px) {
        .category-col {
            width: 20%;
        }

        /* 5 per row */
    }

    @media (max-width: 768px) {
        .category-col {
            width: 25%;
        }

        /* 4 per row */
    }

    @media (max-width: 576px) {
        .category-col {
            width: 33.33%;
        }

        /* 3 per row */
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
                rgba(46, 43, 43, 0.26),
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

    .product-sm-card {
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

    /* categry box  */

    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #25D366;
        color: #fff;
        border-radius: 50%;
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        text-decoration: none;
    }

    .whatsapp-float:hover {
        background: #1ebe5d;
        color: #fff;
    }
</style>

<body>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- slider header Start -->
    <div class="container my-3">
        <div class="row">
            <div class="col-12">

                <div id="carouselId" class="carousel slide carousel-fade" data-bs-ride="carousel" style="padding-top: 70px;">

                    <div class="carousel-inner rounded-4 overflow-hidden" style="margin-top: 25px;">

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
                    <button class="carousel-control-prev mt-5" type="button" data-bs-target="#carouselId" data-bs-slide="prev ">
                        <span class="carousel-control-prev-icon"></span>
                    </button>

                    <button class="carousel-control-next mt-5" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- category main -->
    <div class="container py-4">
        <div class="row g-3 category-row">

            @foreach($categoriestop as $category)
            <div class="category-col">

                <a href="{{ route('website.category-products', $category->slug) }}"
                    class="category-card text-center">

                    <div class="category-img">
                        <img src="{{ $category->image
                        ? asset('storage/categories/'.$category->image)
                        : asset('img/default.png') }}"
                            alt="{{ $category->name }}">
                    </div>

                    <p class="category-title">{{ $category->name }}</p>

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

                                    {{-- DISCOUNT --}}
                                    @if($product->mrp > $product->final_price)
                                    @php
                                    $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                                    @endphp
                                    <div class="offer-badge">{{ $discount }}% OFF</div>
                                    @endif

                                    @php
                                    $images = $product->product_images;
                                    $image = $images[0] ?? null;
                                    @endphp

                                    <div class="fruite-img">
                                        <a href="{{ route('productdetails', $product->id) }}">
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
                                        </a>
                                    </div>

                                    <div class="p-4 border border-top-0">

                                        <div class="delivery-time mb-1">Free delivery</div>

                                        <form action="{{ route('add_cart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            <h6 class="product-title">
                                                {{ Str::limit(Str::title($product->name), 40) }}
                                            </h6>

                                            <p class="product-unit">
                                                {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                                                {{ Str::title(optional($product->unit)->name) }}
                                            </p>

                                            <div class="price-row">
                                                <div class="price-box">
                                                    <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span><br>
                                                    <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                                                </div>

                                                <button type="submit" class="btn-add-sm">ADD</button>
                                            </div>

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

                                    {{-- DISCOUNT --}}
                                    @if($product->mrp > $product->final_price)
                                    @php
                                    $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                                    @endphp
                                    <div class="offer-badge">{{ $discount }}% OFF</div>
                                    @endif

                                    @php
                                    $images = $product->product_images;
                                    $image = $images[0] ?? null;
                                    @endphp

                                    <div class="fruite-img">
                                        <a href="{{ route('productdetails', $product->id) }}">
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
                                        </a>
                                    </div>

                                    <div class="p-4 border border-top-0">

                                        <div class="delivery-time mb-1">Free delivery</div>

                                        <form action="{{ route('add_cart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                                            <h6 class="product-title">
                                                {{ Str::limit(Str::title($product->name), 40) }}
                                            </h6>

                                            <p class="product-unit">
                                                {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                                                {{ Str::title(optional($product->unit)->name) }}
                                            </p>

                                            <div class="price-row">
                                                <div class="price-box">
                                                    <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span><br>
                                                    <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                                                </div>

                                                <button type="submit" class="btn-add-sm">ADD</button>
                                            </div>

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

                        {{-- DISCOUNT --}}
                        @if($product->mrp > $product->final_price)
                        @php
                        $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                        @endphp
                        <div class="offer-badge">{{ $discount }}% OFF</div>
                        @endif

                        <a href="{{ route('productdetails', $product->id) }}">
                            <div class="product-sm-img">
                                <img src="{{ $image 
                                ? asset('storage/products/'.$image) 
                                : asset('website/img/no-image.png') }}">
                            </div>

                            <div class="product-sm-title">
                                {{ Str::limit(Str::title($product->name), 35) }}
                            </div>

                            <div class="product-unit">
                                {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                                {{ Str::title(optional($product->unit)->name) }}
                            </div>
                        </a>

                        <div class="product-sm-footer">
                            <div>
                                <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span><br>
                                <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                            </div>

                            <form action="{{ route('add_cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn-add-sm">ADD</button>
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

                        {{-- DISCOUNT --}}
                        @if($product->mrp > $product->final_price)
                        @php
                        $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                        @endphp
                        <div class="offer-badge">{{ $discount }}% OFF</div>
                        @endif

                        <a href="{{ route('productdetails', $product->id) }}">
                            <div class="product-sm-img">
                                <img src="{{ $image 
                        ? asset('storage/products/'.$image) 
                        : asset('website/img/no-image.png') }}">
                            </div>

                            <div class="product-sm-title">
                                {{ Str::limit(Str::title($product->name), 35) }}
                            </div>

                            <div class="product-unit">
                                {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                                {{ Str::title(optional($product->unit)->name) }}
                            </div>
                        </a>

                        <div class="product-sm-footer">
                            <div>
                                <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span><br>
                                <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                            </div>

                            <form action="{{ route('add_cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn-add-sm">ADD</button>
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