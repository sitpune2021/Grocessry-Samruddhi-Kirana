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

    .hero-banner {
        height: 420px;
        position: relative;
    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right,
                rgba(0, 0, 0, 0.55),
                rgba(0, 0, 0, 0.1));
    }

    .hero-content {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        /* horizontal center */
    }

    .hero-title {
        margin-top: 220px;
        /* title thoda upar */
        text-align: center;
        font-size: 42px;
        font-weight: 700;
        line-height: 1.2;
    }

    .hero-btn {
        margin-top: 140px;
        /* 🔥 push button to bottom */
        margin-bottom: 40px;
        /* bottom spacing */
        background: #81c408;
        color: #fff;
        padding: 12px 28px;
        border-radius: 100px;
        font-weight: 600;
        transition: all 0.3s ease;
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

                    <div class="carousel-inner rounded-4 overflow-hidden">

                        @foreach($banners as $key => $banner)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <div class="hero-banner position-relative">

                                <img src="{{ asset('storage/'.$banner->image) }}"
                                    alt="{{ $banner->name }}"
                                    class="hero-img">

                                <!-- Overlay -->
                                <div class="hero-overlay"></div>

                                <!-- Content -->
                                <div class="hero-content">

                                    <h1 class="hero-title">
                                        {{ $banner->name }}
                                    </h1>

                                    <a href="#" class="btn hero-btn">
                                        Shop Now
                                    </a>
                                </div>
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

    <!-- <div class="container py-4">
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
    </div> -->


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
 
    <!-- Banner Section End -->

    <!-- Fact Start -->
   

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