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
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center">
                                        <h4 class="mb-0 flex-grow-1">
                                            @if($mode === 'add')
                                            Add Warehouse
                                            @elseif($mode === 'edit')
                                            Edit Warehouse
                                            @else
                                            View Warehouse
                                            @endif
                                        </h4>
                                    </div>

                                    <div class="card-body">
                                        <form action="{{ route('warehouse.store') }}" method="POST">
                                            @csrf

                                            <div class="row">

                                                {{-- Warehouse Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse Name <span class="mandatory">*</span></label>
                                                        <input type="text" name="name"
                                                            class="form-control"
                                                            placeholder="Warehouse Name"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- Warehouse Type --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Warehouse Type <span class="mandatory">*</span></label>
                                                        <select name="type"
                                                            class="form-select"
                                                            id="warehouseType"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Type</option>
                                                            <option value="master">Master</option>
                                                            <option value="district">District</option>
                                                            <option value="taluka">Taluka</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Parent Warehouse --}}
                                                <div class="col-md-4" id="parentDiv">
                                                    <div class="mb-3">
                                                        <label class="form-label">Parent Warehouse</label>
                                                        <select name="parent_id"
                                                            class="form-select"
                                                            {{ $mode === 'view' ? 'disabled' : '' }}>
                                                            <option value="">Select Parent Warehouse</option>
                                                            @foreach($warehouses as $w)
                                                            <option value="{{ $w->id }}">
                                                                {{ $w->name }} ({{ ucfirst($w->type) }})
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Address --}}
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Address</label>
                                                        <textarea name="address"
                                                            class="form-control"
                                                            rows="2"
                                                            placeholder="Address"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}></textarea>
                                                    </div>
                                                </div>

                                                {{-- Contact Person --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Contact Person</label>
                                                        <input type="text" name="contact_person"
                                                            class="form-control"
                                                            placeholder="Contact Person"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mobile</label>
                                                        <input type="text" name="mobile"
                                                            class="form-control"
                                                            maxlength="10"
                                                            placeholder="Mobile"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- User Name --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">User Name</label>
                                                        <input type="text" name="user_name"
                                                            class="form-control"
                                                            placeholder="User Name"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
                                                    </div>
                                                </div>

                                                {{-- User Email --}}
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="user_email"
                                                            class="form-control"
                                                            placeholder="Email"
                                                            {{ $mode === 'view' ? 'readonly' : '' }}>
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

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
    const typeSelect = document.getElementById('warehouseType');
    const parentDiv = document.getElementById('parentDiv');

    function toggleParent() {
        if (typeSelect.value === 'master') {
            parentDiv.style.display = 'none';
        } else {
            parentDiv.style.display = 'block';
        }
    }

    typeSelect.addEventListener('change', toggleParent);
    toggleParent();
</script>