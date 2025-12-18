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
                                        @if($mode === 'add')
                                        Add Product
                                        @elseif($mode === 'edit')
                                        Edit Product
                                        @else
                                        View Product
                                        @endif
                                    </div>

                                    <!-- Card Body -->
                                    <div class="card-body">
                                        <form
                                            action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}"
                                            enctype="multipart/form-data"
                                            method="POST">
                                            @csrf
                                            @if(isset($product))
                                            @method('PUT')
                                            @endif

                                            <div class="row">

                                                {{-- Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Category <span class="mandatory">*</span></label>
                                                        <select name="category_id"
                                                            class="form-select @error('category_id') is-invalid @enderror"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Category</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                {{-- Product Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name <span class="mandatory">*</span></label>
                                                        <input type="text" name="name"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            value="{{ old('name', $product->name ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                {{-- SKU --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">SKU</label>
                                                        <input type="text" name="sku"
                                                            class="form-control @error('sku') is-invalid @enderror"
                                                            value="{{ old('sku', $product->sku ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                        @error('sku') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                {{-- Description --}}
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description"
                                                            class="form-control @error('description') is-invalid @enderror"
                                                            rows="3"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('description', $product->description ?? '') }}</textarea>
                                                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>

                                                {{-- Prices --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Base Price</label>
                                                        <input type="number" step="0.01" name="base_price"
                                                            class="form-control"
                                                            value="{{ old('base_price', $product->base_price ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Retailer Price</label>
                                                        <input type="number" step="0.01" name="retailer_price"
                                                            class="form-control"
                                                            value="{{ old('retailer_price', $product->retailer_price ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">MRP</label>
                                                        <input type="number" step="0.01" name="mrp"
                                                            class="form-control"
                                                            value="{{ old('mrp', $product->mrp ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- GST & Stock --}}
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">GST (%)</label>
                                                        <input type="number" step="0.01" name="gst_percentage"
                                                            class="form-control"
                                                            value="{{ old('gst_percentage', $product->gst_percentage ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Stock</label>
                                                        <input type="number" name="stock"
                                                            class="form-control"
                                                            value="{{ old('stock', $product->stock ?? '') }}"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- Product Image --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Image</label>
                                                        <input type="file" name="product_image"
                                                            class="form-control"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                    </div>

                                                    @if(!empty($product->product_image))
                                                    <img src="{{ asset('storage/products/'.$product->product_image) }}"
                                                        width="120" class="rounded border">
                                                    @endif
                                                </div>

                                                <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('product.index') }}" class="btn btn-outline-secondary">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if($mode === 'add')
                                                <button type="submit" class="btn btn-primary">
                                                     Save Product
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-primary">
                                                     Update Product
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
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>