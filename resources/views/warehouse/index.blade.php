@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse Transfers</h5>
                </div>
                <div class="col-md-auto ms-auto mt-5">
                    <a href="/warehouse-transfer" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Transfer Stock
                    </a>
                </div>
            </div><br><br>

            <table id="transfersTable" class="table table-bordered table-striped mt-4 mb-5">
                <thead>
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
                            <td>{{ $t->batch->batch_no }}</td>
                            <td>{{ $t->quantity }}</td>
                            <td>{{ $t->created_at->format('d-m-Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <!-- EDIT -->
                                    <a href="{{ route('transfer.edit', $t->id) }}" 
                                    class="btn btn-sm btn-primary" title="Edit">
                                        ✏️
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('transfer.destroy', $t->id) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Are you sure?')" 
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Delete">
                                            🗑️
                                        </button>
                                    </form>

                                </div>
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

@endsection

@push('scripts')
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
/* Space above and below table */
#transfersTable_wrapper {
    margin-top: 20px;
    margin-bottom: 40px;
}

/* Search input spacing */
.dataTables_filter {
    margin-bottom: 25px;
}

#transfersTable th, #transfersTable td {
    padding: 12px 15px; /* Table cell padding */
}
</style>

<script>
$(document).ready(function() {
    $('#transfersTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search transfers..."
        }
    });
});
</script>
@endpush

