@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="container bg-white mt-5 shadow rounded p-3">

            <h4 class="card-title mt-5">Retailer Orders Approve</h4>
            <!-- Search -->
            <x-datatable-search />

            <div class="table-responsive mt-5">

                    <table class="table table-bordered table-striped">

                        <thead class="table-light">

                            <tr>
                                <th>Order No</th>
                                <th>Retailer</th>
                                <th>Products</th>
                                <th>Total Qty</th>
                                <th>Total Amount</th>                               
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($retailerOrders as $order)

                                <tr>

                                    {{-- ORDER NO --}}
                                    <td>
                                        <strong>
                                            {{ $order->order_no }}
                                        </strong>
                                    </td>


                                    {{-- RETAILER --}}
                                    <td>

                                        {{ $order->retailer->name ?? '-' }}

                                        @if($order->retailer?->shop_name)

                                            <br>

                                            <small class="text-muted">
                                                {{ $order->retailer->shop_name }}
                                            </small>

                                        @endif

                                    </td>


                                    {{-- PRODUCTS --}}
                                    <td>

                                        @foreach($order->items as $item)

                                            <div class="mb-1">

                                                <strong>
                                                    {{ $item->product->name ?? '-' }}
                                                </strong>

                                                <span class="text-muted">
                                                    × {{ $item->quantity }}
                                                </span>

                                                <br>

                                                <small class="text-muted">
                                                    ₹ {{ number_format($item->price, 2) }}
                                                    × {{ $item->quantity }}
                                                    =
                                                    ₹ {{ number_format($item->total, 2) }}
                                                </small>

                                            </div>

                                        @endforeach

                                    </td>


                                    {{-- TOTAL QTY --}}
                                    <td>

                                        {{ $order->items->sum('quantity') }}

                                    </td>


                                    {{-- TOTAL AMOUNT --}}
                                    <td>

                                        <strong>
                                            ₹ {{ number_format($order->total_amount, 2) }}
                                        </strong>

                                    </td>


                                    {{-- STATUS --}}
                                    <td>

                                        @if($order->status == 'pending')

                                            <span class="badge bg-warning text-dark">
                                                Pending
                                            </span>

                                        @elseif($order->status == 'approved')

                                            <span class="badge bg-success">
                                                Approved
                                            </span>

                                        @elseif($order->status == 'dispatched')

                                            <span class="badge bg-primary">
                                                Dispatched
                                            </span>

                                        @elseif($order->status == 'delivered')

                                            <span class="badge bg-info">
                                                Delivered
                                            </span>

                                        @elseif($order->status == 'cancelled')

                                            <span class="badge bg-danger">
                                                Cancelled
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                {{ ucfirst($order->status) }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTION --}}
                        <td>

                            {{-- PENDING --}}
                            @if($order->status == 'pending')

                                {{-- APPROVE --}}
                                <form
                                    method="POST"
                                    action="{{ route('retailer.order.approve', $order->id) }}"
                                    class="d-inline"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Are you sure you want to approve this retailer order?')"
                                    >
                                        Approve
                                    </button>

                                </form>


                                {{-- REJECT --}}
                                <form
                                    method="POST"
                                    action="{{ route('retailer.order.reject', $order->id) }}"
                                    class="d-inline"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to reject this retailer order?')"
                                    >
                                        Reject
                                    </button>

                                </form>


                            {{-- APPROVED --}}
                            {{-- APPROVED --}}
                        @elseif($order->status == 'approved')

                            {{-- DISPATCH --}}
                            <form
                                method="POST"
                                action="{{ route('retailer.order.dispatch', $order->id) }}"
                                class="d-inline"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-primary"
                                    onclick="return confirm('Are you sure you want to dispatch this retailer order? DC warehouse stock will be deducted.')"
                                >
                                    <i class="bi bi-truck"></i>
                                    Dispatch
                                </button>

                            </form>


                            {{-- DISPATCHED --}}
                            @elseif($order->status == 'dispatched')

                                <span class="text-primary">
                                    <i class="bi bi-truck"></i>
                                    Dispatched
                                </span>


                            {{-- DELIVERED --}}
                            @elseif($order->status == 'delivered')

                                <span class="text-info">
                                    <i class="bi bi-check-circle"></i>
                                    Delivered
                                </span>


                            {{-- CANCELLED --}}
                            @elseif($order->status == 'cancelled')

                                <span class="text-danger">
                                    <i class="bi bi-x-circle"></i>
                                    Cancelled
                                </span>

                            @endif

                        </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >

                                        No retailer orders found for this Distribution Center.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

            </div>

        </div>

    </div>
@endsection
