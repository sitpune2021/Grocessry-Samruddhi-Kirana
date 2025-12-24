@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Add Warehouse
                    </a>
                </div>
            </div>

            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <div class="table-responsive mt-3 p-3">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                       <thead class="table-light">
                            <th>Sr No</th>
                            <th>Warehouse Name</th>
                            <th>Address</th>
                            <th>Contact Person</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($warehouses as $warehouse)

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->address ?? '-' }}</td>
                            <td>{{ $warehouse->contact_person ?? '-' }}</td>
                            <td>{{ $warehouse->contact_number ?? '-'}}</td>
                            <td>{{ $warehouse->email ?? '-'}}</td>
                            <td>{{$warehouse->status ?? '-'}}</td>
                            <td class="action-column" style="white-space:nowrap;">
                                <x-action-buttons
                                    :view-url="route('warehouse.show', $warehouse->id)"
                                    :edit-url="route('warehouse.edit', $warehouse->id)"
                                    :delete-url="route('warehouse.destroy', $warehouse->id)" />
                            </td>
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
            <x-pagination
                :from="$warehouses->firstItem()"
                :to="$warehouses->lastItem()"
                :total="$warehouses->total()" />

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
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("dt-search-1");
    const table = document.getElementById("batchTable");

    if (!searchInput || !table) return;

    const rows = table.querySelectorAll("tbody tr");

    searchInput.addEventListener("keyup", function () {
        const value = this.value.toLowerCase().trim();

        rows.forEach(row => {

            // Skip "No role found" row
            if (row.cells.length === 1) return;

            row.style.display = row.textContent
                .toLowerCase()
                .includes(value)
                ? ""
                : "none";
        });
    });

});
</script>