<div class="row g-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="rounded position-relative fruite-item h-100">

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

            <div class="fruite-img">
                <a href="{{ route('productdetails', $product->id) }}">
                    <img
                        src="{{ $image ? asset('storage/products/'.$image) : asset('website/img/no-image.png') }}"
                        class="img-fluid w-100"
                        style="height:160px; object-fit:contain;">
                </a>
            </div>

            <div class="p-3 border border-top-0">
                <div class="delivery-time mb-1">Free delivery</div>

                <form action="{{ route('add_cart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <h6 class="product-title mb-1">
                        {{ Str::limit($product->name, 40) }}
                    </h6>

                    <p class="product-unit mb-2">
                        {{ rtrim(rtrim(number_format($product->unit_value, 2), '0'), '.') }}
                        {{ optional($product->unit)->name }}
                    </p>

                    <div class="price-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="price-new">₹{{ number_format($product->final_price, 0) }}</span><br>
                            <span class="price-old">₹{{ number_format($product->mrp, 0) }}</span>
                        </div>

                        @include('website.partials.add-to-cart-btn', ['product' => $product])
                    </div>
                </form>
            </div>

        </div>
    </div>
    @empty
    <p class="text-center">No products found</p>
    @endforelse
</div>

<div class="mt-4 d-flex flex-column align-items-end">
    {{-- Pagination --}}
    {{ $products->onEachSide(0)->links() }}
</div>