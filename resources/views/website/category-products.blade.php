@extends('website.layout')

@section('title','Home')

@section('content')

<style>
    .product-scroll {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding-bottom: 10px;
        scroll-behavior: smooth;
    }

    .product-scroll::-webkit-scrollbar {
        display: none;
    }

    .product-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* card width for slider */
   
</style>

<div class="container py-4" style="margin-top:160px;">

    <h4 class="mb-3">Buy Dishwashing Accessories Online</h4>

    <div class="product-scroll">

        @foreach($products as $product)
        @php
        $image = $product->product_images[0] ?? null;
        @endphp

        <div class="rounded position-relative fruite-item">

            {{-- DISCOUNT --}}
            @if($product->mrp > $product->final_price)
            @php
            $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
            @endphp
            <div class="offer-badge">{{ $discount }}% OFF</div>
            @endif

            <div class="fruite-img">
                <a href="{{ route('productdetails', $product->id) }}">
                    <img
                        src="{{ $image
                            ? asset('storage/products/'.$image)
                            : asset('website/img/no-image.png') }}"
                        class="img-fluid w-100 rounded-top"
                        style="height:200px;object-fit:cover;">
                </a>
            </div>

            <div class="p-4 border border-top-0">

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

        @endforeach

    </div>
</div>


</div>

@endsection