@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm p-2">
            <div class="card-datatable text-nowrap">

                @php
                    $canView = hasPermission('transfer_challan.view');
                    $canEdit = hasPermission('transfer_challan.edit');
                    $canDelete = hasPermission('transfer_challan.delete');
                @endphp

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title mb-0">Transfer Challans</h5>
                    </div>
                    <!-- @if(hasPermission('transfer_challan.create'))
                    <div class="col-md-auto ms-auto">
                        <a href="{{ route('transfer-challans.create') }}"
                            class="btn btn-success btn-sm d-flex align-items-center gap-1">
                            <i class="bx bx-plus"></i> Add Transfer Challan
                        </a>
                    </div>
                    @endif -->
                </div>

                <!-- Search -->
                <div class="px-3 pt-2">
                    <x-datatable-search />
                </div>

                <!-- Warehouse Filter (Super Admin Only) -->
                @if (auth()->user()->role_id == 1)
                    <form method="GET" action="{{ route('transfer-challans.index') }}" class="row px-3 mb-3">
                        <div class="col-md-4">
                            <select name="warehouse_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Warehouses</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    
                    {{-- <a href="{{ route('transfer-challans.download.pdf', $transferChallan->id) }}"
                        class="btn btn-sm btn-outline-danger">PDF</a>

                    <a href="{{ route('transfer-challans.download.csv', $transferChallan->id) }}"
                        class="btn btn-sm btn-outline-success">CSV</a> --}}
                @endif

                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table id="batchTable" class="table table-bordered table-striped mb-0">
                        
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:80px;">Sr No</th>
                                <th>Challan No</th>
                                <th>From Warehouse</th>
                                <th>To Warehouse</th>
                                <th>Transfer Date</th>
                               <th>Status</th> 
                                @if($canView)
                                <th class="text-center" style="width:150px;">Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($challans as $index => $item)
                                <tr>
                                    <td class="text-center fw-semibold">
                                        {{ $index + 1 }}
                                    </td>

                                    <td>{{ $item->challan_no }}</td>

                                    <td>{{ $item->fromWarehouse->name ?? '-' }}</td>

                                    <td>{{ $item->toWarehouse->name ?? '-' }}</td>

                                    <td>{{ \Carbon\Carbon::parse($item->transfer_date)->format('d-m-Y') }}</td>

                                    {{-- <td>
                                        <span class="badge 
                                            {{ $item->status == 'pending' ? 'bg-warning' : 
                                            ($item->status == 'dispatched' ? 'bg-info' : 'bg-success') }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td> --}}
                                    <!-- <td>
                                        @if(
                                            $item->status == 'pending' &&
                                            auth()->user()->warehouse_id == $item->from_warehouse_id
                                        )
                                            <form method="POST" action="{{ route('warehouse.transfer.dispatch.bulk') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="challan_id" value="{{ $item->id }}">
                                                <button class="btn btn-sm btn-success">
                                                    Dispatch
                                                </button>
                                            </form>
                                        @endif
                                    </td> -->
                                    <td>
                                        <span class="badge 
                                            {{ $item->status == 'pending' ? 'bg-warning' : 
                                            ($item->status == 'dispatched' ? 'bg-info' : 'bg-success') }}">
                                            {{ ucfirst($item->status ?? 'N/A') }}
                                        </span>                                      
                                    </td>

                                    @if($canView)
                                    <td class="text-center">
                                        <!-- <x-action-buttons :view-url="route('transfer-challans.show', $item->id)" :edit-url="route('transfer-challans.edit', $item->id)" :delete-url="route('transfer-challans.destroy', $item->id)" /> -->
                                         @if(
                                            $item->status == 'pending' &&
                                            auth()->user()->warehouse_id == $item->from_warehouse_id
                                        )
                                            <form method="POST" action="{{ route('warehouse.transfer.dispatch.bulk') }}" class="d-inline ms-2">
                                                @csrf
                                                <input type="hidden" name="challan_id" value="{{ $item->id }}">
                                                <button class="btn btn-sm btn-success">Dispatch</button>
                                            </form>
                                        @endif

                                        <a href="{{ route('transfer-challans.download.pdf', $item->id) }}"
                                            class="btn btn-sm btn-outline-danger mt-1">PDF</a>

                                        <a href="{{ route('transfer-challans.download.csv', $item->id) }}"
                                            class="btn btn-sm btn-outline-success mt-1">CSV</a>
                                            
                                    </td>
                                    @endif                                  
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No transfer challans found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-3 py-2">
                    <x-pagination :from="$challans->firstItem()" :to="$challans->lastItem()" :total="$challans->total()" />
                </div>

            </div>
        </div>
    </div>
@endsection

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
                    if (row.cells.length === 1) return;
                    row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
                });
            });
        });
    </script>
@endpush
