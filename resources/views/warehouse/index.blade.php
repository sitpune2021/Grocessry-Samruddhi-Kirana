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
<!-- Search -->
            <x-datatable-search />
            
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
                        <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
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



<script>
    $(document).ready(function() {
        $('#transfersTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'desc']
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search transfers..."
            }
        });
    });
</script>