@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('user.view');
            $canEdit = hasPermission('user.edit');
            $canDelete = hasPermission('user.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">User</h5>
                </div>

                <div class="col-md-auto ms-auto d-flex gap-2">
                    <div class="col-md-auto ms-auto d-flex gap-2">



                        <!-- Add User Button -->
                        <a href="{{ route('user.create') }}" class="btn btn-success">
                            <i class="bx bx-plus"></i> Add User
                        </a>
                    </div>
                </div>


                <!-- Search -->
                <x-datatable-search />

                @if(session('success'))
                <div id="successAlert"
                    class="alert alert-success alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                    role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <script>
                    setTimeout(function() {
                        let alert = document.getElementById('successAlert');
                        if (alert) {
                            let bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }
                    }, 10000); // 15 seconds
                </script>
                @endif
                
                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table id="batchTable" class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Sr No</th>
                                <th>Logo</th>
                                <th>User Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Warehouse</th>
                                <th>Role</th>
                                <th>Status</th>
                                @if($canView || $canEdit || $canDelete )
                                <th>Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($user->profile_photo)
                                    <a href="{{ asset('storage/' . $user->profile_photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                            width="50">
                                    </a>
                                    @endif
                                </td>

                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->mobile ?? '-' }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>{{ $user->warehouse->name ?? '-' }}</td>
                                <td>{{ $user->role->name ?? '-' }}</td>

                                <td class="text-primary">{{ $user->status == 1 ? 'Yes' : 'No' }}</td>

                                @if($canView || $canEdit || $canDelete )
                                <td class="text-center" style="white-space:nowrap;">
                                    @if(hasPermission('user.view'))
                                    <a href="{{ route('user.show', $user->id) }}" class="btn btn-sm btn-primary">View</a>
                                    @endif
                                    @if(hasPermission('user.edit'))
                                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endif
                                    @if(hasPermission('user.delete'))
                                    <form action="{{ route('user.destroy', $user->id)}}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete User ?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No user found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-3 py-2">
                    {{ $users->onEachSide(0)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    @endsection

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const searchInput = document.getElementById("dt-search-1");
            const table = document.getElementById("batchTable");

            if (!searchInput || !table) return;

            const rows = table.querySelectorAll("tbody tr");

            searchInput.addEventListener("keyup", function() {
                const value = this.value.toLowerCase().trim();

                rows.forEach(row => {

                    // Skip "No role found" row
                    if (row.cells.length === 1) return;

                    row.style.display = row.textContent
                        .toLowerCase()
                        .includes(value) ?
                        "" :
                        "none";
                });
            });

        });
    </script>

    <!-- table search box script -->

    @push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const searchInput = document.getElementById("dt-search-1");
            const table = document.getElementById("batchTable");

            if (!searchInput || !table) return;

            const rows = table.querySelectorAll("tbody tr");

            searchInput.addEventListener("keyup", function() {
                const value = this.value.toLowerCase().trim();

                rows.forEach(row => {

                    // Skip "No role found" row
                    if (row.cells.length === 1) return;

                    row.style.display = row.textContent
                        .toLowerCase()
                        .includes(value) ?
                        "" :
                        "none";
                });
            });

        });
    </script>
    @endpush