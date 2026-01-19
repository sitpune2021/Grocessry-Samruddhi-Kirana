@extends('website.layout')

@section('title', $product->name)

@section('content')

<!-- Page Header -->
<div class="container-fluid page-header py-4 mb-5 bg-dark">
    <h1 class="text-center text-white display-6">Product Details</h1>
</div>

<!-- Product Detail -->
<div class="container mb-5">
    <div class="row g-4">

        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <img src="{{ asset('storage/products/'.$product->product_images[0]) }}"
                    class="img-fluid rounded" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h3 class="fw-bold">{{ $product->name }}</h3>
                    <p class="text-muted mb-2">Category: {{ $product->category->name ?? 'N/A' }}</p>

                    <h4 class="text-primary fw-bold mb-3">₹{{ $product->mrp }}</h4>

                    <form action="{{ route('add_cart') }}" method="POST" class="d-flex align-items-center gap-3 flex-wrap">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="input-group" style="width:130px;">
                            <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
                            <input type="number" name="qty" value="1" min="1" class="form-control text-center">
                            <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
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

    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">Related Products</h3>

        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100 shadow-sm position-relative">

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
                            <button class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="fa fa-shopping-bag me-2"></i>Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection