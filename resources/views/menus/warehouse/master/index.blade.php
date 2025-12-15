@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Warehouse</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Add Warehouse
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
                        <th>Category</th>
                        <th>Warehouse Name</th>
                        <th>SKU</th>
                        <th>Description</th>
                        <th>Base Price</th>
                        <th>Retailer Price</th>
                        <th>MRP</th>
                        <th>GST (%)</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($warehouses as $index => $warehouse)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- warehouse Name --}}
                        <td></td>

                        <td>{{ $warehouse->name }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                      
                        <td>
                           
                        </td>

                        {{-- Actions --}}
                        <td>
                            <x-action-buttons
                                :view-url="route('warehouse.show', $warehouse->id)"
                                :edit-url="route('warehouse.edit', $warehouse->id)"
                                :delete-url="route('warehouse.destroy', $warehouse->id)" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted">
                            No Products found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
             </div>

            <!-- Pagination -->
            <x-pagination
                :from="$warehouses->firstItem()"
                :to="$warehouses->lastItem()"
                :total="$warehouses->total()" />

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush