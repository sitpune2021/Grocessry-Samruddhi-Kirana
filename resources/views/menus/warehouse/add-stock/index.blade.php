@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Stock In Warehouse</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('warehouse.addStockForm') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Stock In Warehouse
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
                            <th>Sr No</th>
                            <th>Warehouse</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Batch No</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Warehouse --}}
                            <td>{{ $stock->warehouse->name ?? '-' }}</td>

                            {{-- Category --}}
                            <td>{{ $stock->category->name ?? '-' }}</td>

                            {{-- Product --}}
                            <td>{{ $stock->product->name ?? '-' }}</td>

                            {{-- Batch --}}
                            <td>{{ $stock->batch->batch_no ?? '-' }}</td>

                            {{-- Quantity --}}
                            <td>
                                {{ $stock->quantity }}
                            </td>

                            {{-- Actions --}}
                            <td>
                                <x-action-buttons
                                    :view-url="route('warehouse.viewStockForm', $stock->id)"
                                    :edit-url="route('warehouse.editStockForm', $stock->id)"
                                    :delete-url="route('stock.delete', $stock->id)" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No stock found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            <x-pagination
                :from="$stocks->firstItem()"
                :to="$stocks->lastItem()"
                :total="$stocks->total()" />

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush