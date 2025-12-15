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
                                    <h5 class="card-header">Add Category</h5>
                                    <div class="card-body">
                                        <form action="{{ route('category.store') }}" method="POST">
                                            @csrf
                                            <!-- Category Name -->
                                            <div class="form-floating mb-4">
                                                <input
                                                    type="text"
                                                    name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    id="categoryName"
                                                    placeholder="Category Name">
                                                <label for="categoryName">Category Name</label>
                                                @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <!-- Category Slug / Code (optional second field) -->
                                            <div class="form-floating mb-4">
                                                <input
                                                    type="text"
                                                    name="slug"
                                                    class="form-control @error('slug') is-invalid @enderror"
                                                    id="categorySlug"
                                                    placeholder="Category Slug">
                                                <label for="categorySlug">Category Slug</label>
                                                @error('slug')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-outline-primary">
                                                    Save Category
                                                </button>
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

</html>