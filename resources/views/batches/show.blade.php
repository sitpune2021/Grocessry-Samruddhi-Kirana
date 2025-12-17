@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Batch Details</h3>

    <table border="1" cellpadding="10" cellspacing="0" width="50%">
        <tr>
            <th align="left">Product</th>
            <td>{{ $batch->product->name ?? '-' }}</td>
        </tr>

        <tr>
            <th align="left">Batch Number</th>
            <td>{{ $batch->batch_no }}</td>
        </tr>

        <tr>
            <th align="left">Quantity</th>
            <td>{{ $batch->quantity }}</td>
        </tr>

        <tr>
            <th align="left">MFG Date</th>
            <td>{{ $batch->mfg_date }}</td>
        </tr>

        <tr>
            <th align="left">Expiry Date</th>
            <td>{{ $batch->expiry_date }}</td>
        </tr>

        <tr>
            <th align="left">Created At</th>
            <td>{{ $batch->created_at }}</td>
        </tr>
    </table>

    <br>

    <a href="{{ route('batches.index') }}">⬅ Back to list</a>

</div>
@endsection
