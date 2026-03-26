@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
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

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h5 class="mt-4">Role Permissions</h5>
                        <div id="select_all_box">
                            <label for="check_all" class="form-check-label p-2">Select All</label>
                            <input type="checkbox" class="perm large-checkbox" id="check_all">
                        </div>
                    </div>

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
                            'dashboard' => 'Dashboard',
                            'roles' => 'Roles',
                            'user' => 'User',
                            'role_permission' => 'Permission',
                            'brands' => 'Brands',
                            'category' => 'Category',
                            'sub_category' => 'Sub Category',
                            'unit' => 'Unit',
                            'product' => 'Product',
                            'supplier' => 'Supplier',
                            'supplier_challan' => 'Supplier Challan',
                            'warehouse' => 'Warehouse',
                            'stock' => 'Stock',
                            'batches' => 'Batches',
                            'warehouse_transfer_request' => 'Warehouse Transfer Request',
                            // 'disribution_center' => 'Distribution Center / Shop',
                            'delivery_agent' => 'Delivery Agent',
                            'vehical_assignment' => 'Vehical Assignment',
                            // 'offers' => 'Offers',
                            'coupons' => 'Coupons',
                            'retailer_offers' => 'Retailer Offers',
                            //'website_orders' => 'Website Orders',
                            'customer_orders' => 'Customer Orders',
                            'transfer_challan' => 'Transfer Challan',
                            'banners' => 'Banners',
                            ];

                            $dashboardCards = [
                            'total_users' => 'Total Users',
                            'total_products' => 'Total Products',
                            'total_sales' => 'Total Sales',
                            'total_stock' => 'Total Stock',
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

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-success">Save Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        $("#select_all_box").hide(); // hide initially

        $("#role_id").change(function() {

            let selectedRole = $("#role_id option:selected").text().trim();

            if (selectedRole.toLowerCase() === "master admin") {
                $("#select_all_box").show();
            } else {
                $("#select_all_box").hide();
                $("#check_all").prop("checked", false);
            }

        });

    });


    $("#check_all").change(function() {
        $("input.perm:not(#check_all)").prop("checked", $(this).prop("checked"));
    });

    $(".perm").change(function() {

        if (!$(this).is(":checked")) {
            $("#check_all").prop("checked", false);
        } else if ($(".perm:not(:checked)").length === 0) {
            $("#check_all").prop("checked", true);
        }

    });

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
@endpush