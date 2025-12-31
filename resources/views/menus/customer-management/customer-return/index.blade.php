@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Customer Order Returns</h5>
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
                            <th style="width: 25%;">Price</th>
                            <th style="width: 25%;">total</th>
                            <th style="width: 25%;">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                        <tr>
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $return->customer->first_name ?? '-' }}
                            </td>

                            <td>
                                {{ $return->order->order_number ?? '-' }}
                            </td>

                            <td>
                                {{ $return->product->name ?? '-' }}
                            </td>

                            <td>
                                ₹{{ number_format($return->orderItem->price ?? 0, 2) }}
                            </td>

                            <td>
                                ₹{{ number_format(($return->orderItem->price ?? 0) * $return->quantity, 2) }}
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('customer-returns.show', $return->id) }}"
                                    class="btn btn-sm btn-primary">
                                    View
                                </a>

                                <!-- <a href="{{ route('customer-returns.edit', $return->id) }}"
                                    class="btn btn-sm btn-warning">
                                    QC
                                </a> -->

                                <a href="javascript:void(0)"
                                    class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#qcModal{{ $return->id }}">
                                    QC
                                </a>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No return records found
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
</div>
<!-- QC Modal -->
<div class="modal fade" id="qcModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Quality Check (QC)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('customer-returns.update', $return->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">QC Status</label>
                        <select name="qc_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="passed">Passed</option>
                            <option value="failed">Failed</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Return Status</label>
                        <select name="status" class="form-select" required>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Submit QC
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
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