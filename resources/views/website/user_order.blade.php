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

                <!-- Search -->
                <x-datatable-search />

                <div class="table-responsive mt-3">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif         

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>User</th>
                                <th>Order Number</th>
                                <th>Product Name</th>
                                <th>Total</th>
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
