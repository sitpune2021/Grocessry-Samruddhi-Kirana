@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container bg-white mt-5 shadow rounded p-3">

        <h5 class="card-title mt-5">Purchase Order History</h5>

        <div class="table-responsive mt-5">

            <table id="transfersTable" class="table table-bordered table-striped  mb-0">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->po_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->po_date)->format('d M, Y') }}</td>

                            <td>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->price, 2) }}</td>
                                                <td>₹{{ number_format($item->total, 2) }}</td>                                      
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>

                            <td>₹{{ number_format($order->subtotal, 2) }}</td>
                            <td>₹{{ number_format($order->discount, 2) }}</td>
                            <td><b>₹{{ number_format($order->grand_total, 2) }}</b></td>
                            <td>
                                <a href="{{ route('purchase.invoice', $order->id) }}"
                                    class="btn btn-sm btn-success">
                                    Invoice Report
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                No Purchase Orders Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
