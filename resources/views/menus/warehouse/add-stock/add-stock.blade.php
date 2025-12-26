@include('layouts.header')

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>

            <div class="layout-page">

                @include('layouts.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row justify-content-center">
                            <div class="col-12">

                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">
                                        <i class="bx bx-box me-1"></i>
                                        @if ($mode === 'add')
                                            Add Warehouse Stock
                                        @elseif($mode === 'edit')
                                            Edit Stock
                                        @else
                                            View Stock
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form
                                            action="{{ $mode === 'edit' ? route('stock.update', $warehouse_stock->id) : route('warehouse.addStock') }}"
                                            enctype="multipart/form-data" method="POST">
                                            @csrf
                                            @if ($mode === 'edit')
                                                @method('PUT')
                                            @endif

                                            <div class="row">
                                                {{-- Warehouse --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse <span
                                                                class="text-danger">*</span></label>
                                                        <select  id= "warehouse_id" name="warehouse_id" class="form-select "
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                                            <option value="">Select Warehouse</option>

                                                            @foreach ($warehouses as $warehouse)
                                                                <option value="{{ $warehouse->id }}"
                                                                    {{ old('warehouse_id', $warehouse_stock->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>

                                                                    {{ $warehouse->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        @error('warehouse_id')
                                                            <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Category <span class="text-danger">
                                                                *</span></label>
                                                        <select name="category_id" id="category_id" class="form-select "
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                                            <option value="">Select Category</option>

                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ old('category_id', $warehouse_stock->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        @error('category_id')
                                                            <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="product_id">Product <span
                                                                class="text-danger">*</span></label>

                                                        <select name="product_id" id="product_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>

                                                            <option value="">-- Select Product --</option>

                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}"
                                                                    {{ old('product_id', $warehouse_stock->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        @error('product_id')
                                                            <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                {{-- Batch --}}

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="batch_id">Batch</label>
                                                        <select name="batch_id" id="batch_id" class="form-control"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">-- Select Batch No --</option>
                                                            @foreach ($product_batches as $product_batch)
                                                                <option value="{{ $product_batch->id }}"
                                                                    {{ old('batch_id', $warehouse_stock->batch_id ?? '') == $product_batch->id ? 'selected' : '' }}>
                                                                    {{ $product_batch->batch_no }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                          @error('batch_id')
                                                            <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Prices --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" step="0.01" name="quantity"
                                                            class="form-control"
                                                            value="{{ old('quantity', $warehouse_stock->quantity ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}
                                                            placeholder="Quantity">
                                                             @error('quantity')
                                                            <span class="text-danger mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                <!-- Buttons (Right Aligned) -->
                                                <div class="mt-4 d-flex justify-content-end gap-2">
                                                    <a href="{{ route('index.addStock.warehouse') }}"
                                                        class="btn btn-outline-secondary">
                                                        <i class="bx bx-arrow-back"></i> Back
                                                    </a>
                                                    @if ($mode === 'add')
                                                        <button type="submit" class="btn btn-primary">
                                                            Save Stock
                                                        </button>
                                                    @elseif($mode === 'edit')
                                                        <button type="submit" class="btn btn-primary">
                                                            Update Stock
                                                        </button>
                                                    @endif
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- / Content -->
                    @include('layouts.footer')
                </div>
            </div>
        </div>

    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const categorySelect = document.getElementById('category_id');
        const productSelect = document.getElementById('product_id');

        categorySelect.addEventListener('change', function() {

            const categoryId = this.value;
            productSelect.innerHTML = '<option value="">Loading...</option>';

            if (!categoryId) {
                productSelect.innerHTML = '<option value="">-- Select Product --</option>';
                return;
            }

            fetch(`/get-products-by-category/${categoryId}`)
                .then(res => res.json())
                .then(data => {

                    productSelect.innerHTML = '<option value="">-- Select Product --</option>';

                    if (data.length === 0) {
                        productSelect.innerHTML +=
                            '<option value="">No products found</option>';
                    }

                    data.forEach(product => {
                        productSelect.innerHTML += `
                        <option value="${product.id}">
                            ${product.name}
                        </option>`;
                    });
                })
                .catch(() => {
                    productSelect.innerHTML =
                        '<option value="">Error loading products</option>';
                });
        });

    });
</script>
