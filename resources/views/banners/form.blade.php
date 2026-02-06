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
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row justify-content-center">
                        <!-- Form card -->
                        <div class="col-12 col-md-10 col-lg-12">
                            <div class="card mb-4">

                                <h4 class="card-header text-center">
                                    {{ $banner ? 'Edit Banner' : 'Create Banner' }}
                                </h4>

                                <div class="card-body">

                                    <form
                                        action="{{ $banner ? route('banners.update', $banner->id) : route('banners.store') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label>Banner Name</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', $banner->name ?? '') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Redirect Product (Optional)</label>
                                            <select name="product_id" class="form-control">
                                                <option value="">-- Select Product --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ old('product_id', $banner->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Banner Image</label>
                                            <input type="file" name="image" class="form-control"
                                                {{ $banner ? '' : 'required' }}>
                                        </div>

                                        @if ($banner)
                                            <div class="mb-3">
                                                <img src="{{ asset('storage/' . $banner->image) }}" width="200">
                                            </div>
                                        @endif

                                        <a href="{{ route('banners.index') }}" class="btn btn-success">Back</a>

                                        <button type="submit" class="btn btn-success">
                                            {{ $banner ? 'Update Banner' : 'Save Banner' }}
                                        </button>
                                    </form>

                                    @if ($errors->any())
                                        <div class="alert alert-danger mt-3">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
</body>
