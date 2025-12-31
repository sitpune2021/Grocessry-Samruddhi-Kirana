@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Supplier</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('supplier.create') }}"
                       class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Supplier
                    </a>
                </div>

                <!-- Table -->
                <div class="table-responsive mt-5">
                    <table id="batchTable" class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">Sr No</th>
                                <th style="width: 15%;">LOGO</th>
                                <th style="width: 15%;">Name</th>
                                <th style="width: 15%;">Bill No</th>
                                <th style="width: 15%;">Challen no</th>
                                <th style="width: 15%;">Batch No</th>
                                <th style="width: 15%;">Phone</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($supplier as $index => $item)
                                <tr>
                                    <td class="text-center fw-semibold">
                                        {{ $supplier->firstItem() + $index }}
                                    </td>

                                    <td class="text-center">
                                        @if ($item->logo)
                                            <img src="{{ asset('storage/suppliers/' . $item->logo) }}" width="50"
                                                height="50" class="rounded border">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>{{ $item->supplier_name }}</td>
                                    <td>{{ $item->bill_no }}</td>
                                    <td>{{ $item->challan_no }}</td>
                                    <td>{{ $item->batch_no }}</td>

                                    <td>{{ $item->mobile }}</td>

                                    <td>
                                        <x-action-buttons :view-url="route('supplier.show', $item->id)" :edit-url="route('supplier.edit', $item->id)" :delete-url="route('supplier.destroy', $item->id)" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No suppliers found</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-3 py-2">
                    <x-pagination :from="$supplier->firstItem()" :to="$supplier->lastItem()" :total="$supplier->total()" />
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Warehouse Filter (Super Admin Only) -->
            @if(auth()->user()->role_id == 1)
            <form method="GET" action="{{ route('supplier.index') }}" class="row px-3 mb-3">
                <div class="col-md-4">
                    <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            @endif

            <!-- Table -->
            <div class="table-responsive mt-3">
                <table id="batchTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:80px;">Sr No</th>
                            <th style="width:15%;">Logo</th>
                            <th style="width:25%;">Name</th>
                            <th style="width:15%;">Phone</th>
                            <th class="text-center" style="width:150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suppliers as $index => $item)
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ $suppliers->firstItem() + $index }}
                            </td>

                            <td class="text-center">
                                @if ($item->logo)
                                    <img src="{{ asset('storage/suppliers/' . $item->logo) }}"
                                         width="50" height="50"
                                         class="rounded border">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td>{{ $item->supplier_name }}</td>
                            <td>{{ $item->mobile }}</td>

                            <td class="text-center">
                                <x-action-buttons
                                    :view-url="route('supplier.show', $item->id)"
                                    :edit-url="route('supplier.edit', $item->id)"
                                    :delete-url="route('supplier.destroy', $item->id)" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No suppliers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                <x-pagination
                    :from="$suppliers->firstItem()"
                    :to="$suppliers->lastItem()"
                    :total="$suppliers->total()" />
            </div>

        </div>
    </div>
</div>
@endsection
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
            if (row.cells.length === 1) return;
            row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
        });
    });
});
</script>
@endpush
