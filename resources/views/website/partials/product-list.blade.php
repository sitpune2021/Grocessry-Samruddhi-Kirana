{{-- PRODUCTS CHECK --}}
@if($products->count() > 0)

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4">

    @foreach($products as $product)
    <div class="col">
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
                        <img src="{{ asset('storage/products/'.$image) }}"
                            class="img-fluid w-100 rounded-top"
                            alt="{{ $product->name }}"
                            style="height:200px; object-fit:cover;">
                    @else
                        <img src="{{ asset('website/img/no-image.png') }}"
                            class="img-fluid w-100 rounded-top"
                            alt="No Image"
                            style="height:200px; object-fit:cover;">
                    @endif
                </a>
            </div>

            <div class="p-3 border border-top-0">

                <form action="{{ route('add_cart') }}" method="POST" class="add-cart-form" onsubmit="return false;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <h6 class="product-title text-center mb-1">
                        {{ Str::limit(Str::title($product->name), 40) }}
                    </h6>

                    <p class="product-unit text-center text-muted mb-2">
                        {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                        {{ Str::title(optional($product->unit)->name) }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-success">
                                ₹{{ number_format($product->final_price, 0) }}
                            </span><br>
                            <small class="text-decoration-line-through text-muted">
                                ₹{{ number_format($product->mrp, 0) }}
                            </small>
                        </div>

                        @include('website.partials.add-to-cart-btn', ['product' => $product])
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endforeach

</div>

{{-- PAGINATION --}}
<div class="mt-4 d-flex justify-content-end">
    {{ $products->links() }}
</div>

@else

{{-- EMPTY STATE --}}
<div class="empty-wrapper">
    <div class="empty-state text-center">
        <img src="{{ asset('website/img/nothing.gif') }}"
            alt="No Products"
            class="empty-img mb-4">

        <h4 class="fw-semibold mb-2">Nothing here yet</h4>
        <p class="text-muted mb-0">Try searching for something else.</p>
    </div>
</div>

@endif


<style>
    .empty-wrapper {
    width: 100%;
    min-height: 60vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.empty-img {
    width: 200px;
    opacity: 0.9;
}

.fruite-item {
    transition: 0.3s;
}

.fruite-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}

</style>