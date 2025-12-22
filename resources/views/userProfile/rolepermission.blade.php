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
                                    $dashboardCards = [
                                    'Total Users',
                                    'Total Products',
                                    'Total Sales',
                                    'Total Stock'
                                    ];
                                    @endphp

                                    @foreach([
                                    'Dashboard','Roles','User','RolePermission','Brands','Category',
                                    'Product','Batches','Warehouse','AddStock','Sale',
                                    'Warehousetransfer'
                                    ] as $module)



                                    <tr>
                                        <td>{{ $module }}</td>

                                        <input type="hidden" name="permissions[{{ $module }}][]" value="">

                                        {{-- Dashboard → card-wise view permissions --}}
                                        @if($module == 'Dashboard')
                                        <td colspan="4" class="text-start">
                                            @foreach($dashboardCards as $card)
                                            <label class="me-4">
                                                <input type="checkbox"
                                                    class="perm large-checkbox"
                                                    name="permissions[Dashboard][]"
                                                    value="view_{{ strtolower($card) }}">
                                            <label class="me-4 ms-2">
                                                View {{ $card }}
                                            </label>
                                            @endforeach
                                        </td>
                                        @else
                                        <td><input type="checkbox" class="perm large-checkbox" name="permissions[{{ $module }}][]" value="add"></td>
                                        <td><input type="checkbox" class="perm large-checkbox" name="permissions[{{ $module }}][]" value="view"></td>
                                        <td><input type="checkbox" class="perm large-checkbox" name="permissions[{{ $module }}][]" value="edit"></td>
                                        <td><input type="checkbox" class="perm large-checkbox" name="permissions[{{ $module }}][]" value="delete"></td>
                                        @endif

                                    </tr>

                                    @endforeach
                                </tbody>

                            </table>


                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Save Permissions</button>
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

                        if (role_id == 2) {
                            $(".perm").prop("checked", true);
                        }

                        if (response.status) {
                            let permissions = response.permissions;

                            Object.keys(permissions).forEach(function(module) {
                                permissions[module].forEach(function(action) {
                                    $(`input[name="permissions[${module}][]"][value="${action}"]`)
                                        .prop("checked", true);
                                });
                            });
                        }

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