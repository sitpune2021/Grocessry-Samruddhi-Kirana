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
        <div class="container  ">
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

                        <!-- LEFT SIDEBAR -->
                        <div class="col-lg-3 col-md-4">
                            <div class="card shadow-sm p-3">
                                <h4 class="mb-3">Price Range</h4>

                                <form id="priceForm">
                                    <input type="number"
                                        class="form-control mb-2"
                                        id="minPrice"
                                        placeholder="Min Price"
                                        value="{{ request('min_price') }}">

                                    <input type="number"
                                        class="form-control mb-3"
                                        id="maxPrice"
                                        placeholder="Max Price"
                                        value="{{ request('max_price') }}">

                                    <button type="button"
                                        class="btn btn-sm btn-primary w-100"
                                        onclick="loadProducts(1)">
                                        Apply Filter
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- RIGHT PRODUCTS -->
                        <div class="col-lg-9 col-md-8">
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
    // Category change
    document.getElementById('categoryFilter').addEventListener('change', function () {
        loadProducts(1);
    });

    function loadProducts(page = 1) {
        let categoryId = document.getElementById('categoryFilter').value;
        let minPrice   = document.getElementById('minPrice').value;
        let maxPrice   = document.getElementById('maxPrice').value;

        fetch(`{{ route('shop.filter') }}?category_id=${categoryId}&min_price=${minPrice}&max_price=${maxPrice}&page=${page}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('product-container').innerHTML = html;
            });
    }

    // Pagination AJAX
    document.addEventListener('click', function (e) {
        let link = e.target.closest('.pagination a');
        if (!link) return;

        e.preventDefault();
        let page = new URL(link.href).searchParams.get('page');
        loadProducts(page);
    });
</script>
