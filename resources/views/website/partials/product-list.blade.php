<div class="row g-4 justify-content-center">
    @forelse($products as $product)
        <div class="col-md-6 col-lg-6 col-xl-4">
            <div class="rounded position-relative fruite-item">
                <div class="p-4 border border-secondary rounded-bottom">
                    @php
                        $images = $product->product_images; // Already array
                        $image = $images[0] ?? null;
                    @endphp

                    <div class="fruite-img">
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
                    </div>

                    <h4>{{ $product->name }}</h4>
                    <p class="text-dark fs-5 fw-bold mb-0">
                        ₹ {{ $product->mrp }}
                    </p>
                    <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>                                           
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
    </style>

    <div class="mt-4 d-flex flex-column align-items-end">

    {{ $products->links() }}

    <div class="mt-2 text-muted">
        Showing {{ $products->firstItem() }} 
        to {{ $products->lastItem() }} 
        of {{ $products->total() }} results
    </div>

</div>
