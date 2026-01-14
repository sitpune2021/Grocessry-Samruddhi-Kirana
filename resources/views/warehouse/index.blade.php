@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable">
            @php
            $canView = hasPermission('warehouse_transfer_request.view');
            $canEdit = hasPermission('warehouse_transfer_request.edit');
            $canDelete = hasPermission('warehouse_transfer_request.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse Stock Transfers</h5>
                </div>
                @if(hasPermission('warehouse_transfer_request.create') && Auth::user()->role_id != 1)
                <div class="col-md-auto ms-auto mt-5">
                    <a href="{{ route('transfer.create') }}" class="btn btn-success">
                        Request Stock
                    </a>
                </div>
                @endif
            </div><br><br>
            <!-- Search -->
            <x-datatable-search />
            <div class="table-responsive mt-3">
                <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <!-- <th>Approved By Warehouse</th> -->
                            <th>Requested By Warehouse</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Status</th>
                            @if($canView || $canEdit || $canDelete)
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <!-- <td>{{ $t->approvedByWarehouse->name ?? '' }}</td> -->
                            <td>{{ $t->requestedByWarehouse->name  ?? '' }}</td>
                            <td>{{ $t->category->name }}</td>
                            <td>{{ $t->product->name }}</td>
                            <td>{{ $t->batch->batch_no ?? '' }}</td>
                            <td>{{ $t->quantity }}</td>
                            <td>{{ $t->created_at->format('d-m-Y') }}</td>
                            <td>
                                @if ($t->status == 1)
                                <span class="badge bg-success">Approved</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if($canView)
                                <a href="{{ route('transfer.show', $t->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if($canEdit)
                                <a href="{{ route('transfer.edit', $t->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if($canDelete)
                                <form action="{{ route('transfer.destroy', $t->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete batch?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No transfers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    $(document).ready(function() {
        $('#transfersTable').DataTable({
            scrollX: true, // ✅ REQUIRED for wide tables
            autoWidth: false, // ✅ REQUIRED
            pageLength: 10,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                    targets: -1,
                    orderable: false
                } // Action column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search transfers..."
            }
        });
    });
</script>

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("transfersTable");

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