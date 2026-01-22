@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Raise Warehouse Stock Return</h5>
                </div>
                @php
                $warehouseType = auth()->user()?->warehouse?->type;

                @endphp

                @if ($warehouseType === 'taluka' || $warehouseType === 'district' ||$warehouseType === 'distribution_center')
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('stock-returns.create') }}" class="btn btn-success">
                        <i class="bx bx-plus"></i> Raise Return
                    </a>
                </div>
                @endif
            </div>

            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>sr no</th>
                            <th>Return No</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $key => $return)

                        <tr>
                            <td>{{ $returns->firstItem() + $key }}</td>

                            <td>
                                {{ $return->return_number ?? 'WR-' . str_pad($return->id, 5, '0', STR_PAD_LEFT) }}
                            </td>

                            <td>{{ $return->fromWarehouse->name ?? '-' }}</td>
                            <td>{{ $return->toWarehouse->name ?? '-' }}</td>

                            <td>{{ ucfirst(str_replace('_',' ', $return->return_reason)) }}</td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $return->WarehouseStockReturnItem->sum('return_qty') }} Items

                                </span>
                            </td>

                            <td>
                                @php
                                $statusColors = [
                                'draft' => 'secondary',
                                'approved' => 'success',
                                'dispatched' => 'warning',
                                'received' => 'primary',
                                'rejected' => 'danger',
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$return->status] ?? 'dark' }}">
                                    {{ strtoupper($return->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $return->creator->first_name ?? '-' }}
                                <br>
                                <small class="text-muted">
                                    ({{ ucfirst(str_replace('_', ' ', $return->creator->role->name ?? 'N/A')) }})
                                </small>
                            </td>

                            <td>{{ $return->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('warehouse-stock-returns.download-pdf', $return->id) }}"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Download PDF">
                                    <i class="ri-file-pdf-line"></i>
                                </a>
                            </td>
                            <td>
                                @php
                                $userWarehouseId = auth()->user()->warehouse_id;
                                $userWarehouseType = auth()->user()->warehouse->type;
                                @endphp


                                {{-- Taluka approves the return --}}
                                @if($return->status === 'draft'
                                && $userWarehouseType === 'taluka'
                                && $userWarehouseId === $return->to_warehouse_id)
                                <form action="{{ route('stock-returns.dc-approve', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>
                                @endif

                                {{-- TALUKA → DISTRICT FLOW --}}
                                {{-- Distribution center dispach to taluka the return --}}
                                @if($return->status === 'approved'
                                && $userWarehouseType === 'distribution_center'
                                && $userWarehouseId === $return->from_warehouse_id)

                                <form action="{{ route('stock-returns.dispatch', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">Dispatch</button>
                                </form>
                                @endif
                                {{-- taluka receive stock from DC --}}
                                @if($return->status === 'dispatched'
                                && $userWarehouseType === 'taluka'
                                && $userWarehouseId === $return->to_warehouse_id)

                                <form action="{{ route('stock-returns.receive', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Receive</button>
                                </form>
                                @endif

                                {{-- DISTRICT → MASTER FLOW --}}
                                @if($return->status === 'received'
                                && $userWarehouseType === 'taluka'
                                && $userWarehouseId === $return->to_warehouse_id)

                                <a href="{{ route('stock-returns.return-to-master', $return->id) }}"
                                    class="btn btn-success btn-sm">
                                    Return to District
                                </a>
                                @endif

                                {{-- DISTRICT → MASTER FLOW --}}
                                @if($return->status === 'received'
                                && $userWarehouseType === 'district')

                                <a href="{{ route('stock-returns.return-to-master', $return->id) }}"
                                    class="btn btn-success btn-sm">
                                    Return to Master
                                </a>
                                @endif

                                @if($return->status === 'MASTER_CREATED'
                                && $userWarehouseType === 'master')

                                <form action="{{ route('stock-returns.approve1', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Approve</button>
                                </form>
                                @endif

                                @if($return->status === 'MASTER_APPROVED'
                                && $userWarehouseType === 'district')

                                <form action="{{ route('stock-returns.dispatch1', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">Dispatch</button>
                                </form>
                                @endif

                                @if($return->status === 'MASTER_DISPATCHED'
                                && $userWarehouseType === 'master')

                                <form action="{{ route('stock-returns.receive1', $return->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary btn-sm">Receive</button>
                                </form>
                                @endif


                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                No stock returns found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :from="$returns->firstItem()" :to="$returns->lastItem()" :total="$returns->total()" />
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>