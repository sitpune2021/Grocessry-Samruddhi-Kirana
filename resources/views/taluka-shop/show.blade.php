@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#f5f6fa; min-height:100vh;">

    <div class="container">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">

                <h3 class="mb-4">Taluka to Shop Transfer Details</h3>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Product</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->product->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Batch Number</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->batch->batch_no ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Transferred Quantity</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->quantity }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>From Warehouse</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->fromWarehouse->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>To Warehouse</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->toWarehouse->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>MFG Date</label>
                        <input type="text" class="form-control"
                            value="{{ optional($transfer->batch)->mfg_date }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Expiry Date</label>
                        <input type="text" class="form-control"
                            value="{{ optional($transfer->batch)->expiry_date }}" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Transfer Date</label>
                        <input type="text" class="form-control"
                            value="{{ $transfer->created_at->format('d-m-Y H:i') }}" readonly>
                    </div>

                </div>

                <div class="mt-4">
                    <a href="{{ route('district-taluka-transfer.index') }}"
                        class="btn btn-secondary">
                        ⬅ Back
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection