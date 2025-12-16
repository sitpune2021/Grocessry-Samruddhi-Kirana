@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Batch List</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="/batches/create" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Add New Batch
                    </a>
                </div>
            </div>

            <table id="batchTable" class="table table-bordered table-striped mt-4 mb-5">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>MFG</th>
                        <th>Expiry</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                        <tr>
                            <td>{{ $batch->product->name }}</td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>{{ $batch->mfg_date }}</td>
                            <td>{{ $batch->expiry_date }}</td>
                            <td align="center">
                                @if($batch->quantity > 0)
                                    <a href="/sale/{{ $batch->product_id }}" title="Sell Product">
                                        🛒 Sell Product
                                    </a>
                                @else
                                    ❌
                                @endif
                            </td>
                        </tr>
                    @endforeach
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
    /* Table wrapper spacing */
    #batchTable_wrapper {
        margin-top: 20px;
        margin-bottom: 40px;
    }

    /* Search input spacing */
    .dataTables_filter {
        margin-bottom: 20px;
    }

    /* Table cell padding */
    #batchTable th, #batchTable td {
        padding: 12px 15px;
    }
    </style>

    <script>
    $(document).ready(function() {
        $('#batchTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']], // Sort by Product name ascending by default
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search batches..."
            }
        });
    });
    </script>
@endpush
