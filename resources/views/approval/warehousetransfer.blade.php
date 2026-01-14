@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container bg-white mt-5 shadow rounded p-3">

        <h5 class="card-title mt-5">Warehouse Stock Request Approve</h5>

        <div class="table-responsive mt-5">

            <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Approved By Warehouse</th>
                        <th>Requested By Warehouse</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $t)
                    <tr>
                        <td>{{ $t->approvedByWarehouse->name }}</td>
                        <td>{{ $t->requestedByWarehouse->name }}</td>
                        <td>{{ $t->product->name }}</td>
                        <td>{{ $t->quantity }}</td>
                        <td>
                            @if($t->status == 0)
                                <span class="badge bg-warning">Pending</span>
                            @elseif($t->status == 1)
                                <span class="badge bg-success">Dispatched</span>
                            @elseif($t->status == 2)
                                <span class="badge bg-success">Approved</span>                           
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>                           
                            @if(
                                $t->status == 0 &&
                                $t->approved_by_warehouse_id == auth()->user()->warehouse_id
                            )
                                <div class="d-flex gap-1">
                                    <form method="POST"
                                        action="{{ route('warehouse.transfer.reject', $t->id) }}"
                                        onsubmit="return confirm('Are you sure you want to reject this transfer?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if(
                                $t->status == 1 &&
                                $t->approved_by_warehouse_id == auth()->user()->warehouse_id
                            )
                                <div class="d-flex gap-1">
                                    <form method="POST"
                                        action="{{ route('warehouse.transfer.approve', $t->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                    </form>
                                </div>
                            @endif
                            @if($t->status == 0 && $t->approved_by_warehouse_id == auth()->user()->warehouse_id)
                                <form method="POST" action="{{ route('warehouse.transfer.dispatch', $t->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Dispatch</button>
                                </form>
                            @endif
                            @if($t->status == 1 && $t->requested_by_warehouse_id == auth()->user()->warehouse_id)
                                <form method="POST" action="{{ route('warehouse.transfer.receive', $t->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Receive</button>
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