@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('warehouse.view');
            $canEdit = hasPermission('warehouse.edit');
            // $canDelete = hasPermission(permission: 'warehouse.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse/ Distribution Center</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    @if(hasPermission('warehouse.create'))
                    <a href="{{ route('warehouse.create') }}" class="btn btn-success">
                        Add Warehouse
                    </a>
                    @endif
                </div>
            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

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
                <table id="warehouse_id" class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <th>Sr No</th>
                        <th>Warehouse Name</th>
                        <th>Type</th>
                        <th>Address</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Status</th>
                        @if($canView || $canEdit ) {{-- || $canDelete --}}
                        <th>Actions</th>
                        @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($warehouses as $warehouse)

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->type }}</td>
                            <td>{{ $warehouse->address ?? '-' }}</td>
                            <!-- <td>{{ $warehouse->contact_person ?? '-' }}</td>
                            <td>{{ $warehouse->contact_number ?? '-'}}</td>
                            <td>{{ $warehouse->email ?? '-'}}</td> -->
                            <td>
                                @forelse($warehouse->users as $user)
                                    <div>{{ $user->first_name }} {{ $user->last_name }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>
                                @forelse($warehouse->users as $user)
                                    <div>{{ $user->mobile }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>
                                @forelse($warehouse->users as $user)
                                    <div>{{ $user->email ?? '-' }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>{{$warehouse->status ?? '-'}}</td>

                            @if($canView || $canEdit ) {{-- || $canDelete --}}
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('warehouse.view'))
                                <a href="{{ route('warehouse.show', $warehouse->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('warehouse.edit'))
                                <a href="{{ route('warehouse.edit', $warehouse->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                {{-- @if(hasPermission('warehouse.delete'))
                                <form action="{{ route('warehouse.destroy', $warehouse->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete warehouse?')" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                                </form>
                                @endif --}}
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No Warehouse found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $warehouses->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("warehouse_id");

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