@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-datatable text-nowrap">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">Product</h5>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <a href="{{ route('product.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Add Product
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <x-datatable-search />

                <!-- Table -->
                <div class="table-responsive mt-5">
                    <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                        <thead class="table-light">
                            <tr>
                                <th>Sr No</th>
                                <th>Image</th>

                                <th>Category</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Description</th>
                                <th>Base Price</th>
                                <th>Retailer Price</th>
                                <th>MRP</th>
                                <th>GST (%)</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($products as $index => $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    {{-- Product Image --}}
                                    <td>
                                        @if (!empty($product->product_images))
                                            @php
                                                $images = json_decode($product->product_images, true);
                                                $image = $images[0] ?? null;
                                            @endphp

                                            @if ($image)
                                                <img src="{{ asset('storage/products/' . $image) }}" alt="Product Image"
                                                    width="60" height="60" class="rounded border">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>

                                    {{-- Category Name --}}
                                    <td>{{ $product->category->name ?? '-' }}</td>

                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku ?? '-' }}</td>
                                    <td>{{ $product->description ?? '-' }}</td>
                                    <td>₹ {{ number_format($product->base_price, 2) }}</td>
                                    <td>₹ {{ number_format($product->retailer_price, 2) }}</td>
                                    <td>₹{{ number_format($product->mrp, 2) }}</td>
                                    <td>{{ $product->gst_percentage ?? '-' }}</td>
                                    <td>{{ $product->stock ?? '-' }}</td>



                                    {{-- Actions --}}
                                    <td>
                                        <x-action-buttons :view-url="route('product.show', $product->id)" :edit-url="route('product.edit', $product->id)" :delete-url="route('product.destroy', $product->id)" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">
                                        No Products found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <x-pagination :from="$products->firstItem()" :to="$products->lastItem()" :total="$products->total()" />

            </div>

            <!-- Search -->

         

            <!-- Pagination -->

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

    <!-- table search box script -->

    @push('scripts')
        <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const searchInput = document.getElementById("dt-search-1");
                const table = document.getElementById("batchTable");

                if (!searchInput || !table) return;

                const rows = table.querySelectorAll("tbody tr");

                searchInput.addEventListener("keyup", function() {
                    const value = this.value.toLowerCase().trim();

                    rows.forEach(row => {

                        // Skip "No role found" row
                        if (row.cells.length === 1) return;

                        row.style.display = row.textContent
                            .toLowerCase()
                            .includes(value) ?
                            "" :
                            "none";
                    });
                });

            });
        </script>

    @endpush
