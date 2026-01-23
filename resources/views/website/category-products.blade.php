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

    /* //////////////////////index slider card  */
    .product-card {
        width: 230px;
        height: 320px;
        /* FIXED HEIGHT */
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    /* OFFER BADGE */
    .offer-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #2563eb;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 6px;
        border-radius: 4px;
    }

    /* IMAGE */
    .product-img {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-img img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
    }

    /* INFO */
    .product-info {
        flex-grow: 1;
    }

    .delivery-time {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .product-title {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
        height: 36px;
        /* 2 lines fixed */
        overflow: hidden;
    }

    .product-unit {
        font-size: 13px;
        color: #777;
        /* margin-top: 4px; */
    }

    /* PRICE ROW */
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* margin-top: 8px; */
    }

    .price-box {

        flex-direction: column;
    }

    .price-new {
        font-size: 16px;
        font-weight: 700;
    }

    .price-old {
        font-size: 12px;
        color: #999;
        text-decoration: line-through;
    }

    /* ADD BUTTON */
    .btn-add {
        border: 1px solid #28a745;
        background: #fff;
        color: #28a745;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-add:hover {
        background: #28a745;
        color: #fff;
    }
</style>

<div class="container py-4">

    <h4 class="mb-3">Buy Dishwashing Accessories Online</h4>

    <div class="product-scroll" style="margin-top: 150px;">

        @foreach($products as $product)

        <div class="product-card">
            <a href="{{ route('productdetails', $product->id) }}">

                {{-- DISCOUNT --}}
                @if($product->mrp > $product->final_price)
                @php
                $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                @endphp
                <div class="offer-badge">{{ $discount }}% OFF</div>
                @endif

                @php
                $image = $product->product_images[0] ?? null;
                @endphp

                <img
                    src="{{ $image ? asset('storage/products/'.$image) : asset('website/img/no-image.png') }}"
                    alt="{{ $product->name }}">

                <div class="delivery-time">Free delivery</div>

                <h6 class="product-title">
                    {{ Str::limit(Str::title($product->name), 40) }}
                </h6>

                <p class="product-unit">
                    {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                    {{ Str::title(optional($product->unit)->name) }}
                </p>

                <div class="price-row">
                    <div class="price-box">
                        <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span>
                        <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                    </div>
            </a>

            <form action="{{ route('add_cart') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn-add">ADD</button>
            </form>
        </div>
    </div>

    </a>

    @endforeach

</div>

</div>

@endsection