@extends('website.layout')

@section('title', 'Home')


@section('content')


<!-- Featurs Section End -->

<style>
    /* FORCE pagination to horizontal row */
    .pagination {
        justify-content: center !important;
        flex-wrap: wrap;
    }

    .pagination .page-item {
        display: inline-flex !important;
    }

    .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* /// */

    /* Small product card (Blinkit style) */
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

    .pagination {
        justify-content: center !important;
        flex-wrap: wrap;
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
</style>

<body>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Hero Start -->
    <div class="py-5 mb-5">

        <div class="row g-5 align-items-center">
            <div class="col-md-12 col-lg-7"><br><br><br><br><br><br>
                <h4 class="mb-3 text-secondary" style="padding: 0px 70px;">100% Organic Foods</h4>
                <h1 class="mb-5 text-primary" style="padding: 0px 70px;">Organic Veggies & Fruits Foods</h1>
                <div class="position-relative mx-auto" style="padding: 0px 70px;">
                    <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="number" placeholder="Search">
                    <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; right: 25%;">Submit Now</button>
                </div>
            </div>
            <!-- line change -->
            <div class="col-md-12 col-lg-7 px-3 px-lg-5">
                <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner" role="listbox"><br><br><br><br><br><br>

                        @foreach($banners as $key => $banner)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }} rounded">
                            <img
                                src="{{ asset('storage/'.$banner->image) }}"
                                class="img-fluid w-100 h-100 bg-secondary rounded"
                                alt="{{ $banner->name }}">
                            <a href="#" class="btn px-4 py-2 text-white rounded">
                                {{ $banner->name }}
                            </a>
                        </div>
                        @endforeach

                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->
    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">

                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Our Organic Products</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
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

                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <h4>{{ $product->name }}</h4>
                                        <p>₹ {{ $product->mrp }}</p>
                                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
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
                            <div class="col-md-6 col-lg-3">
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

                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                        <h4>{{ $product->name }}</h4>
                                        <p>₹ {{ $product->mrp }}</p>
                                        <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
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

                            {{-- Showing result text --}}
                            <!-- <div class="mt-2 text-muted">
                                    Showing {{ $categoryProducts->firstItem() }}
                                    to {{ $categoryProducts->lastItem() }}
                                    of {{ $categoryProducts->total() }} results
                                </div> -->
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>


    <div class="container py-4">
        <div class="row g-4">

            <!-- CATEGORY ITEM -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/paan.png" alt="Paan Corner">
                    </div>
                    <p class="category-title">Paan<br>Corner</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/dairy.png" alt="">
                    </div>
                    <p class="category-title">Dairy, Bread<br>& Eggs</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/fruits.png">
                    </div>
                    <p class="category-title">Fruits &<br>Vegetables</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/drinks.png">
                    </div>
                    <p class="category-title">Cold Drinks<br>& Juices</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/snacks.png">
                    </div>
                    <p class="category-title">Snacks &<br>Munchies</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/breakfast.png">
                    </div>
                    <p class="category-title">Breakfast &<br>Instant Food</p>
                </a>
            </div>

            <!-- repeat same structure for all categories -->

        </div>
        <div class="row g-4">

            <!-- CATEGORY ITEM -->
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/paan.png" alt="Paan Corner">
                    </div>
                    <p class="category-title">Paan<br>Corner</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/dairy.png" alt="">
                    </div>
                    <p class="category-title">Dairy, Bread<br>& Eggs</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/fruits.png">
                    </div>
                    <p class="category-title">Fruits &<br>Vegetables</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/drinks.png">
                    </div>
                    <p class="category-title">Cold Drinks<br>& Juices</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/snacks.png">
                    </div>
                    <p class="category-title">Snacks &<br>Munchies</p>
                </a>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <a href="#" class="category-card text-center">
                    <div class="category-img">
                        <img src="img/breakfast.png">
                    </div>
                    <p class="category-title">Breakfast &<br>Instant Food</p>
                </a>
            </div>

            <!-- repeat same structure for all categories -->

        </div>
    </div>


    <!-- slide product  -->
    <div class="container py-2">
        @foreach($categories as $category)
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
                            <span class="price">₹{{ $product->mrp }}</span>
                            <button class="btn-add-sm">ADD</button>
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


    <!-- Featurs Start -->
    <!-- Service/Featured Products Start -->
    <div class="container-fluid service py-5 bg-light">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <!-- Service 1 -->
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="text-decoration-none">
                        <div class="service-item rounded overflow-hidden shadow-sm hover-scale">
                            <img src="{{ asset('website/img/featur-1.jpg') }}" class="img-fluid w-100 rounded-top" alt="Fresh Apples">
                            <div class="service-content text-center p-4 bg-primary text-white">
                                <h5>Fresh Apples</h5>
                                <h3 class="mb-0">20% OFF</h3>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Service 2 -->
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="text-decoration-none">
                        <div class="service-item rounded overflow-hidden shadow-sm hover-scale">
                            <img src="{{ asset('website/img/featur-2.jpg') }}" class="img-fluid w-100 rounded-top" alt="Tasty Fruits">
                            <div class="service-content text-center p-4 bg-light text-primary">
                                <h5>Tasty Fruits</h5>
                                <h3 class="mb-0">Free Delivery</h3>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Service 3 -->
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="text-decoration-none">
                        <div class="service-item rounded overflow-hidden shadow-sm hover-scale">
                            <img src="{{ asset('website/img/featur-3.jpg') }}" class="img-fluid w-100 rounded-top" alt="Exotic Vegetable">
                            <div class="service-content text-center p-4 bg-secondary text-white">
                                <h5>Exotic Vegetable</h5>
                                <h3 class="mb-0">Discount $30</h3>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Section Start-->
    <div class="container-fluid banner bg-secondary my-5">
        <div class="container py-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="py-4">
                        <h1 class="display-3 text-white">Fresh Exotic Fruits</h1>
                        <p class="fw-normal display-3 text-dark mb-4">in Our Store</p>
                        <p class="mb-4 text-dark">The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic words etc.</p>
                        <a href="#" class="banner-btn btn border-2 border-white rounded-pill text-dark py-3 px-5">BUY</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="{{ asset('website/img/baner-1.png') }}" class="img-fluid w-100 rounded" alt="">
                        <div class="d-flex align-items-center justify-content-center bg-white rounded-circle position-absolute" style="width: 140px; height: 140px; top: 0; left: 0;">
                            <h1 style="font-size: 100px;">1</h1>
                            <div class="d-flex flex-column">
                                <span class="h2 mb-0">50$</span>
                                <span class="h4 text-muted mb-0">kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Section End -->

    <!-- Fact Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <!-- Fact Item 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="counter bg-white rounded text-center p-4 shadow-sm hover-shadow">
                        <div class="counter-icon mb-3">
                            <i class="fa fa-users fa-2x text-primary"></i>
                        </div>
                        <h4 class="text-uppercase text-secondary mb-2">Satisfied Customers</h4>
                        <h1 class="display-5 text-dark mb-0">1963</h1>
                    </div>
                </div>

                <!-- Fact Item 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="counter bg-white rounded text-center p-4 shadow-sm hover-shadow">
                        <div class="counter-icon mb-3">
                            <i class="fa fa-award fa-2x text-primary"></i>
                        </div>
                        <h4 class="text-uppercase text-secondary mb-2">Quality of Service</h4>
                        <h1 class="display-5 text-dark mb-0">99%</h1>
                    </div>
                </div>

                <!-- Fact Item 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="counter bg-white rounded text-center p-4 shadow-sm hover-shadow">
                        <div class="counter-icon mb-3">
                            <i class="fa fa-certificate fa-2x text-primary"></i>
                        </div>
                        <h4 class="text-uppercase text-secondary mb-2">Quality Certificates</h4>
                        <h1 class="display-5 text-dark mb-0">33</h1>
                    </div>
                </div>

                <!-- Fact Item 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="counter bg-white rounded text-center p-4 shadow-sm hover-shadow">
                        <div class="counter-icon mb-3">
                            <i class="fa fa-boxes fa-2x text-primary"></i>
                        </div>
                        <h4 class="text-uppercase text-secondary mb-2">Available Products</h4>
                        <h1 class="display-5 text-dark mb-0">789</h1>
                    </div>
                </div>
            </div>
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