@extends('website.layout')

@section('title', 'Home')


@section('content')

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
                <div class="col-md-12 col-lg-5" style="padding: 0px 70px;">
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

        <!-- Featurs Section Start -->
        <div class="container-fluid featurs py-5">
            <div class="container py-5 p">
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

        <!-- Featurs Section End -->

        <style>
            /* FORCE pagination to horizontal row */
            .pagination {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between;
                gap: 6px;
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
        </style>

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

        <div class="container py-5">
            <div class="tab-content">

                <!-- TAB 1 : ALL PRODUCTS (Small Cards) -->
                <div id="tab-1"
                    class="tab-pane fade show {{ empty($categoryId) ? 'active' : '' }}">

                    <div class="container-fluid fruite py-5">
                        <div class="container">

                            <div class="row mb-4">
                                <div class="col text-start">
                                    <h3>Spices & Masala</h3>
                                </div>
                            </div>

                            <div class="row g-3">

                                @forelse($allProducts as $product)
                                @php
                                $image = $product->product_images[0] ?? null;
                                @endphp

                                <div class="col-6 col-md-4 col-lg-2">
                                    <div class="product-sm-card">

                                        <div class="product-sm-img">
                                            <img src="{{ $image
                                            ? asset('storage/products/'.$image)
                                            : asset('website/img/no-image.png') }}"
                                                alt="{{ $product->name }}">
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

                                @empty
                                <p class="text-center">No products found</p>
                                @endforelse

                            </div>

                        </div>
                    </div>



                </div>

            </div>
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


        <!-- Vesitable Shop Start-->
        <!-- <div class="container-fluid vesitable py-5">
            <div class="container py-5">
                <h1 class="mb-0">Fresh Organic Vegetables</h1>
                <div class="owl-carousel vegetable-carousel justify-content-center">
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-6.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-1.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-3.png') }}" class="img-fluid w-100 rounded-top bg-light" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Banana</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-4.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Bell Papper</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-5.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Potatoes</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-6.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-5.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Potatoes</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="border border-primary rounded position-relative vesitable-item">
                        <div class="vesitable-img">
                            <img src="{{ asset('website/img/vegetable-item-6.jpg') }}" class="img-fluid w-100 rounded-top" alt="">
                        </div>
                        <div class="text-white bg-primary px-3 py-1 rounded position-absolute" style="top: 10px; right: 10px;">Vegetable</div>
                        <div class="p-4 rounded-bottom">
                            <h4>Parsely</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te incididunt</p>
                            <div class="d-flex justify-content-between flex-lg-wrap">
                                <p class="text-dark fs-5 fw-bold mb-0">$7.99 / kg</p>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Vesitable Shop End -->


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


        <!-- Bestsaler Product Start -->
        <!-- <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                    <h1 class="display-4">Bestseller Products</h1>
                    <p>Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-1.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-2.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-3.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-4.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-5.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <img src="{{ asset('website/img/best-product-6.jpg') }}" class="img-fluid rounded-circle w-100" alt="">
                                </div>
                                <div class="col-6">
                                    <a href="#" class="h5">Organic Tomato</a>
                                    <div class="d-flex my-3">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="mb-3">3.12 $</h4>
                                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="text-center">
                            <img src="{{ asset('website/img/fruite-item-1.jpg') }}" class="img-fluid rounded" alt="">
                            <div class="py-4">
                                <a href="#" class="h5">Organic Tomato</a>
                                <div class="d-flex my-3 justify-content-center">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 class="mb-3">3.12 $</h4>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="text-center">
                            <img src="{{ asset('website/img/fruite-item-2.jpg') }}" class="img-fluid rounded" alt="">
                            <div class="py-4">
                                <a href="#" class="h5">Organic Tomato</a>
                                <div class="d-flex my-3 justify-content-center">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 class="mb-3">3.12 $</h4>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="text-center">
                            <img src="{{ asset('website/img/fruite-item-3.jpg') }}" class="img-fluid rounded" alt="">
                            <div class="py-4">
                                <a href="#" class="h5">Organic Tomato</a>
                                <div class="d-flex my-3 justify-content-center">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 class="mb-3">3.12 $</h4>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="text-center">
                            <img src="{{ asset('website/img/fruite-item-4.jpg') }}" class="img-fluid rounded" alt="">
                            <div class="py-2">
                                <a href="#" class="h5">Organic Tomato</a>
                                <div class="d-flex my-3 justify-content-center">
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star text-primary"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <h4 class="mb-3">3.12 $</h4>
                                <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Bestsaler Product End -->


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

</body>

@endsection