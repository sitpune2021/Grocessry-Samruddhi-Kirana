@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container bg-white mt-5 shadow rounded p-3">

        <h5 class="card-title mt-5">Warehouse / Distribution Center Stock Request Approve</h5>

        <div class="table-responsive mt-5">

            <table id="transfersTable" class="table table-bordered table-striped  mb-0">
                
                <thead class="table-light">
                    <tr>
                        <th>Requested By Warehouse</th>
                        <th>Approved By Warehouse</th>                        
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($transfers ?? collect()) as $groupKey => $group)

                    @php
                        $first = $group->first();
                        $userWarehouseId = auth()->user()->warehouse_id;
                    @endphp

                    <tr class="table-primary">
                        <td colspan="6">
                            <strong>
                                {{ $first->approvedByWarehouse->name }}
                                →
                                {{ $first->requestedByWarehouse->name }}
                            </strong>

                            {{-- MASTER: Dispatch All --}}
                            @if($first->status == 0 && $first->approved_by_warehouse_id == $userWarehouseId)
                                <!-- <form method="POST" action="{{ route('warehouse.transfer.dispatch.bulk') }}" class="d-inline">
                                    @csrf
                                    @foreach($group as $t)
                                        <input type="hidden" name="transfer_ids[]" value="{{ $t->id }}">
                                    @endforeach
                                    <button class="btn btn-sm btn-success float-end">
                                        Dispatch All
                                    </button>
                                </form> -->
                                <form method="GET" action="{{ route('transfer-challans.create') }}">
                                    <input type="hidden" name="from_warehouse_id" value="{{ $first->approved_by_warehouse_id }}">
                                    <input type="hidden" name="to_warehouse_id" value="{{ $first->requested_by_warehouse_id }}">

                                    <input type="hidden" name="transfer_group" value="{{ $groupKey }}">
                                    <button class="btn btn-sm btn-success">transfer-challans</button>
                                </form>
                            @endif

                            {{-- DISTRICT: Receive All --}}
                            @if($first->status == 1 && $first->requested_by_warehouse_id == $userWarehouseId)
                                <form method="POST" action="{{ route('warehouse.transfer.receive.bulk') }}" class="d-inline">
                                    @csrf
                                    @foreach($group as $t)
                                        <input type="hidden" name="transfer_ids[]" value="{{ $t->id }}">
                                    @endforeach
                                    <button class="btn btn-sm btn-primary float-end">
                                        Receive All
                                    </button>
                                </form>
                            @endif

                        </td>
                    </tr>

                    @foreach($group as $t)
                    <tr>
                        <td>{{ $t->requestedByWarehouse->name }}</td>
                        <td>{{ $t->approvedByWarehouse->name }}</td>                       
                        <td>{{ $t->product->name }}</td>
                        <td>{{ 
                                $t->challan?->items
                                    ->where('product_id', $t->product_id)
                                    ->first()?->quantity 
                                ?? $t->quantity 
                            }}
                        </td>
                        <td>
                            @if($t->status == 0)
                                <span class="badge bg-warning">Pending</span>
                            @elseif($t->status == 1)
                                <span class="badge bg-success">Dispatched</span>
                            @elseif($t->status == 2)
                                <span class="badge bg-primary">Received</span>
                                @elseif($t->status == 3)
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            {{-- MASTER: Single Dispatch --}}
                            @if($t->status == 0 && $t->approved_by_warehouse_id == $userWarehouseId)
                                <form method="POST" action="{{ route('warehouse.transfer.dispatch.single', $t->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Dispatch</button>
                                </form>

                                <form method="POST" action="{{ route('warehouse.transfer.reject', $t->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to reject this transfer?')">
                                        Reject
                                    </button>
                                </form>
                            @endif
                            {{-- DISTRICT: Single Receive --}}
                            <!-- @if($t->status == 1 && $t->requested_by_warehouse_id == $userWarehouseId)
                                <form method="POST" action="{{ route('warehouse.transfer.receive.single', $t->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Receive</button>
                                </form>
                            @endif -->
                        </td>

                    </tr>
                    @endforeach

                    @endforeach
                </tbody>

            </table>

        </div>
        
    </div>

</div>
@endsection