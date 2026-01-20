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
                                        <h4>Add Category</h4>
                                        @elseif($mode === 'edit')
                                        <h4>Edit Category</h4>
                                        @else
                                        <h4>View Category</h4>
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($category) ? route('category.update', $category->id) : route('category.store') }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if (isset($category))
                                            @method('PUT')
                                            @endif

                                            <div class="row g-3">

                                                <!-- Category Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Category Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name', $category->name ?? '') }}"
                                                        placeholder="Enter category name"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Category Slug -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Category Slug <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="slug" class="form-control"
                                                        value="{{ old('slug', $category->slug ?? '') }}"
                                                        placeholder="auto-generated or manual"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('slug')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- Images --}}
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Category Images <span class="text-danger">*</span></label>

                                                    @if ($mode !== 'view')
                                                    <input type="file" name="category_images[]" multiple
                                                        class="form-control @error('category_images') is-invalid @enderror">
                                                    @endif

                                                    @error('category_images')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror

                                                    @if (!empty($category->category_images))
                                                    @php
                                                    $images = $category->category_images; // Already array
                                                    $image = $images[0] ?? null;
                                                    @endphp

                                                    @if ($image)
                                                    <img src="{{ asset('storage/categories/' . $image) }}" alt="Category Image"
                                                        width="60" height="60" class="rounded border">
                                                    @else
                                                    <span class="text-muted">No Image</span>
                                                    @endif
                                                    @else
                                                    <span class="text-muted">No Image</span>
                                                    @endif
                                                </div>

                                            </div>

                                            <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('category.index') }}"
                                                    class="btn btn-success">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Category
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Category
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

        if (nameInput && slugInput) {
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
        }
    });
</script>