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
                                    <h4 class="card-header">
                                        @if($mode === 'add')
                                        Add Warehouse
                                        @elseif($mode === 'edit')
                                        Edit Warehouse
                                        @else
                                        View Warehouse
                                        @endif
                                    </h4>
                                    <div class="card-body">
                                        <form action="{{ route('warehouse.store') }}" method="POST">
                                            @csrf

                                            <div class="form-floating mb-3">
                                                <input type="text" name="name" class="form-control" placeholder="Warehouse Name" required>
                                                <label>Warehouse Name</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <select name="type" class="form-select" id="warehouseType" required>
                                                    <option value="">Select Type</option>
                                                    <option value="master">Master</option>
                                                    <option value="district">District</option>
                                                    <option value="taluka">Taluka</option>
                                                </select>
                                                <label>Warehouse Type</label>
                                            </div>

                                            <div class="form-floating mb-3" id="parentDiv">
                                                <select name="parent_id" class="form-select">
                                                    <option value="">Select Parent Warehouse</option>
                                                    @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}">{{ $w->name }} ({{ ucfirst($w->type) }})</option>
                                                    @endforeach
                                                </select>
                                                <label>Parent Warehouse</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input type="text" name="address" class="form-control" placeholder="Address">
                                                <label>Address</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input type="text" name="contact_person" class="form-control" placeholder="Contact Person">
                                                <label>Contact Person</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input type="text" name="mobile" class="form-control" placeholder="Mobile">
                                                <label>Mobile</label>
                                            </div>

                                            <!-- <h5>User Login (Optional)</h5> -->
                                            <div class="form-floating mb-3">
                                                <input type="text" name="user_name" class="form-control" placeholder="User Name">
                                                <label>User Name</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="email" name="user_email" class="form-control" placeholder="Email">
                                                <label>Email</label>
                                            </div>
                                            <!-- <div class="form-floating mb-3">
                                                <input type="password" name="user_password" class="form-control" placeholder="Password">
                                                <label>Password</label>
                                            </div> -->

                                            <button type="submit" class="btn btn-primary">Create Warehouse</button>
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