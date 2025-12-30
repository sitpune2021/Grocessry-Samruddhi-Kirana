@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container bg-white mt-5 shadow rounded p-3">

        <h5 class="card-title mt-5">District Warehouse Transfer List</h5>

        <div class="table-responsive mt-5">

            <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                <thead class="table-light">
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $t)
                    <tr>
                        <td>{{ $t->fromWarehouse->name }}</td>
                        <td>{{ $t->toWarehouse->name }}</td>
                        <td>{{ $t->product->name }}</td>
                        <td>{{ $t->quantity }}</td>
                        <td>
                            @if($t->status == 0)
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-success">Approved</span>
                            @endif
                        </td>
                        <td>
                            @if($t->status == 0)
                            <form method="POST"
                                action="{{ route('warehouse.transfer.approve', $t->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $transfers->links() }}

        </div>
        
    </div>

</div>
@endsection