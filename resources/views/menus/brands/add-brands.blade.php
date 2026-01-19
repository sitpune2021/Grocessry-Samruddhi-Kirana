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
                            <div class="col-12">
                                <div class="card shadow-sm border-0 rounded-3">

                                    <!-- Card Header -->
                                    <div class="card-header bg-white fw-semibold">

                                        @if ($mode === 'add')
                                        <h4> Add Brand </h4>
                                        @elseif($mode === 'edit')
                                        Edit Brand
                                        @else
                                        View Brand
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($brand) ? route('brands.update', $brand->id) : route('brands.store') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if (isset($brand))
                                            @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                {{-- Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Category <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="category_id" id="category_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Category</option>

                                                            @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('category_id', $brand->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Sub Category --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Sub Category <span class="text-danger">*</span>
                                                        </label>

                                                        <select name="sub_category_id" id="sub_category_id" class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Sub Category</option>

                                                            @foreach ($subCategories as $sub)
                                                            <option value="{{ $sub->id }}"
                                                                {{ old('sub_category_id', $brand->sub_category_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                                {{ $sub->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>

                                                        @error('sub_category_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>



                                                {{-- Brand Name --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Brand Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $brand->name ?? '') }}"
                                                        placeholder="Enter brand name"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Brand Slug --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Brand Slug</label>
                                                    <input type="text" name="slug"
                                                        class="form-control @error('slug') is-invalid @enderror"
                                                        value="{{ old('slug', $brand->slug ?? '') }}"
                                                        placeholder="Auto-generated or manual"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Description --}}
                                                <!-- <div class="col-md-4">
                                                    <label class="form-label fw-medium">Description</label>
                                                    <input type="text" name="description"
                                                        class="form-control @error('description') is-invalid @enderror"
                                                        value="{{ old('description', $brand->description ?? '') }}"
                                                        placeholder="Short description"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div> -->

                                                {{-- Logo --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Brand Logo</label>
                                                    @if ($mode !== 'view')
                                                    <input type="file" name="logo"
                                                        class="form-control @error('logo') is-invalid @enderror"
                                                        accept="image/*">
                                                    @endif

                                                    @if (isset($brand) && $brand->logo)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/brands/' . $brand->logo) }}"
                                                            alt="Brand Logo" width="80" class="rounded border">
                                                    </div>
                                                    @endif

                                                    @error('logo')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Status --}}
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Status</label> <span
                                                        class="text-danger">*</span>
                                                    <select name="status" class="form-control"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="1"
                                                            {{ old('status', $brand->status ?? 1) == 1 ? 'selected' : '' }}>
                                                            Active
                                                        </option>
                                                        <option value="0"
                                                            {{ old('status', $brand->status ?? 1) == 0 ? 'selected' : '' }}>
                                                            Inactive
                                                        </option>
                                                    </select>
                                                </div>

                                            </div>

                                            {{-- Buttons --}}
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('brands.index') }}" class="btn btn-success">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>


                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Brand
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Brand
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
                    @include('layouts.footer')
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>
    <!-- / Layout wrapper -->
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');

        nameInput.addEventListener('keyup', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = generateSlug(this.value);
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.manual = true;
        });

        function generateSlug(text) {
            return text
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {

        $('#category_id').on('change', function() {
            let categoryId = $(this).val();
            let subCategorySelect = $('#sub_category_id');

            subCategorySelect.html('<option value="">Loading...</option>');

            if (!categoryId) {
                subCategorySelect.html('<option value="">Select Sub Category</option>');
                return;
            }

            $.ajax({
                url: "{{ url('get-sub-categories') }}/" + categoryId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let options = '<option value="">Select Sub Category</option>';
                    $.each(data, function(key, value) {
                        options += `<option value="${value.id}">${value.name}</option>`;
                    });
                    subCategorySelect.html(options);
                },
                error: function() {
                    subCategorySelect.html('<option value="">Select Sub Category</option>');
                }
            });
        });

    });
</script>