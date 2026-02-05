@extends('website.layout')


@section('title', 'Home')

@section('content')


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
        <div class="col-lg-4 mb-4" style="margin:auto; text-align:center;">
            <h3 class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-weight:700; font-size:28px; letter-spacing:0.5px;">
                Popular Categories
            </h3>
        </div>

        <div class="category-slider">
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
        <div class="slider-dots">
            <span class="dot dot-red"></span>
            <span class="dot dot-green active"></span>
            <span class="dot dot-blue"></span>
        </div>
    </div>

    <!-- sell product  -->
    <div class="container py-2">
        <div class="row p-2">
            <div class="col text-start">
                <h3 class="fw-bold text-dark"
                    style="font-family:'Poppins',sans-serif;font-weight:700;font-size:28px;letter-spacing:0.5px;">
                    Grab Or Gone
                </h3>
            </div>
        </div>
        <div class="position-relative product-slider-wrapper">
            <button class="slider-arrow left">&#10094;</button>

            <div class="product-slider">
                @foreach($saleproduct as $product)
                @php
                $image = $product->product_images[0] ?? null;
                $sale = $product->sale; // 🔥 on_sale_products data
                @endphp

                <div class="product-slide-item">
                    <div class="product-sm-card">

                        {{-- DISCOUNT BADGE --}}
                        @if($sale)
                        <div class="offer-badge">
                            {{ $sale->discount_percent }}% OFF
                        </div>
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

                        {{-- PRICE SECTION --}}
                        <div class="product-sm-footer">
                            <div>
                                <span class="price-new">
                                    ₹{{ number_format($sale->sale_price, 0) }}
                                </span><br>

                                <span class="price-old">
                                    ₹{{ number_format($sale->mrp, 0) }}
                                </span>
                            </div>

                            {{-- ADD TO CART --}}
                            <form action="{{ route('add_cart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                @include('website.partials.add-to-cart-btn', ['product' => $product])

                            </form>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            <button class="slider-arrow right">&#10095;</button>
        </div>
    </div>


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite">
        <div class="container">
            <div class="tab-class text-center">

                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h3 class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-weight:700; font-size:28px; letter-spacing:0.5px;">
                            Our Products
                        </h3>
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

                                        <!-- <div class="delivery-time mb-1">Free delivery</div> -->

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

                                                @include('website.partials.add-to-cart-btn', ['product' => $product])
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </div>

                        <div class="mt-4 d-flex flex-column align-items-end">
                            {{-- Pagination --}}
                            {{ $allProducts->onEachSide(0)->links() }}
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

                                                @include('website.partials.add-to-cart-btn', ['product' => $product])
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
                            {{ $categoryProducts->onEachSide(0)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- slide product  -->
    <div class="container p-0 ">
        @foreach($cate as $category)
        @if($category->products->count())

        <div class="row p-2">
            <div class="col text-start">
                <h3 class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-weight:700; font-size:28px; letter-spacing:0.5px;">{{ $category->name }}</h3>
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
                                @include('website.partials.add-to-cart-btn', ['product' => $product])
                            </form>
                        </div>

                    </div>
                </div>

                @endforeach
            </div>
            <div class="slider-dots">
                <span class="dot dot-red"></span>
                <span class="dot dot-green active"></span>
                <span class="dot dot-blue"></span>
            </div>
            <button class="slider-arrow right">&#10095;</button>
            <div class="slider-dots text-center mt-3"></div>
        </div>

        @endif
        @endforeach
    </div>

    <!-- latest product  -->
    <div class="container py-2">
        <div class="row p-2">
            <div class="col text-start">
                <h3 class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-weight:700; font-size:28px; letter-spacing:0.5px;">Latest Products</h3>
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
                                @include('website.partials.add-to-cart-btn', ['product' => $product])
                            </form>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
            <div class="slider-dots">
                <span class="dot dot-red"></span>
                <span class="dot dot-green active"></span>
                <span class="dot dot-blue"></span>
            </div>
            <button class="slider-arrow right">&#10095;</button>
        </div>

    </div>

    <!-- brands -->
    <div class="container py-2">
        <div class="row p-2">
            <div class="col text-start">
                <h3 class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif; font-weight:700; font-size:28px; letter-spacing:0.5px;">Top Brands</h3>
            </div>
        </div>
        <div class="brand-slider p-3" id="brandSlider">
            <div class="brand-track" id="brandTrack">
                @foreach($brands as $brand)
                <div class="brand-col">
                    <img src="{{ asset('storage/brands/'.$brand->logo) }}"
                        alt="{{ $brand->name }}">
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Featurs Section Start -->
    <div class="container featurs py-5">
        <div class="container">
            <div class="row g-3">

                <!-- Feature 1 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>Free Shipping</h5>
                            <p>Free on orders over $300</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>Secure Payment</h5>
                            <p>100% secure payment</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>30 Day Return</h5>
                            <p>30-day money guarantee</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-6 col-md-3">
                    <div class="featurs-item">
                        <div class="featurs-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="featurs-content">
                            <h5>24/7 Support</h5>
                            <p>Fast support anytime</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const slider = document.querySelector(".category-slider");
        let scrollAmount = 0;

        setInterval(() => {
            if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth) {
                slider.scrollLeft = 0;
            } else {
                slider.scrollLeft += 130;
            }
        }, 2500); // speed control
    });
</script>

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

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const slider = document.getElementById("brandSlider");
        const track = document.getElementById("brandTrack");

        /* 🔁 clone brands MULTIPLE times */
        const original = track.innerHTML;
        track.innerHTML = original + original + original + original;

        let speed = 0.5;

        function infiniteScroll() {
            slider.scrollLeft += speed;

            if (slider.scrollLeft >= track.scrollWidth / 2) {
                slider.scrollLeft = 0;
            }

            requestAnimationFrame(infiniteScroll);
        }

        infiniteScroll();
    });
</script>





@endsection