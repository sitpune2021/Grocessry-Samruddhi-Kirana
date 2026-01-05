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
                                        Add Tax
                                        @elseif($mode === 'edit')
                                        Edit Tax
                                        @else
                                        View Tax
                                        @endif
                                    </div>

                                    <div class="card-body">
                                        <form action="{{ route('taxes.store') }}" method="POST">
                                            @csrf
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="name" class="form-label">Tax Name</label>
                                                    <input type="text" name="name" id="name" class="form-control" placeholder="GST 5%" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="is_active" class="form-label">Status</label>
                                                    <select name="is_active" id="is_active" class="form-control" required>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="cgst" class="form-label">CGST (%)</label>
                                                    <input type="number" step="0.01" name="cgst" id="cgst" class="form-control" placeholder="2.5" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="sgst" class="form-label">SGST (%)</label>
                                                    <input type="number" step="0.01" name="sgst" id="sgst" class="form-control" placeholder="2.5" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="igst" class="form-label">IGST (%)</label>
                                                    <input type="number" step="0.01" name="igst" id="igst" class="form-control" placeholder="5" required>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-success">Save Tax</button>
                                            <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Back</a>
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