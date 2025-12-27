@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse Transfers</h5>
                </div>
                <div class="col-md-auto ms-auto mt-5">
                    <a href="{{route('transfer.create')}}" class="btn btn-primary">
                        Transfer Stock
                    </a>
                </div>
            </div><br><br>
            <!-- Search -->
            <x-datatable-search />
            <div class="table-responsive mt-3">
                <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>From Warehouse</th>
                            <th>To Warehouse</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->fromWarehouse->name }}</td>
                            <td>{{ $t->toWarehouse->name }}</td>
                            <td>{{ $t->category->name }}</td>
                            <td>{{ $t->product->name }}</td>
                            <td>{{ $t->batch->batch_no  ?? ''}}</td>
                            <td>{{ $t->quantity }}</td>
                            <td>{{ $t->created_at->format('d-m-Y') }}</td>
                            <td class="action-column" style="white-space:nowrap;">
                                <x-action-buttons
                                    :view-url="route('transfer.show', $t->id)"
                                    :edit-url="route('transfer.edit', $t->id)"
                                    :delete-url="route('transfer.destroy', $t->id)" />
                            </td>
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