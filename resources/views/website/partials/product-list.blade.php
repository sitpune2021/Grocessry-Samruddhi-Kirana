<div class="row g-4 justify-content-center">
    @forelse($products as $product)
    <div class="col-6 col-md-4 col-lg-3 col-xl-3">
        <div class="rounded position-relative fruite-item">
            <div class="p-4 border border-secondary rounded-bottom">
                @php
                $images = $product->product_images; // Already array
                $image = $images[0] ?? null;
                @endphp

                <div class="fruite-img">
                    <a href="{{ route('productdetails', $product->id) }}">
                        @if($image)
                        <img
                            src="{{ asset('storage/products/'.$image) }}"
                            class="img-fluid w-100 rounded-top"
                            alt="{{ $product->name }}"
                            style="height: 160px; object-fit: cover;">
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

                    <div class="delivery-time mb-1">Free delivery</div>

                    <form action="{{ route('add_cart') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- DISCOUNT --}}
                        @if($product->mrp > $product->final_price)
                        @php
                        $discount = round((($product->mrp - $product->final_price) / $product->mrp) * 100);
                        @endphp

                        <div class="offer-badge">{{ $discount }}% OFF</div>
                        @endif
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

                            <button type="submit" class="btn-add-sm">ADD</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    @empty
    <p class="text-center">No products found</p>
    @endforelse
</div>



<style>
    /* FORCE pagination to horizontal row */
    .pagination {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between;
        gap: 6px;
    }

    .pagination .page-item {
        display: inline-flex !important;
    }

    .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fruite-item .p-4 {
        padding: 12px !important;
    }

    .product-title {
        font-size: 14px;
        line-height: 1.3;
    }

    .product-unit {
        font-size: 12px;
        margin-bottom: 6px;
    }

    .price-new {
        font-size: 15px;
        font-weight: 600;
    }

    .price-old {
        font-size: 12px;
    }
</style>

<div class="mt-4 d-flex flex-column align-items-end">

    {{ $products->links() }}

    <!-- <div class="mt-2 text-muted">
        {{ $products->firstItem() }}
        {{ $products->lastItem() }}
        {{ $products->total() }}
    </div> -->

</div>