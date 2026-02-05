<div class="row g-4">
    @forelse($products as $product)
    <div class="col-md-6 col-lg-3">
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
                    <img
                        src="{{ asset('storage/products/'.$image) }}"
                        class="img-fluid w-100 rounded-top"
                        alt="{{ $product->name }}"
                        style="height: 200px; object-fit: cover;">
                    @else
                    <img
                        src="{{ asset('website/img/no-image.png') }}"
                        class="img-fluid w-100 rounded-top"
                        alt="No Image"
                        style="height: 200px; object-fit: cover;">
                    @endif
                </a>
            </div>

            <div class="p-4 border border-top-0">

                <!-- <div class="delivery-time mb-1">Free delivery</div> -->

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
    </div>
    @empty
    <p class="text-center">No products found</p>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-end">
    {{ $products->links() }}
</div>
</div>

<div class="mt-4 d-flex flex-column align-items-end">
    {{-- Pagination --}}
    {{ $products->onEachSide(0)->links() }}
</div>