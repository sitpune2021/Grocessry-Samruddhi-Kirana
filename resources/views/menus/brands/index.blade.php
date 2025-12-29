@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Brands</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    @if(hasPermission('brands', 'create'))
                    <a href="{{ route('brands.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Brands
                    </a>
                    @endif
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5">
                <table id="batchTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 15%;">Logo</th>
                            <th style="width: 30%;">Brand Name</th>
                            <th style="width: 40%;">Slug</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($brands as $index => $brand)
                        <tr>

                            {{-- Sr No --}}
                            <td class="text-center fw-semibold">
                                {{ $brands->firstItem() + $index }}
                            </td>

                            {{-- Logo --}}
                            <td class="text-center">
                                @if($brand->logo)
                                <img src="{{ asset('storage/brands/'.$brand->logo) }}"
                                    alt="{{ $brand->name }}"
                                    width="50"
                                    height="50"
                                    class="rounded border">
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Brand Name --}}
                            <td>
                                <span class="fw-medium">{{ $brand->name }}</span>
                            </td>

                            {{-- Slug --}}
                            <td class="text-muted">
                                {{ $brand->slug }}
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($brand->status)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center" style="white-space:nowrap;">
                                <x-action-buttons
                                    :view-url="route('brands.show', $brand->id)"
                                    :edit-url="route('brands.edit', $brand->id)"
                                    :delete-url="route('brands.destroy', $brand->id)" />
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No brands found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                <x-pagination
                    :from="$brands->firstItem()"
                    :to="$brands->lastItem()"
                    :total="$brands->total()" />
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("batchTable");

        if (!searchInput || !table) return;

        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {

                // Skip "No role found" row
                if (row.cells.length === 1) return;

                row.style.display = row.textContent
                    .toLowerCase()
                    .includes(value) ?
                    "" :
                    "none";
            });
        });

    });
</script>

@endpush