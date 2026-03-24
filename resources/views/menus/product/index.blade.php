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
                    <h4 class="card-title">Product</45>
                </div>

                @if (hasPermission('product.create'))
                <div class="col-md-auto ms-auto d-flex gap-2"">
                            <a href=" {{ route('product.create') }}" class="btn btn-success">
                    Add Product
                    </a>

                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#productBulkUploadModal">
                        Upload CSV
                    </button>
                    <a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#csvModal">
                        Download CSV</a>
                </div>
                @endif
            </div>

            <!-- Search -->
            <x-datatable-search />

            @if (session('success'))
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
                            @if ($canView || $canEdit || $canDelete)
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
                            @if ($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if (hasPermission('product.view'))
                                <a href="{{ route('product.show', $product->id) }}"
                                    class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if (hasPermission('product.edit'))
                                <a href="{{ route('product.edit', $product->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if (hasPermission('product.delete'))
                                <form action="{{ route('product.destroy', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete product?')"
                                        class="btn btn-sm btn-danger">
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

<!-- bulk download model -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#csvModal">
    Download CSV
</button>

<div class="modal fade" id="csvModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="csvForm">
                @csrf

                <div class="modal-header">
                    <h5>Download Product CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Category -->
                    <select id="category" name="category_id" class="form-control mb-2" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <!-- SubCategory -->
                    <select id="subcategory" name="subcategory_id" class="form-control mb-2" required>
                        <option value="">Select SubCategory</option>
                    </select>

                    <!-- Brand -->
                    <select id="brand" name="brand_id" class="form-control mb-2" required>
                        <option value="">Select Brand</option>
                    </select>

                    <!-- UNIT -->
                    <select id="unit" name="unit" class="form-control mb-2" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>

                    <!-- GST -->
                    <select id="gst" name="gst" class="form-control mb-2" required>
                        <option value="">Select GST</option>
                        @foreach($taxes as $tax)
                        <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->percentage }}%)</option>
                        @endforeach
                    </select>

                </div>

                <div class="modal-footer">
                    <button type="button" id="downloadBtn" class="btn btn-success">
                        Download CSV
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- Product Bulk Upload Modal -->
<div class="modal fade" id="productBulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Products Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('product.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Excel / CSV File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Only .xlsx, .xls, .csv allowed. Max 5MB.</small>
                    </div>
                    <div class="alert alert-info py-2 mb-0">
                        <small>
                            <strong>Format:</strong> Category | Sub Category | Brand | Product Name | Barcode |
                            Description | Unit | Unit Value | Base Price | Selling Price | MRP | GST | Image URL<br>
                            <a href="{{ route('product.sample-excel') }}" class="text-decoration-underline">Download
                                Sample</a>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
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
<script>
    const categories = @json($categories);

    // Load SubCategory
    document.getElementById('category').addEventListener('change', function() {
        let cat = categories.find(c => c.id == this.value);
        let sub = document.getElementById('subcategory');

        sub.innerHTML = '<option value="">Select SubCategory</option>';

        if (cat) {
            cat.sub_categories.forEach(s => {
                sub.innerHTML += `<option value="${s.id}">${s.name}</option>`;
            });
        }
    });

    // Load Brand
    document.getElementById('subcategory').addEventListener('change', function() {
        let cat = categories.find(c => c.id == document.getElementById('category').value);
        let brand = document.getElementById('brand');

        brand.innerHTML = '<option value="">Select Brand</option>';

        if (cat) {
            let sub = cat.sub_categories.find(s => s.id == this.value);
            if (sub) {
                sub.brands.forEach(b => {
                    brand.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                });
            }
        }
    });

    // Download CSV + Close Modal
    document.getElementById('downloadBtn').addEventListener('click', function() {

        let category = document.getElementById('category').value;
        let subcategory = document.getElementById('subcategory').value;
        let brand = document.getElementById('brand').value;
        let unit = document.getElementById('unit').value; // ✅ added
        let gst = document.getElementById('gst').value;

        if (!category || !subcategory || !brand || !unit || !gst) {
            alert('Please select all fields including GST');
            return;
        }

        let form = document.getElementById('csvForm');
        let formData = new FormData(form);

        fetch("{{ route('product.sample-excel') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(async res => {

                // ❗ Handle validation / error response
                if (!res.ok) {
                    let text = await res.text();
                    alert("Error: " + text);
                    throw new Error("Download failed");
                }

                return res.blob();
            })
            .then(blob => {

                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = "product_sample.csv";
                document.body.appendChild(a);
                a.click();
                a.remove();

                // ✅ Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('csvModal'));
                modal.hide();
            })
            .catch(err => console.error(err));
    });
</script>


@endpush