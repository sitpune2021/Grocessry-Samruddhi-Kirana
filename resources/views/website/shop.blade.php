@extends('website.layout')

@section('title', 'Home')


@section('content')

<body>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <!-- <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Pages</a></li>
                <li class="breadcrumb-item active text-white">Shop</li>
            </ol> -->
    </div>
    <!-- Single Page Header End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <!-- <h1 class="mb-4">All Products</h1> -->
            <div class="row g-4">
                <div class="col-lg-12">

                    <div class="row g-4">

                        <div class="col-xl-3">
                            <!-- <div class="input-group w-100 mx-auto d-flex">
                                    <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                                    <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                                </div> -->
                        </div>

                        <div class="col-6"></div>
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <select id="categoryFilter" class="form-select">
                                    <option value="all">All Products</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row g-4">

                        <div class="col-lg-3">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="mb-3">

                                    </div>
                                </div>

                                <!-- <div class="col-lg-12">
                                        <div class="mb-3">
                                            <h4 class="mb-2">Price</h4>
                                            <form method="GET" action="{{ route('shop') }}">
                                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                                <input type="range"
                                                    class="form-range w-100"
                                                    name="price"
                                                    min="0"
                                                    max="500"
                                                    value="{{ $maxPrice ?? 0 }}"
                                                    onchange="this.form.submit()">
                                                <output>{{ $maxPrice ?? 0 }}</output>
                                            </form>
                                        </div>
                                    </div> -->

                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <h4 class="mb-2">Price Range</h4>
                                        <form id="priceForm">
                                            <input type="number" class="form-control mb-2"
                                                id="minPrice" placeholder="Min Price" value="{{ request('min_price') }}">

                                            <input type="number" class="form-control mb-2"
                                                id="maxPrice" placeholder="Max Price" value="{{ request('max_price') }}">

                                            <button type="button" class="btn btn-primary w-100" onclick="loadProducts(1)">
                                                Apply Filter
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- <div class="col-lg-12">
                                        <h4 class="mb-3">Featured products</h4>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-1.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-2.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div class="rounded me-4" style="width: 100px; height: 100px;">
                                                <img src="img/featur-3.jpg" class="img-fluid rounded" alt="">
                                            </div>
                                            <div>
                                                <h6 class="mb-2">Big Banana</h6>
                                                <div class="d-flex mb-2">
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star text-secondary"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                                <div class="d-flex mb-2">
                                                    <h5 class="fw-bold me-2">2.99 $</h5>
                                                    <h5 class="text-danger text-decoration-line-through">4.11 $</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center my-4">
                                            <a href="#" class="btn border border-secondary px-4 py-3 rounded-pill text-primary w-100">Vew More</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="position-relative">
                                            <img src="img/banner-fruits.jpg" class="img-fluid w-100 rounded" alt="">
                                            <div class="position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);">
                                                <h3 class="text-secondary fw-bold">Fresh <br> Fruits <br> Banner</h3>
                                            </div>
                                        </div>
                                    </div> -->

                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div id="product-container">
                                @include('website.partials.product-list', ['products' => $products])
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->


    <script>
        document.getElementById('categoryFilter').addEventListener('change', function() {
            loadProducts(1);
        });

        function loadProducts(page = 1) {
            let categoryId = document.getElementById('categoryFilter').value;

            fetch(`{{ route('shop.filter') }}?category_id=${categoryId}&page=${page}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('product-container').innerHTML = html;
                });
        }

        // pagination ajax
        document.addEventListener('click', function(e) {
            let link = e.target.closest('.pagination a');
            if (!link) return;

            e.preventDefault();
            let page = new URL(link.href).searchParams.get('page');
            loadProducts(page);
        });
    </script>

    <script>
        function loadProducts(page = 1) {
            let categoryId = document.getElementById('categoryFilter').value;
            let minPrice = document.getElementById('minPrice').value;
            let maxPrice = document.getElementById('maxPrice').value;

            fetch(`{{ route('shop.filter') }}?category_id=${categoryId}&min_price=${minPrice}&max_price=${maxPrice}&page=${page}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('product-container').innerHTML = html;
                });
        }

        // Pagination AJAX
        document.addEventListener('click', function(e) {
            let link = e.target.closest('.pagination a');
            if (!link) return;

            e.preventDefault();
            let page = new URL(link.href).searchParams.get('page');
            loadProducts(page);
        });
    </script>