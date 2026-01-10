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
                                        <i class="bx bx-category me-1"></i>
                                        @if ($mode === 'add')
                                        Add Sub Category
                                        @elseif($mode === 'edit')
                                        Edit Sub Category
                                        @else
                                        View Sub Category
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($subCategory) ? route('sub-category.update', $subCategory->id) : route('sub-category.store') }}"
                                            method="POST">
                                            @csrf

                                            @if (isset($subCategory))
                                            @method('PUT')
                                            @endif

                                            <!-- Inputs side by side -->
                                            <div class="row g-3">

                                                <!-- Parent Category -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Parent Category <span class="text-danger">*</span>
                                                    </label>

                                                    <select name="category_id" id="category_id" class="form-select" {{ $mode === 'view' ? 'disabled' : '' }}>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            {{ old('category_id', $subCategory->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                            {{ $cat->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                    @error('category_id')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Sub Category Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Sub Category Name <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="name"
                                                        id="name"
                                                        class="form-control"
                                                        value="{{ old('name', $subCategory->name ?? '') }}"
                                                        placeholder="Enter sub category name"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Sub Category Slug -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Sub Category Slug <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="slug"
                                                        id="slug"
                                                        class="form-control"
                                                        value="{{ old('slug', $subCategory->slug ?? '') }}"
                                                        placeholder="auto-generated or manual"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>

                                                    @error('slug')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>

                                            <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('sub-category.index') }}" class="btn btn-success">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Sub Category
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Sub Category
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