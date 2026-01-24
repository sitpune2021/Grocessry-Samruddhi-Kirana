@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Role</h5>
                </div>

                <div class="col-md-auto ms-auto d-flex gap-2">
                    <div class="col-md-auto ms-auto d-flex gap-2">
                        <!-- Add Role Button -->
                        <a href="{{ route('roles.create') }}" class="btn btn-success">
                            <i class="bx bx-plus"></i> Add Role
                        </a>
                    </div>
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
            <div class="table-responsive mt-3 p-3">
                <table id="batchTable" class="table table-bordered table-striped">

                    <thead class="">
                        <tr class="bg-light">
                            <th>Sr No</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                        // 🔹 Descending Sr No calculation with pagination
                        $srNo = $roles->total() - ($roles->currentPage() - 1) * $roles->perPage();
                        @endphp

                        @forelse ($roles as $role)
                        <tr>
                            <td>{{ $srNo-- }}</td> <!-- Descending Sr No -->
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description ?? '-' }}</td>
                           
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('roles.show'))
                                <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('roles.edit'))
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('user.delete'))
                                <form action="{{ route('roles.destroy', $role->id)}}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete User ?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No role found</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $roles->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection

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