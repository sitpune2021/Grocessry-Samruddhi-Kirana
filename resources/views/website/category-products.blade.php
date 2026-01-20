@extends('website.layout')

@section('title','Home')

@section('content')

<style>
    /* ================= SCROLL CONTAINER ================= */
    .product-scroll {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding-bottom: 10px;
        scroll-behavior: smooth;
    }

    /* Hide scrollbar */
    .product-scroll::-webkit-scrollbar {
        display: none;
    }

    .product-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* ================= PRODUCT CARD ================= */
    .product-card {
        min-width: 180px;
        max-width: 180px;
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        flex-shrink: 0;
        position: relative;
    }

    .product-card img {
        width: 100%;
        height: 140px;
        object-fit: contain;
        border-radius: 8px;
    }

    .product-card h6 {
        font-size: 14px;
        margin: 10px 0 4px;
        min-height: 36px;
    }

    .qty {
        font-size: 12px;
        color: #777;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price {
        font-weight: 600;
    }

    /* ADD BUTTON */
    .btn-add {
        border: 1px solid #28a745;
        background: #fff;
        color: #28a745;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 6px;
    }

    /* DISCOUNT BADGE (OPTIONAL) */
    .badge-off {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #2563eb;
        color: #fff;
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 4px;
    }
</style>

<div class="container py-4">

    <h4 class="mb-3">Buy Dishwashing Accessories Online</h4>

    <div class="product-scroll" style="margin-top: 150px;">

        @foreach($products as $product)

        <div class="product-card">

            {{-- Optional badge --}}
            <div class="badge-off">40% OFF</div>

            @php
            $image = $product->product_images[0] ?? null;
            @endphp

            <img
                src="{{ $image ? asset('storage/products/'.$image) : asset('website/img/no-image.png') }}"
                alt="{{ $product->name }}">

            <h6>{{ Str::limit($product->name, 40) }}</h6>

            <p class="qty">1 pc</p>

            <div class="price-row">
                <span class="price">₹ {{ $product->mrp }}</span>

                <form action="{{ route('add_cart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn-add">ADD</button>
                </form>
            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection