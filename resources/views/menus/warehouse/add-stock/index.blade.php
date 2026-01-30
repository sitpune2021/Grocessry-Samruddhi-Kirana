@extends('layouts.app')

@section('content')


    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card p-2">
            <div class="card-datatable text-nowrap">
                @php
                    $canView = hasPermission('stock.view');
                    $canEdit = hasPermission('stock.edit');
                    $canDelete = hasPermission('stock.delete');
                @endphp

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">Stock In Warehouse</h5>
                    </div>


                    <div class="col-md-auto ms-auto">
                        @if (hasPermission('stock.create'))
                            <a href="{{ route('warehouse.addStockForm') }}" class="btn btn-success">
                                <i class="bx bx-plus"></i> Stock In Warehouse
                            </a>
                        @endif
                    </div>

                </div>

                <!-- Search + Warehouse Filter -->

                <x-datatable-search />
                @if (session('success'))
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

                <div class=" justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-4">

                    @if (Auth::user()->role_id == 1)
                        <form method="GET" action="{{ route('index.addStock.warehouse') }}">
                            <!-- <label class="form-label mb-1">Select Warehouse</label> -->
                            <select name="warehouse_id" class="form-select" onchange="this.form.submit()"
                                style="min-width:220px">
                                <option value="">-- All Warehouses --</option>
                                @foreach ($warehouses as $w)
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
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">SR NO</th>
                                    <th>Warehouse</th>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th style="width:120px">Quantity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $sr = 1; @endphp

                                @forelse ($stocks as $warehouseId => $warehouseStocks)
                                    {{-- Warehouse Header --}}
                                    <tr class="table-secondary warehouse-row" onclick="toggleWarehouse({{ $warehouseId }})"
                                        style="cursor:pointer">
                                        <td>{{ $sr++ }}</td>
                                        <td colspan="4">
                                            <i class="bx bx-chevron-right me-1 toggle-icon"
                                                id="icon-{{ $warehouseId }}"></i>
                                            <strong>{{ $warehouseStocks->first()->warehouse->name }}</strong>
                                        </td>
                                    </tr>

                                    {{-- Child Rows --}}
                                    @foreach ($warehouseStocks as $stock)
                                        <tr class="warehouse-child warehouse-{{ $warehouseId }}" style="display:none">
                                            <td></td>
                                            <td></td>
                                            <td>{{ $stock->category->name }}</td>
                                            <td>{{ $stock->product->name }}</td>
                                            <td>{{ $stock->quantity }}</td>
                                        </tr>
                                    @endforeach

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No stock found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        {{-- </div> --}}

                    </div>

                    {{-- <div class="px-3 py-2">
                        {{ $stocks->onEachSide(0)->links('pagination::bootstrap-5') }}
                    </div> --}}

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
            <script>
                function toggleWarehouse(id) {

                    const rows = document.querySelectorAll('.warehouse-' + id);
                    const icon = document.getElementById('icon-' + id);

                    let isHidden = rows[0].style.display === 'none';

                    rows.forEach(row => {
                        row.style.display = isHidden ? 'table-row' : 'none';
                    });

                    icon.classList.toggle('bx-chevron-right');
                    icon.classList.toggle('bx-chevron-down');
                }
            </script>
        @endpush



        {{-- Actions --}}
        {{-- 
                            @if ($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if (hasPermission('stock.view'))
                                <a href="{{ route('warehouse.viewStockForm', $stock->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if (hasPermission('stock.edit'))
                                <a href="{{ route('warehouse.editStockForm', $stock->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if (hasPermission('stock.delete'))
                                <form action="{{ route('stock.delete', $stock->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete stock?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif --}}
