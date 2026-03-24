@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('brands.view');
            $canEdit = hasPermission('brands.edit');
            $canDelete = hasPermission('brands.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h4 class="card-title mb-0">Brands</h4>
                </div>

                <div class="col-md-auto ms-auto d-flex gap-2">
                    @if (hasPermission('brands.create'))
                    <a href="{{ route('brands.create') }}" class="btn btn-success">
                        Add Brands
                    </a>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                        data-bs-target="#bulkUploadModal">
                        Upload CSV
                    </button>
                    <!-- <a href="{{ route('brands.sample-excel') }}" class="btn btn-outline-secondary"> -->
                    <a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#csvModal">
                        Download Csv
                    </a>
                    @endif
                </div>
            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

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
            <div class="table-responsive mt-5">
                <table id="batchTable" class="table table-bordered table-striped mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 15%;">Logo</th>
                            <th style="width: 30%;">Brand Name</th>
                            <th style="width: 40%;">Slug</th>
                            <th class="text-center" style="width: 120px;">Status</th>

                            @if ($canView || $canEdit || $canDelete)
                            <th style="width: 150px;">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($brands as $index => $brand)
                        <tr>

                            {{-- Sr No --}}
                            <td class="text-center fw-semibold">
                                {{ $brands->firstItem() + $index }}
                            </td>

                            {{-- Logo --}}
                            <td class="text-center">
                                @if ($brand->logo)
                                <img src="{{ asset('storage/brands/' . $brand->logo) }}"
                                    alt="{{ $brand->name }}" width="50" height="50"
                                    class="rounded border">
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Brand Name --}}
                            <td>
                                <span class="fw-medium">{{ $brand->name }}</span>
                            </td>

                            {{-- Slug --}}
                            <td class="text-muted">
                                {{ $brand->slug }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <form action="{{ route('updateStatus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $brand->id }}">

                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            onchange="this.form.submit()" {{ $brand->status ? 'checked' : '' }}>
                                    </div>
                                </form>
                            </td>

                            {{-- Actions --}}
                            @if ($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">

                                @if ($canView)
                                <a href="{{ route('brands.show', $brand->id) }}"
                                    class="btn btn-sm btn-primary">View</a>
                                @endif

                                @if ($canEdit)
                                <a href="{{ route('brands.edit', $brand->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                @endif

                                @if ($canDelete)
                                <form action="{{ route('brands.destroy', $brand->id) }}" method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Delete brand?')"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                No brands found
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $brands->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>


        </div>
    </div>

</div>

<div class="modal fade" id="csvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="csvForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Download CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Category -->
                    <div class="mb-3">
                        <label>Category</label>
                        <select id="category" name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- SubCategory -->
                    <div class="mb-3">
                        <label>SubCategory</label>
                        <select id="subcategory" name="subcategory_id" class="form-control" required>
                            <option value="">Select SubCategory</option>
                        </select>
                    </div>

                    <!-- Brand -->
                    <!-- <div class="mb-3">
                        <label>Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" required>
                    </div> -->

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="downloadBtn">Download</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Upload Brands Csv</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('brands.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">CSV File <span
                                class="text-danger">*</span></label>
                        <input type="file" name="excel_file" class="form-control" accept=".csv" required>
                        <!-- <small class="text-muted">Only .xlsx, .xls, .csv allowed. Max 5MB.</small> -->
                    </div>

                    <!-- <div class="alert alert-info py-2 mb-0">
                        <small>
                            <strong>Format:</strong> Category Name | Sub Category Name | Brand Name | Logo URL<br>
                            <a href="{{ route('brands.sample-excel') }}" class="text-decoration-underline">Download
                                Sample</a>
                        </small>
                    </div> -->

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

    document.getElementById('category').addEventListener('change', function() {
        let categoryId = this.value;
        let subDropdown = document.getElementById('subcategory');

        subDropdown.innerHTML = '<option value="">Select SubCategory</option>';

        let selected = categories.find(c => c.id == categoryId);

        if (selected) {
            selected.sub_categories.forEach(sub => {
                let option = `<option value="${sub.id}">${sub.name}</option>`;
                subDropdown.innerHTML += option;
            });
        }
    });

    document.getElementById('downloadBtn').addEventListener('click', function() {

        let form = document.getElementById('csvForm');
        let formData = new FormData(form);

        fetch("{{ route('brands.sample-excel') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {

                // ✅ Download file
                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = "brand_sample.csv";
                document.body.appendChild(a);
                a.click();
                a.remove();

                // ✅ Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('csvModal'));
                modal.hide();

                // ✅ Reset form (optional)
                form.reset();

            })
            .catch(err => {
                console.error(err);
                alert('Download failed');
            });

    });
</script>


@endpush