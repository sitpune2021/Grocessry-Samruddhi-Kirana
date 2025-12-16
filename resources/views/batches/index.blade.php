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

            <!-- Search -->
            <x-datatable-search />

            <table class="table table-bordered">

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
                                        🛒 SEll PRODUCT
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
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush