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
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">User with Role Permissions</h4>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('Store') }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label>Role</label>
                                    <select name="role_id" id="role_id" class="form-select" required>
                                        <option value="">Select Role</option>

                                        @foreach($roles as $role)
                                        @if($role->id != 1)
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <h5 class="mt-4">Role Permissions</h5>

                            <table class="table table-bordered text-center mt-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Module</th>
                                        <th>Add</th>
                                        <th>View</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                    $modules = [
                                    'dashboard'             => 'Dashboard',
                                    'roles'                 => 'Roles',
                                    'user'                  => 'User',
                                    'role_permission'       => 'Role Permission',
                                    'brands'                => 'Brands',
                                    'category'              => 'Category',
                                    'sub_category'          => 'Sub Category',
                                    'unit'                  => 'Unit',
                                    'product'               => 'Product',
                                    'supplier'              => 'Supplier',
                                    'warehouse'             => 'Warehouse',
                                    'stock'                 => 'Stock',
                                    'batches'               => 'Batches',
                                    'warehouse_transfer'    => 'Warehouse Transfer',
                                    'disribution_center'    => 'Distribution Center / Shop',
                                    'delivery_agent'        => 'Delivery Agent',
                                    'vehical_assignment'    => 'Vehical Assignment',
                                    'offers'                => 'Offers / Coupons',
                                    'retailer_offers'       => 'Retailer Offers',
                                    ];

                                    $dashboardCards = [
                                    'total_users'       => 'Total Users',
                                    'total_products'    => 'Total Products',
                                    'total_sales'       => 'Total Sales',
                                    'total_stock'       => 'Total Stock',
                                    ];
                                    @endphp


                                    @foreach($modules as $moduleKey => $moduleLabel)
                                    <tr>
                                        <td>{{ $moduleLabel }}</td>

                                        @if($moduleKey === 'dashboard')
                                        <td colspan="4" class="text-start">
                                            @foreach($dashboardCards as $key => $label)
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="perm large-checkbox"
                                                    name="permissions[]"
                                                    value="dashboard.view.{{ $key }}"
                                                    id="dash_{{ $key }}">
                                                <label for="dash_{{ $key }}">{{ $label }}</label>
                                            </div>
                                            @endforeach
                                        </td>
                                        @else
                                        <td>
                                            <input type="checkbox" class="perm large-checkbox"
                                                name="permissions[]"
                                                value="{{ $moduleKey }}.create">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="perm large-checkbox"
                                                name="permissions[]"
                                                value="{{ $moduleKey }}.view">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="perm large-checkbox"
                                                name="permissions[]"
                                                value="{{ $moduleKey }}.edit">
                                        </td>
                                        <td>
                                            <input type="checkbox" class="perm large-checkbox"
                                                name="permissions[]"
                                                value="{{ $moduleKey }}.delete">
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>


                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Save Permissions</button>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            function loadPermissions() {
                let role_id = $("#role_id").val();

                $(".perm").prop("checked", false);

                if (!role_id) return;

                $.ajax({
                    url: "/get-role-permissions/" + role_id,
                    type: "GET",
                    success: function(response) {

                        $(".perm").prop("checked", false);

                        if (response.status && Array.isArray(response.permissions)) {

                            response.permissions.forEach(function(permission) {

                                $(`input.perm[value="${permission}"]`)
                                    .prop("checked", true);

                            });
                        }

                        // Super Admin (optional)
                        if (role_id == 2) {
                            $(".perm").prop("checked", true);
                        }
                    }

                });
            }

            loadPermissions();

            $("#role_id").change(function() {
                loadPermissions();
            });

        });
    </script>
    <!-- / Layout wrapper -->
</body>