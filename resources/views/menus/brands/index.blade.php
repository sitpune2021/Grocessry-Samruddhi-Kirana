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
                        <select id="category" name="category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- SubCategory Multi Select Dropdown -->
                    <div class="mb-3">
                        <label>SubCategory</label>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                type="button" id="subDropdown" data-bs-toggle="dropdown">
                                Select SubCategory
                            </button>

                            <div class="dropdown-menu w-100 p-2"
                                style="max-height: 200px; overflow-y: auto;"
                                id="subDropdownMenu">
                                <p class="text-muted">Select SubCategory</p>
                            </div>
                        </div>
                    </div>


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

    // ✅ Load SubCategories as checkbox
    document.getElementById('category').addEventListener('change', function() {

        let cat = categories.find(c => c.id == this.value);
        let dropdown = document.getElementById('subDropdownMenu');
        let btn = document.getElementById('subDropdown');

        dropdown.innerHTML = '';
        btn.innerText = 'Select SubCategory';

        if (cat && cat.sub_categories.length > 0) {

            dropdown.innerHTML += `
            <input type="text" class="form-control mb-2" placeholder="Search..." id="subSearch">

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllSub">
                <label class="form-check-label fw-bold">Select All</label>
            </div>
            <hr>
        `;

            cat.sub_categories.forEach(s => {
                dropdown.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input sub-checkbox"
                        type="checkbox"
                        name="subcategory_id[]"
                        value="${s.id}"
                        id="sub_${s.id}">
                    <label class="form-check-label">${s.name}</label>
                </div>
            `;
            });

        } else {
            dropdown.innerHTML = `<p class="text-danger">No subcategories found</p>`;
        }
    });


    // ✅ Select All + Count
    document.addEventListener('change', function(e) {

        // Select All
        if (e.target.id === 'selectAllSub') {
            let isChecked = e.target.checked;

            let allCheckboxes = document.querySelectorAll('.sub-checkbox');
            let dropdownBtn = document.getElementById('subDropdown');

            // Check / Uncheck all
            allCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });

            // ✅ Update dropdown text
            if (isChecked) {
                dropdownBtn.innerText = "All Selected";
            } else {
                dropdownBtn.innerText = "Select SubCategory";
            }
        }

        // Update count
        if (e.target.classList.contains('sub-checkbox')) {

            let selected = document.querySelectorAll('.sub-checkbox:checked');
            let btn = document.getElementById('subDropdown');

            btn.innerText = selected.length > 0 ?
                selected.length + " selected" :
                "Select SubCategory";
        }
    });


    // ✅ Search filter
    document.addEventListener('keyup', function(e) {
        if (e.target.id === 'subSearch') {

            let value = e.target.value.toLowerCase();

            document.querySelectorAll('#subDropdownMenu .form-check').forEach(div => {
                div.style.display = div.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        }
    });

    // ✅ Download Brand CSV
    document.getElementById('downloadBtn').addEventListener('click', function() {

        let category = document.getElementById('category').value;
        let subcategories = document.querySelectorAll('.sub-checkbox:checked');

        // ✅ Validation
        if (!category || subcategories.length === 0) {
            alert('Please select category and subcategory');
            return;
        }

        let form = document.getElementById('csvForm');
        let formData = new FormData(form);

        // ✅ Disable button
        let btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        btn.innerText = "Downloading...";

        fetch("{{ route('brands.sample-excel') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error("Failed to download");
                }
                return res.blob();
            })
            .then(blob => {

                let url = window.URL.createObjectURL(blob);
                let a = document.createElement('a');
                a.href = url;
                a.download = "brand_sample.csv";
                a.click();

                // ✅ Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('csvModal'));
                modal.hide();

                // ✅ Reset form
                form.reset();

                // Reset dropdown text (if custom UI)
                document.getElementById('subDropdown').innerText = "Select SubCategory";
            })
            .catch(err => {
                console.error(err);
                alert("Something went wrong!");
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = "Download";
            });

    });
</script>

<!-- <script>
    let selectedSubcategories = [];

    // ===============================
    // 1. Load subcategories on category change
    // ===============================
    document.getElementById('category').addEventListener('change', function() {

        let categoryId = this.value;
        alert(categoryId)
        let menu = document.getElementById('subDropdownMenu');

        selectedSubcategories = []; // reset
        menu.innerHTML = 'Loading...';

        if (!categoryId) {
            menu.innerHTML = '<p class="text-muted">Select SubCategory</p>';
            return;
        }

        fetch(`/get-subcategories/${categoryId}`)
            .then(res => res.json())
            .then(data => {

                menu.innerHTML = '';

                if (data.length === 0) {
                    menu.innerHTML = '<p class="text-danger">No Subcategories Found</p>';
                    return;
                }

                data.forEach(sub => {

                    let item = document.createElement('div');

                    item.innerHTML = `
                    <label class="d-block">
                        <input type="checkbox" class="sub-checkbox" value="${sub.id}">
                        ${sub.name}
                    </label>
                `;

                    menu.appendChild(item);
                });

            })
            .catch(err => {
                console.error(err);
                menu.innerHTML = '<p class="text-danger">Error loading data</p>';
            });
    });


    // ===============================
    // 2. Handle checkbox selection
    // ===============================
    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('sub-checkbox')) {

            let id = e.target.value;

            if (e.target.checked) {
                if (!selectedSubcategories.includes(id)) {
                    selectedSubcategories.push(id);
                }
            } else {
                selectedSubcategories = selectedSubcategories.filter(val => val !== id);
            }

            // Optional: Update button text
            document.getElementById('subDropdown').innerText =
                selectedSubcategories.length > 0 ?
                selectedSubcategories.length + ' selected' :
                'Select SubCategory';
        }
    });


    // ===============================
    // 3. Submit form & download CSV
    // ===============================
    document.getElementById('csvForm').addEventListener('submit', function(e) {

        e.preventDefault();

        let category = document.getElementById('category').value;

        if (!category) {
            alert('Please select category');
            return;
        }

        if (selectedSubcategories.length === 0) {
            alert('Please select at least one subcategory');
            return;
        }

        let form = this;

        // Remove old dynamic inputs
        document.querySelectorAll('.dynamic-sub').forEach(el => el.remove());

        // Add subcategory_id[] inputs dynamically
        selectedSubcategories.forEach(id => {

            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'subcategory_id[]';
            input.value = id;
            input.classList.add('dynamic-sub');

            form.appendChild(input);
        });

        // IMPORTANT: Set action manually (since form doesn't have one)
        form.action = '/brands.sample-excel'; // 👈 your route
        form.method = 'POST';

        form.submit(); // ✅ triggers CSV download
    });
</script> -->
@endpush