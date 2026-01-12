@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container bg-white mt-5 shadow rounded p-3">

        <h5 class="card-title mt-5">Taluka-Taluka Warehouse Stock Transfer</h5>

        <div class="table-responsive mt-5">

            <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">sr no</th>
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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $t->fromWarehouse->name }}</td>
                        <td>{{ $t->toWarehouse->name }}</td>
                        <td>{{ $t->product->name }}</td>
                        <td>{{ $t->quantity }}</td>
                        <td>
                            @if($t->status == 0)
                                <span class="badge bg-warning">Pending</span>
                            @elseif($t->status == 1)
                                <span class="badge bg-success">Accepted</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            @if($t->status == 0)
                                <div class="d-flex gap-1">
                                    <form method="POST"
                                        action="{{ route('taluka-taluka.transfer.approve', $t->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('taluka-taluka.transfer.reject', $t->id) }}"
                                        onsubmit="return confirm('Are you sure you want to reject this transfer?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">
                                            Reject
                                        </button>
                                    </form>
                                </div>
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