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
            <x-datatable-search />

            <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Qty</th>
                        <th>MFG</th>
                        <th>Expiry</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr>
                        <td>{{ $batch->product->name }}</td>
                        <td>{{ $batch->batch_no }}</td>
                        <td>
                            <span class="{{ $batch->quantity > 0 ? 'success' : 'danger' }}">
                                {{ $batch->quantity }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($batch->mfg_date)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('d-m-Y') }}</td>
                        <td class="text-center">
                            @if($batch->quantity > 0)
                            <a href="/sale/{{ $batch->product_id }}"
                                class="btn btn-sm btn-primary"
                                title="Sell Product">
                                <i class="bx bx-cart"></i> Sell
                            </a>
                            @else
                            <span class="text-danger fw-bold">Out of Stock</span>
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