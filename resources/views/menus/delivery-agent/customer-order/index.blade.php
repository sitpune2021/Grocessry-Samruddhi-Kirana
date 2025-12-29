@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Customer Orders</h5>
                </div>
                <!-- <div class="col-md-auto ms-auto">
                    <a href="{{ route('delivery-agents.create') }}"
                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Agent
                    </a>
                </div> -->

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="driverVehicleTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 25%;">Customer Name</th>
                            <th style="width: 25%;">Order Number</th>
                            <th style="width: 25%;">Product</th>
                            <th style="width: 25%;">Quantity</th>
                            <th style="width: 25%;">Price</th>
                            <th style="width: 25%;">total</th>
                            <th style="width: 25%;">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $index => $order)

                        @foreach($order->customerOrderItems as $item)
                        <tr>
                            <td class="text-center">
                                {{ $loop->parent->iteration }}
                            </td>

                            <td>
                                {{ $order->customer->first_name ?? '-' }}
                                {{ $order->customer->last_name ?? '' }}
                            </td>

                            <td>
                                {{ $order->order_number }}
                            </td>

                            <td>
                                {{ $item->product->name ?? '-' }}
                            </td>

                            <td>
                                {{ $item->quantity }}
                            </td>

                            <td>
                                ₹{{ number_format($item->price, 2) }}
                            </td>

                            <td>
                                ₹{{ number_format($item->total, 2) }}
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($order->status === 'pending')
                                <button class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#assignAgentModal"
                                    data-order-id="{{ $order->id }}">
                                    Assign
                                </button>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>


                        </tr>
                        @endforeach

                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No orders found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>


            <!-- Pagination -->
            <div class="px-3 py-2">

            </div>

        </div>
    </div>

    <div class="modal fade" id="assignAgentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.assign.delivery') }}">
                @csrf

                <input type="hidden" name="order_id" id="modal_order_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Delivery Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Agent</label>
                            <select name="delivery_agent_id" class="form-select" required>
                                <option value="">Select Agent</option>
                                @foreach($deliveryAgents as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->user->first_name }} {{ $agent->user->last_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Assign
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection



@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('assignAgentModal');

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');

            document.getElementById('modal_order_id').value = orderId;
        });
    });
</script>



@endpush