@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-datatable text-nowrap">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">User</h5>
                    </div>

                    <div class="col-md-auto ms-auto d-flex gap-2">
                        <div class="col-md-auto ms-auto d-flex gap-2">



                            <!-- Add User Button -->
                            <a href="{{ route('user.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Add User
                            </a>
                        </div>
                    </div>


                    <!-- Search -->
                    <x-datatable-search />

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
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                                        <td>{{ $user->role->name ?? '-' }}</td>

                                        <td class= "text-primary">{{ $user->status == 1 ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <x-action-buttons :view-url="route('user.show', $user->id)" :edit-url="route('user.edit', $user->id)" :delete-url="route('user.destroy', $user->id)" />
                                        </td>
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
                    <x-pagination :from="$users->firstItem()" :to="$users->lastItem()" :total="$users->total()" />

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
