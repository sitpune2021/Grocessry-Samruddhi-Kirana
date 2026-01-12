@extends('website.layout')

@section('title', 'Home')


@section('content')
<style>
    .fruite-img {
        height: 200px;
        width: 100%;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* 🔥 KEY LINE */
        transition: transform 0.3s ease;
    }
</style>

<body>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Hero Start -->
    <div class="py-5 mb-5">
        <div class="py-5" style="
                min-height: 500px;
                margin: 0px auto;
                background:
                    linear-gradient(rgba(248, 223, 173, 0.1), rgba(248, 223, 173, 0.1)),
                    url('/website/img/hero-img.jpg');
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            ">
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
        <div class="container-fluid featurs py-3">
            <div class="container py-3">
                <div class="row g-3">

                    <div class="col-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-3">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto"
                                style="width: 90px; height: 90px;">
                                <i class="fas fa-car-side fa-lg text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h6 class="mb-1">Free Shipping</h6>
                                <p class="mb-0 small">Order over $300</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-3">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto"
                                style="width: 90px; height: 90px;">
                                <i class="fas fa-user-shield fa-lg text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h6 class="mb-1">Secure Payment</h6>
                                <p class="mb-0 small">100% secure</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-3">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto"
                                style="width: 90px; height: 90px;">
                                <i class="fas fa-exchange-alt fa-lg text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h6 class="mb-1">Easy Return</h6>
                                <p class="mb-0 small">30 days policy</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-3">
                            <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto"
                                style="width: 90px; height: 90px;">
                                <i class="fa fa-phone-alt fa-lg text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h6 class="mb-1">24/7 Support</h6>
                                <p class="mb-0 small">Fast help</p>
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
        </style>

        <!-- Fruits Shop Start -->
        <div class="container-fluid fruite py-3">
            <div class="container py-3">
                <div class="tab-class text-center">

                    <div class="row g-2 align-items-center">
                        <div class="col-lg-4 text-start">
                            <h1 class="mb-2">Our Organic Products</h1>
                        </div>
                        <div class="col-lg-8 text-end">
                            <ul class="nav nav-pills d-inline-flex mb-3">
                                <li class="nav-item">
                                    <a class="d-flex m-1 py-1 px-2 bg-light rounded-pill {{ empty($categoryId) ? 'active' : '' }}"
                                        data-bs-toggle="pill" href="#tab-1">
                                        <span class="text-dark" style="width: 120px;">All Products</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="d-flex m-1 py-1 px-2 bg-light rounded-pill {{ !empty($categoryId) ? 'active' : '' }}"
                                        data-bs-toggle="pill" href="#tab-2">
                                        <span class="text-dark" style="width: 120px;">Category Search</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content">

                        <!-- TAB 1 : ALL PRODUCTS -->
                        <div id="tab-1" class="tab-pane fade show {{ empty($categoryId) ? 'active' : '' }}">
                            <div class="row g-3">
                                @foreach($allProducts as $product)
                                <div class="col-md-6 col-lg-3">
                                    <div class="rounded fruite-item border overflow-hidden">
                                        @php
                                        $images = $product->product_images;
                                        $image = $images[0] ?? null;
                                        @endphp

                                        <div class="overflow-hidden" style="height:180px;">
                                            @if($image)
                                            <img src="{{ asset('storage/products/' . $image) }}"
                                                alt="{{ $product->name }}"
                                                class="img-fluid w-100 h-100 rounded-top"
                                                style="object-fit:cover; transition: transform 0.3s;">
                                            @else
                                            <img src="{{ asset('website/img/no-image.png') }}"
                                                alt="No Image"
                                                class="img-fluid w-100 h-100 rounded-top"
                                                style="object-fit:cover; transition: transform 0.3s;">
                                            @endif
                                        </div>

                                        <div class="px-2 pt-2">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">₹ {{ $product->mrp }}</small>
                                        </div>

                                        <div class="p-2 text-center">
                                            <a href="#" class="btn btn-sm border rounded-pill px-2 text-primary">
                                                <i class="fa fa-shopping-bag me-1"></i> Add
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- TAB 2 : CATEGORY SEARCH -->
                        <div id="tab-2" class="tab-pane fade show {{ !empty($categoryId) ? 'active' : '' }}">
                            <form method="GET" action="{{ route('home') }}" class="mb-2">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <div class="row g-3">
                                @forelse($categoryProducts as $product)
                                <div class="col-md-6 col-lg-3">
                                    <div class="rounded fruite-item border overflow-hidden">
                                        @php
                                        $images = $product->product_images;
                                        $image = $images[0] ?? null;
                                        @endphp

                                        <div style="height:180px;" class="overflow-hidden">
                                            @if($image)
                                            <img src="{{ asset('storage/products/'.$image) }}"
                                                class="img-fluid w-100 h-100 rounded-top"
                                                alt="{{ $product->name }}"
                                                style="object-fit: cover;">
                                            @else
                                            <img src="{{ asset('website/img/no-image.png') }}"
                                                class="img-fluid w-100 h-100 rounded-top"
                                                alt="No Image"
                                                style="object-fit: cover;">
                                            @endif
                                        </div>

                                        <div class="px-2 pt-2">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">₹ {{ $product->mrp }}</small>
                                        </div>

                                        <div class="p-2 text-center">
                                            <a href="#" class="btn btn-sm border rounded-pill px-2 text-primary">
                                                <i class="fa fa-shopping-bag me-1"></i> Add
                                            </a>
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
        <!-- Fruits Shop End -->

        <!-- Features Start -->
        <div class="container-fluid service py-3">
            <div class="container py-3">
                <div class="row g-5 justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <a href="#" class="text-decoration-none">
                            <div class="service-item small-card bg-secondary rounded border overflow-hidden">
                                <div class="service-img">
                                    <img src="{{ asset('website/img/featur-1.jpg') }}" class="img-fluid w-100" alt="Fresh Apples">
                                </div>
                                <div class="service-content bg-primary d-flex flex-column justify-content-center align-items-center" style="min-height: 100px; text-align: center;">
                                    <h5 class="text-white mb-1">Fresh Apples</h5>
                                    <small class="text-white fw-bold">20% OFF</small>
                                </div>

                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <a href="#" class="text-decoration-none">
                            <div class="service-item small-card bg-dark rounded border overflow-hidden">
                                <div class="service-img">
                                    <img src="{{ asset('website/img/featur-2.jpg') }}" class="img-fluid w-100" alt="Tasty Fruits">
                                </div>
                                <div class="service-content bg-light d-flex flex-column justify-content-center align-items-center" style="min-height: 100px; text-align: center;">
                                    <h5 class="text-primary mb-1">Tasty Fruits</h5>
                                    <small class="fw-bold">Free Delivery</small>
                                </div>

                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <a href="#" class="text-decoration-none">
                            <div class="service-item small-card bg-primary rounded border overflow-hidden">
                                <div class="service-img">
                                    <img src="{{ asset('website/img/featur-3.jpg') }}" class="img-fluid w-100" alt="Exotic Vegetable">
                                </div>
                                <div class="service-content bg-secondary py-3 d-flex flex-column justify-content-center align-items-center" style="min-height: 100px;">
                                    <h5 class="text-white mb-1">Exotic Vegetable</h5>
                                    <small class="text-white fw-bold">Discount ₹30</small>
                                </div>

                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features End -->

        <!-- Fact Start -->
        <div class="container-fluid py-3">
            <div class="container">
                <div class="bg-light p-3 rounded">
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-3 text-center">
                                <i class="fa fa-users text-secondary mb-1"></i>
                                <h6 class="mb-1">Satisfied Customers</h6>
                                <h4 class="mb-0">1963</h4>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-3 text-center">
                                <i class="fa fa-users text-secondary mb-1"></i>
                                <h6 class="mb-1">Quality of Service</h6>
                                <h4 class="mb-0">99%</h4>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-3 text-center">
                                <i class="fa fa-users text-secondary mb-1"></i>
                                <h6 class="mb-1">Quality Certificates</h6>
                                <h4 class="mb-0">33</h4>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-3">
                            <div class="counter bg-white rounded p-3 text-center">
                                <i class="fa fa-users text-secondary mb-1"></i>
                                <h6 class="mb-1">Available Products</h6>
                                <h4 class="mb-0">789</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fact End -->

        <!-- Tastimonial Start -->
        <!-- <div class="container-fluid testimonial py-5">
            <div class="container py-5">
                <div class="testimonial-header text-center">
                    <h4 class="text-primary">Our Testimonial</h4>
                    <h1 class="display-5 mb-5 text-dark">Our Client Saying!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel">
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">Lorem Ipsum is simply dummy text of the printing Ipsum has been the industry's standard dummy text ever since the 1500s,
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="{{ asset('website/img/testimonial-1.jpg') }}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Client Name</h4>
                                    <p class="m-0 pb-3">Profession</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">Lorem Ipsum is simply dummy text of the printing Ipsum has been the industry's standard dummy text ever since the 1500s,
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="{{ asset('website/img/testimonial-1.jpg') }}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Client Name</h4>
                                    <p class="m-0 pb-3">Profession</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">Lorem Ipsum is simply dummy text of the printing Ipsum has been the industry's standard dummy text ever since the 1500s,
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    <img src="{{ asset('website/img/testimonial-1.jpg') }}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">Client Name</h4>
                                    <p class="m-0 pb-3">Profession</p>
                                    <div class="d-flex pe-5">
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                        <i class="fas fa-star text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- Tastimonial End -->

</body>

@endsection