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
                                        @if($mode === 'add')
                                        Add Category
                                        @elseif($mode === 'edit')
                                        Edit Category
                                        @else
                                        View Category
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ isset($category) ? route('category.update', $category->id) : route('category.store') }}"
                                            method="POST">
                                            @csrf
                                            @if(isset($category))
                                            @method('PUT')
                                            @endif

                                            <!-- Inputs side by side -->
                                            <div class="row g-3">

                                                <!-- Category Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Category Name</label>
                                                    <input
                                                        type="text"
                                                        name="name"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $category->name ?? '') }}"
                                                        placeholder="Enter category name"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Category Slug -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">Category Slug</label>
                                                    <input
                                                        type="text"
                                                        name="slug"
                                                        class="form-control @error('slug') is-invalid @enderror"
                                                        value="{{ old('slug', $category->slug ?? '') }}"
                                                        placeholder="auto-generated or manual"
                                                        {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            </div>

                                            <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('category.index') }}" class="btn btn-outline-secondary">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if($mode === 'add')
                                                <button type="submit" class="btn btn-primary">
                                                    Save Category
                                                </button>
                                                @elseif($mode === 'edit')
                                                <button type="submit" class="btn btn-primary">
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