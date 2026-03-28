@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title"> Website User Order History</h5>
                </div>
            </div><br><br>

            @php $user = auth()->user(); @endphp

            <form method="GET" class="row g-2 mb-3">

                {{-- Warehouse --}}
                <div class="col-md-3">
                    <select name="warehouse_id" class="form-select"
                        {{ !in_array($user->role_id, [1,2]) ? 'disabled' : '' }}>

                        <option value="">All Warehouses</option>

                        @foreach(\App\Models\Warehouse::where('type','distribution_center')->get() as $wh)
                        <option value="{{ $wh->id }}"
                            {{ request('warehouse_id', $user->warehouse_id) == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                        @endforeach
                    </select>

                    @if(!in_array($user->role_id, [1,2]))
                    <input type="hidden" name="warehouse_id" value="{{ $user->warehouse_id }}">
                    @endif
                </div>

                {{-- Dates --}}
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-12 d-flex gap-2 mt-2">
                    <button class="btn btn-primary btn-sm">Filter</button>
                    <a href="" class="btn btn-secondary btn-sm">Reset</a>
                    <a href="{{ route('orders.export.csv') }}" class="btn btn-success btn-sm">
                        Download CSV
                    </a>
                </div>

            </form>

            <div class="table-responsive mt-3">

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Order </th>
                            <th>User</th>
                            <th>Order Number</th>
                            <th>Product Name</th>
                            <th>Total</th>
                            <th>Delivery Agent</th>
                            <th>Status</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user->first_name ?? 'N/A' }}</td>
                            <td>{{ $order->order_number }}</td>
                            <!-- PRODUCTS -->
                            <td>
                                @foreach($order->items as $item)
                                <div>
                                    {{ $item->product->name ?? 'N/A' }}
                                    (Qty: {{ $item->quantity }})
                                </div>
                                @endforeach
                            </td>
                            <td>₹{{ $order->total_amount }}</td>
                            <td>
                                {{ $order->deliveryAgent->user->first_name ?? 'N/A' }}
                                {{ $order->deliveryAgent->user->last_name ?? '' }}
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $order->status }}</span>
                            </td>
                            <!-- <td>
                                    @if($order->status == 'pending')
                                        <form action="{{ route('orderapprove', $order->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                    @else
                                        ✔ Approved
                                    @endif
                                </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection