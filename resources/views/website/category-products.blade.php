

@extends('website.layout')

@section('title', $category->name)

@section('content')

<style>
    .card {
    min-height: 350px; /* adjust as needed */
}
.card img {
    height: 200px;
    object-fit: cover;
}

</style>
<div class="container py-5">

    <h3 class="mb-4">
        {{ $category->name }} Products
    </h3>

    <div class="row g-4" >

        @forelse($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6" >

            <div class="card h-100">
                <img src="{{ asset('storage/products/'.$product->image) }}"
                     class="card-img-top"
                     alt="{{ $product->name }}">

                <div class="card-body text-center">
                    <h6>{{ $product->name }}</h6>
                    <p class="text-success fw-bold">₹ {{ $product->price }}</p>
                </div>
            </div>

        </div>
        @empty
        <p>No products found in this category.</p>
        @endforelse

    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>

</div>

@endsection
