@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('batches.view');
            $canEdit = hasPermission('batches.edit');
            $canDelete = hasPermission('batches.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h4 class="card-title">Batch List</h4>
                </div>

                <div class="col-md-auto ms-auto d-flex gap-2">
                    @if(hasPermission('batches.create'))
                    <a href="{{ route('batches.create') }}" class="btn btn-success">
                        Add New Batch
                    </a>

                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                        data-bs-target="#bulkUploadModal">
                        Upload CSV
                    </button>
                    <!-- <a href="{{ route('brands.sample-excel') }}" class="btn btn-outline-secondary"> -->
                    <a class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#csvModal">
                        Download Csv
                    </a>
                </div>
                @endif


            </div>
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


            @php
            $isSuperAdmin = Auth::user()->role_id == 1;
            @endphp

            <div class="table-responsive p-3">
                <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.no</th>
                            <th>Product</th>
                            @if($isSuperAdmin)
                            <th>Unit</th>
                            @endif
                            @if($isSuperAdmin)
                            <th>Warehouse</th>
                            @endif
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                        <tr>
                            <td style="width: 30px;">{{ $loop->iteration }}</td>
                            <td>{{ $batch->product?->name }}</td>
                            @if($isSuperAdmin)
                            <td>{{ $batch->unit?->name }}</td>
                            @endif

                            @if($isSuperAdmin)
                            <td>{{ $batch->warehouse?->name }}</td>
                            @endif
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>{{ $batch->mfg_date }}</td>
                            <td>{{ $batch->expiry_date }}</td>

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('batches.view') && in_array(Auth::user()->role_id, [1,2]))
                                <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('batches.edit'))
                                <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('batches.delete'))
                                <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete batch?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{--<div class="px-3 py-2">
                {{ $batches->onEachSide(0)->links('pagination::bootstrap-5') }}
        </div>--}}

    </div>
</div>
</div>

<div class="modal fade" id="csvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="csvForm"
                method="POST"
                action="{{ route('batches.download.csv') }}">
                @csrf

                <div id="hiddenInputs"></div>

                <div class="modal-header">
                    <h5 class="modal-title">Download CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Warehouse -->
                    <div class="mb-3">
                        <label>Warehouse</label>
                        <select id="warehouse_id" name="warehouse_id" class="form-control">
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label>Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <!-- SubCategory Dropdown -->
                    <div class="dropdown mb-3">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                            Select SubCategory
                        </button>

                        <div class="dropdown-menu w-100 p-2"
                            style="max-height:200px; overflow-y:auto;"
                            id="subcategoryDropdownMenu">

                            <p class="text-muted mb-2">Select SubCategory</p>

                        </div>
                    </div>

                    <!-- Product Dropdown -->
                    <div class="dropdown mb-3">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown">
                            Select Product
                        </button>

                        <div class="dropdown-menu w-100 p-2"
                            style="max-height:200px; overflow-y:auto;"
                            id="productDropdownMenu">

                            <p class="text-muted">Select Product</p>

                        </div>
                    </div>

                    <!-- Unit -->
                    <!-- <div class="mb-3">
                        <label>Unit</label>
                        <select id="unit_id" name="unit_id" class="form-control">
                            <option value="">Select Unit</option>
                        </select>
                    </div> -->

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Download CSV
                    </button>
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
                <h5 class="modal-title">Upload Product Batch CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('product-batches.bulk-upload') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-body">

                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            CSV File <span class="text-danger">*</span>
                        </label>

                        <input type="file"
                            name="csv_file"
                            class="form-control"
                            accept=".csv"
                            required>
                    </div>

                    <div class="alert alert-info">
                        <strong>CSV Format:</strong><br>

                        Warehouse, Category, SubCategory, Product,
                        Unit, Batch No, Quantity, MFG Date, Expiry Date
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        Upload
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection


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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- ////////////// -->
<script>
    $(document).ready(function() {

        let selectedSubCategories = [];
        let selectedProducts = [];

        // =========================
        // CATEGORY LOAD
        // =========================
        $('#warehouse_id').on('change', function() {

            let warehouseId = $(this).val();

            $.ajax({
                url: '/ws/categories/' + warehouseId,
                type: 'GET',
                success: function(data) {

                    let options = '<option value="">Select Category</option>';

                    $.each(data, function(i, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });

                    $('#category_id').html(options);
                }
            });
        });

        // =========================
        // SUBCATEGORY LOAD
        // =========================
        $('#category_id').on('change', function() {

            let warehouseId = $('#warehouse_id').val();
            let categoryId = $(this).val();

            $.ajax({
                url: '/ws/subcategories/' + warehouseId + '/' + categoryId,
                type: 'GET',
                success: function(data) {

                    let html = `
                <div class="form-check">
                    <input type="checkbox" id="selectAllSubcat">
                    <label><b>Select All</b></label>
                </div><hr>`;

                    $.each(data, function(i, item) {
                        html += `
                    <label class="d-block">
                        <input type="checkbox" class="subcat" value="${item.id}">
                        ${item.name}
                    </label>`;
                    });

                    $('#subcategoryDropdownMenu').html(html);

                    selectedSubCategories = [];
                    selectedProducts = [];
                    $('#productDropdownMenu').html('Select SubCategory first');
                }
            });
        });

        // =========================
        // SUBCATEGORY SELECT
        // =========================
        $(document).on('change', '.subcat', function() {

            selectedSubCategories = [];

            $('.subcat:checked').each(function() {
                selectedSubCategories.push($(this).val());
            });

            updateHidden('sub_category_id[]', selectedSubCategories);

            loadProducts();
        });

        // =========================
        // SELECT ALL SUBCATEGORY
        // =========================
        $(document).on('change', '#selectAllSubcat', function() {

            $('.subcat').prop('checked', this.checked).trigger('change');
        });

        // =========================
        // LOAD PRODUCTS
        // =========================
        function loadProducts() {

            let warehouseId = $('#warehouse_id').val();

            if (selectedSubCategories.length === 0) return;

            $.ajax({
                url: '/ws/products-by-sub/' + warehouseId + '/' + selectedSubCategories.join(','),
                type: 'GET',
                success: function(data) {

                    let html = `
                <div class="form-check">
                    <input type="checkbox" id="selectAllProducts">
                    <label><b>Select All</b></label>
                </div><hr>`;

                    $.each(data, function(i, item) {
                        html += `
                    <label class="d-block">
                        <input type="checkbox" class="product" value="${item.id}">
                        ${item.name}
                    </label>`;
                    });

                    $('#productDropdownMenu').html(html);

                    selectedProducts = [];
                }
            });
        }

        // =========================
        // PRODUCT SELECT
        // =========================
        $(document).on('change', '.product', function() {

            selectedProducts = [];

            $('.product:checked').each(function() {
                selectedProducts.push($(this).val());
            });
            console.log(selectedProducts);
            updateHidden('product_id[]', selectedProducts);
        });

        // =========================
        // SELECT ALL PRODUCTS
        // =========================
        $(document).on('change', '#selectAllProducts', function() {

            $('.product').prop('checked', this.checked).trigger('change');
        });

        // =========================
        // UPDATE HIDDEN INPUTS
        // =========================
        function updateHidden(name, values) {

            $('#hiddenInputs').find('input[name="' + name + '"]').remove();

            values.forEach(id => {
                $('#hiddenInputs').append(
                    `<input type="hidden" name="${name}" value="${id}">`
                );
            });
        }
    });
</script>