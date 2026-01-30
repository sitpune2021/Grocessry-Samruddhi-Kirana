@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    @php
    $canView = hasPermission('product.view');
    $canEdit = hasPermission('product.edit');
    $canDelete = hasPermission('product.delete');
    @endphp

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Product</h5>
                </div>

                @if(hasPermission('product.create'))
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('product.create') }}" class="btn btn-success">
                        Add Product
                    </a>
                </div>
                @endif
            </div>

            <!-- Search -->
            <x-datatable-search />

            @if(session('success'))
            <div id="successAlert"
                class="alert alert-success alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <script>
                setTimeout(function() {
                    let alert = document.getElementById('successAlert');
                    if (alert) {
                        let bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 10000); // 15 seconds
            </script>
            @endif

            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th>Sr No</th>
                            <th>Image</th>

                            <th>Category</th>
                            <th>Product Name</th>

                            <th>Base Price</th>
                            <th>MRP</th>
                            <th>Net Price</th>
                            <th>GST (%)</th>
                            <!-- <th>Stock</th> -->
                            @if($canView || $canEdit || $canDelete)
                            <th>Actions</th>
                            @endif
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
                                $images = $product->product_images; // Already array
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

                            <td>₹ {{ number_format($product->base_price, 2) }}</td>
                            <td>₹{{ number_format($product->mrp, 2) }}</td>
                            <td>₹ {{ number_format($product->final_price, 2) }}</td>
                            <td>{{ $product?->tax?->gst ?? '-' }}</td>
                            <!-- <td>{{ $product->stock ?? '-' }}</td> -->
                            {{-- Actions --}}
                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('product.view'))
                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('product.edit'))
                                <a href="{{route('product.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('product.delete'))
                                <form action="{{ route('product.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete product?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
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

            <div class="px-3 py-2">
                {{ $products->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>
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