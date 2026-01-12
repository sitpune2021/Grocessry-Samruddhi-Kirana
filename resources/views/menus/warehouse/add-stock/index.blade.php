@extends('layouts.app')

@section('content')
<style>
    .search-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;

    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('stock.view');
            $canEdit = hasPermission('stock.edit');
            $canDelete = hasPermission(permission: 'stock.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Stock In Warehouse</h5>
                </div>

                @if(hasPermission('stock.create'))
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('warehouse.addStockForm') }}" class="btn btn-success">
                        <i class="bx bx-plus"></i> Stock In Warehouse
                    </a>
                </div>
                @endif
            </div>

            <!-- Search + Warehouse Filter -->
            <div style="
                 display:flex;
                justify-content:space-between;
                align-items:flex-end;
                gap:5px;
                padding:0.5rem 1rem;
                flex-wrap:wrap; ">

                <form method="GET" action="{{ route('index.addStock.warehouse') }}">
                    <!-- <label>Search:</label> -->
                    <input type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search stock...">
                </form>

                <!-- Warehouse Dropdown (Super Admin only) -->
                @if(Auth::user()->role_id == 1)
                <form method="GET" action="{{ route('index.addStock.warehouse') }}">
                    <label class="form-label mb-1">Select Warehouse</label>
                    <select name="warehouse_id"
                        class="form-select"
                        onchange="this.form.submit()"
                        style="min-width:220px">
                        <option value="">-- All Warehouses --</option>
                        @foreach($warehouses as $w)
                        <option value="{{ $w->id }}"
                            {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif

            </div>



            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="stock" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr class="bg-light">
                            <th>Sr No</th>
                            <th>Warehouse</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Supplier Name</th>
                            <th>Quantity</th>
                            @if($canView || $canEdit || $canDelete)
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Warehouse --}}
                            <td>{{ $stock->warehouse->name ?? '-' }}</td>

                            {{-- Category --}}
                            <td>{{ $stock->category->name ?? '-' }}</td>

                            {{-- Product --}}
                            <td>{{ $stock->product->name ?? '-' }}</td>
                            <td>{{ $stock->supplier->supplier_name ?? '-' }}</td>
                            {{-- Quantity --}}
                            <td>
                                {{ $stock->quantity }}
                            </td>

                            {{-- Actions --}}

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('stock.view'))
                                <a href="{{ route('warehouse.viewStockForm', $stock->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('stock.edit'))
                                <a href="{{ route('warehouse.editStockForm', $stock->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('stock.delete'))
                                <form action="{{ route('stock.delete', $stock->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete stock?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No stock found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="px-3 py-2">
                {{ $stocks->onEachSide(0)->links('pagination::bootstrap-5') }}
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
            const table = document.getElementById("stock");

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