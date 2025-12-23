@include('layouts.header')

<body>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                @include('layouts.sidebar')
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                @include('layouts.navbar')
                <!-- / Navbar -->

                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <div class="col-12">

                            <div class="card mb-4" style="max-width:1200px;margin:auto;">
                                <h4 class="card-header">
                                    @if ($mode === 'add')
                                        Add Batch
                                    @elseif ($mode === 'edit')
                                        Edit Batch
                                    @else
                                        View Batch
                                    @endif
                                </h4>

                                <div class="card-body">
                                    <form method="POST"
                                        action="{{ $mode === 'edit' ? route('batches.update', $batch->id) : ($mode === 'add' ? route('batches.store') : '#') }}"
                                        {{ $mode === 'view' ? 'onsubmit=return false;' : '' }}>

                                        @csrf
                                        @if ($mode === 'edit')
                                            @method('PUT')
                                        @endif

                                        <div class="row g-3 mb-3">

                                            {{-- Category --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                                <select name="category_id" id="category_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id', $batch->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Product --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                                <select name="product_id" id="product_id" class="form-select"
                                                    {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    <option value="">Select Product</option>
                                                    @if (isset($products))
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ old('product_id', $batch->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('product_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror

                                                {{-- View / Edit → Product Image --}}
                                                @if (isset($batch->product->image))
                                                    @php
                                                        $imgUrl = asset('storage/products/' . $batch->product->image);
                                                    @endphp

                                                    @if ($mode === 'view')
                                                        <a href="{{ $imgUrl }}" target="_blank"
                                                            class="d-block mt-2 text-primary text-decoration-underline">
                                                            View Product Image
                                                        </a>
                                                    @elseif ($mode === 'edit')
                                                        <a href="{{ $imgUrl }}" target="_blank">
                                                            <img src="{{ $imgUrl }}" width="80"
                                                                class="rounded border mt-2">
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Batch Number --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Batch Number <span class="text-danger">*</span></label>
                                                <input type="text" name="batch_no"
                                                    value="{{ old('batch_no', $batch->batch_no ?? '') }}"
                                                    class="form-control" placeholder="Enter batch no"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('batch_no')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Quantity --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                                <input type="number" name="quantity" min="1"
                                                    value="{{ old('quantity', $batch->quantity ?? '') }}"
                                                    class="form-control"  placeholder="Enter quantity"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('quantity')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- MFG --}}
                                            <div class="col-md-4">
                                                <label class="form-label">MFG Date<span class="text-danger">*</span></label>
                                                <input type="date" name="mfg_date"
                                                    value="{{ old('mfg_date', $batch->mfg_date ?? '') }}"
                                                    class="form-control"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('mfg_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            {{-- Expiry --}}
                                            <div class="col-md-4">
                                                <label class="form-label">Expiry Date<span class="text-danger">*</span></label>
                                                <input type="date" name="expiry_date"
                                                    value="{{ old('expiry_date', $batch->expiry_date ?? '') }}"
                                                    class="form-control " 
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                @error('expiry_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                        </div>

                                        {{-- Buttons --}}
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary">
                                                Back
                                            </a>

                                            @if ($mode !== 'view')
                                                <button type="submit" class="btn btn-primary">
                                                    {{ $mode === 'edit' ? 'Update Batch' : 'Save Batch' }}
                                                </button>
                                            @endif
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- / Content -->

            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#category_id').change(function() {
        let categoryId = $(this).val();
        $('#product_id').html('<option value="">Loading...</option>');

        if (categoryId) {
            $.ajax({
                url: '/get-products/' + categoryId,
                type: 'GET',
                success: function(products) {
                    let options = '<option value="">-- Select Product --</option>';
                    products.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });
                    $('#product_id').html(options);

                    @if (isset($batch))
                        $('#product_id').val('{{ $batch->product_id }}');
                    @endif
                }
            });
        } else {
            $('#product_id').html('<option value="">-- Select Product --</option>');
        }
    });
</script>
