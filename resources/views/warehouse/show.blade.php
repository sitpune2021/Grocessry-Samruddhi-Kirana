@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Warehouse Transfer Details</h3>

    <table border="1" cellpadding="10" cellspacing="0" width="60%">

        <tr>
            <th>Product</th>
            <td>{{ $transfer->product->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>Batch Number</th>
            <td>{{ $transfer->batch->batch_no ?? '-' }}</td>
        </tr>

        <tr>
            <th>Transferred Quantity</th>
            <td>{{ $transfer->quantity }}</td>
        </tr>

        <tr>
            <th>From Warehouse</th>
            <td>{{ $transfer->fromWarehouse->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>To Warehouse</th>
            <td>{{ $transfer->toWarehouse->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>MFG Date</th>
            <td>{{ $transfer->batch->mfg_date ?? '-' }}</td>
        </tr>

        <tr>
            <th>Expiry Date</th>
            <td>{{ $transfer->batch->expiry_date ?? '-' }}</td>
        </tr>

        <tr>
            <th>Transfer Date</th>
            <td>{{ $transfer->created_at }}</td>
        </tr>

    </table>

    <br>
    <a href="{{ url()->previous() }}">⬅ Back</a>

</div>
@endsection
