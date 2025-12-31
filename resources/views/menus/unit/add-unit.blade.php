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
                                        Add Unit
                                        @elseif($mode === 'edit')
                                        Edit Unit
                                        @else
                                        View Unit
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form
                                            action="{{ $mode === 'edit' ? route('units.update', $units->id) : route('units.store') }}"
                                            method="POST">
                                            @csrf

                                            @if ($mode === 'edit')
                                            @method('PUT')
                                            @endif

                                            <!-- Inputs side by side -->
                                            <div class="row g-3">

                                                <!-- Parent Category -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Unit Name <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="name"
                                                        class="form-control"
                                                        value="{{ old('name', $units->name ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Sub Category Name -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-medium">
                                                        Short Name<span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text"
                                                        name="short_name"
                                                        class="form-control"
                                                        value="{{ old('short_name', $units->short_name ?? '') }}"
                                                        {{ $mode === 'view' ? 'disabled' : '' }}>

                                                    @error('short_name')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Buttons (Right Aligned) -->
                                            <div class="mt-4 d-flex justify-content-end gap-2">
                                                <a href="{{ route('units.index') }}" class="btn btn-success">
                                                    <i class="bx bx-arrow-back"></i> Back
                                                </a>

                                                @if ($mode === 'add')
                                                <button type="submit" class="btn btn-success">
                                                    Save Unit
                                                </button>
                                                @elseif ($mode === 'edit')
                                                <button type="submit" class="btn btn-success">
                                                    Update Unit
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