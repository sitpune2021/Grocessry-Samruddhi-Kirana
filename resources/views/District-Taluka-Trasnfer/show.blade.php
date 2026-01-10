@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background:#f5f6fa; min-height:100vh;">

    <div class="container">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">

                <h5 class="mb-4 fw-semibold">
                    District To Taluka Warehouse Transfers
                </h5>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->product->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->batch->batch_no ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Transferred Quantity</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->quantity }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">From Warehouse</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->fromWarehouse->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">To Warehouse</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->toWarehouse->name ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">MFG Date</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->batch->mfg_date ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->batch->expiry_date ?? '-' }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Transfer Date</label>
                        <input type="text" class="form-control"
                               value="{{ $transfer->created_at->format('d-m-Y H:i') }}" readonly>
                    </div>

                </div>

                <div class="mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-success">
                        ← Back
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
