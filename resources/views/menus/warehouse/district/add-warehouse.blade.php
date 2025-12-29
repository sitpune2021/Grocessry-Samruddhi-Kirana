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
                        <div class="row g-6">

                            <!-- Form controls -->
                            <div class="col-md-6">
                                <div class="card">
                                    <h4 class="card-header">
                                        @if($mode === 'add')
                                        Add Product
                                        @elseif($mode === 'edit')
                                        Edit Product
                                        @else
                                        View Product
                                        @endif
                                    </h4>
                                    <div class="card-body">
                                        <form
                                            action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}"
                                            enctype="multipart/form-data"
                                            method="POST">
                                            @csrf
                                            @if(isset($product))
                                            @method('PUT')
                                            @endif

                                            {{-- Category --}}
                                            <div class="form-floating mb-4">
                                                <select
                                                    name="category_id"
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
                                                <label>Category</label>
                                                @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Product Name --}}
                                            <div class="form-floating mb-4">
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $product->name ?? '') }}"
                                                    placeholder="Product Name"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                <label>Product Name</label>
                                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            {{-- SKU --}}
                                            <div class="form-floating mb-4">
                                                <input type="text" name="sku"
                                                    class="form-control @error('sku') is-invalid @enderror"
                                                    value="{{ old('sku', $product->sku ?? '') }}"
                                                    placeholder="SKU"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>
                                                <label>SKU</label>
                                                @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            {{-- Description --}}
                                            <div class="form-floating mb-4">
                                                <textarea name="description"
                                                    class="form-control @error('description') is-invalid @enderror"
                                                    style="height:120px"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>{{ old('description', $product->description ?? '') }}</textarea>
                                                <label>Description</label>
                                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            {{-- Prices --}}
                                            <div class="row">
                                                <div class="col-md-4 form-floating mb-4">
                                                    <input type="number" step="0.01" name="base_price"
                                                        class="form-control @error('base_price') is-invalid @enderror"
                                                        value="{{ old('base_price', $product->base_price ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    <label>Base Price</label>
                                                </div>

                                                <div class="col-md-4 form-floating mb-4">
                                                    <input type="number" step="0.01" name="retailer_price"
                                                        class="form-control @error('retailer_price') is-invalid @enderror"
                                                        value="{{ old('retailer_price', $product->retailer_price ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    <label>Retailer Price</label>
                                                </div>

                                                <div class="col-md-4 form-floating mb-4">
                                                    <input type="number" step="0.01" name="mrp"
                                                        class="form-control @error('mrp') is-invalid @enderror"
                                                        value="{{ old('mrp', $product->mrp ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    <label>MRP</label>
                                                </div>
                                            </div>

                                            {{-- GST & Stock --}}
                                            <div class="row">
                                                <div class="col-md-6 form-floating mb-4">
                                                    <input type="number" step="0.01" name="gst_percentage"
                                                        class="form-control @error('gst_percentage') is-invalid @enderror"
                                                        value="{{ old('gst_percentage', $product->gst_percentage ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    <label>GST (%)</label>
                                                </div>

                                                <div class="col-md-6 form-floating mb-4">
                                                    <input type="number" name="stock"
                                                        class="form-control @error('stock') is-invalid @enderror"
                                                        value="{{ old('stock', $product->stock ?? '') }}"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    <label>Stock</label>
                                                </div>
                                            </div>

                                            {{-- Product Images (URLs / strings) --}}
                                            <div class="mb-4">
                                                <label class="form-label">Product Image</label>

                                                <input type="file"
                                                    name="product_image"
                                                    class="form-control @error('product_image') is-invalid @enderror"
                                                    {{ $mode === 'view' ? 'readonly' : '' }}>

                                                @error('product_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                {{-- Show existing image (edit/view) --}}
                                                @if(!empty($product->product_image))
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/products/'.$product->product_image) }}"
                                                        width="120"
                                                        class="rounded border">
                                                </div>
                                                @endif
                                            </div>

                                            {{-- Buttons --}}
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('product.index') }}" class="btn btn-success">
                                                    Back
                                                </a>

                                                @if($mode === 'add')
                                                <button type="submit" class="btn btn-success">Save Product</button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">Update Product</button>
                                                @endif
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