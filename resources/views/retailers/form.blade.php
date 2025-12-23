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

                                        <h2 class="text-xl font-semibold mb-4">
                                            {{ isset($retailer) ? 'Edit Retailer' : 'Create Retailer' }}
                                        </h2>
                                    
                                        <div class="card-body">
                                            <form
                                                method="POST"
                                                action="{{ isset($retailer) 
                                                    ? route('retailers.update', $retailer->id) 
                                                    : route('retailers.store') }}"
                                            >
                                                @csrf
                                                @if(isset($retailer))
                                                    @method('PUT')
                                                @endif

                                                <div class="row">
                                                    <!-- Name -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text"
                                                            name="name"
                                                            class="form-control"
                                                            value="{{ old('name', $retailer->name ?? '') }}"
                                                            required>
                                                    </div>

                                                    <!-- Mobile -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mobile</label>
                                                        <input type="text"
                                                            name="mobile"
                                                            class="form-control"
                                                            value="{{ old('mobile', $retailer->mobile ?? '') }}"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Email -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email"
                                                            name="email"
                                                            class="form-control"
                                                            value="{{ old('email', $retailer->email ?? '') }}">
                                                    </div>

                                                    <!-- Address -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Address</label>
                                                        <textarea name="address"
                                                                class="form-control"
                                                                rows="3">{{ old('address', $retailer->address ?? '') }}</textarea>
                                                    </div>
                                                </div>

                                                <!-- Buttons -->
                                                <div class="mt-4 d-flex justify-content-end gap-2">
                                                    <a href="{{ route('retailers.index') }}" class="btn btn-outline-secondary">
                                                        <i class="bx bx-arrow-back"></i> Back
                                                    </a>

                                                    <button type="submit" class="btn btn-primary">
                                                        {{ isset($retailer) ? 'Update' : 'Save' }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

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


