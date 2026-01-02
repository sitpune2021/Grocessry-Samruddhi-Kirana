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
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('stock-returns.create') }}" class="btn btn-success">
                        <i class="bx bx-plus"></i> Raise Return
                    </a>
                </div>
            </div>

            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Return No</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Action</th>
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
                                    {{ $return->items->return_qty ?? 0 }} Items
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

                            <td>{{ $return->creator->name ?? '-' }}</td>

                            <td>{{ $return->created_at->format('d M Y') }}</td>

                           
                            <td>
                                
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