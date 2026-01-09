@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Category</h5>
                </div>
                <div class="col-md-auto ms-auto">
                     @if(hasPermission('category.create'))
                    <a href="{{ route('category.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Category
                    </a>
                    @endif
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5 p-3">
                <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th style="width: 30%;">Category Name</th>
                            <th style="width: 40%;">Slug</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $index => $category)
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ $categories->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="fw-medium">{{ $category->name }}</span>
                            </td>
                            <td class="text-muted">{{ $category->slug }}</td>
                            
                             <td class="text-center" style="white-space:nowrap;" >
                                @if(hasPermission('category.view'))
                                <a href="{{ route('category.show', $category->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('category.edit'))
                                    <a href="{{ route('category.edit', $category->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                 @endif
                                @if(hasPermission('category.delete'))
                                    <form action="{{ route('category.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete category?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No categories found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                <x-pagination
                    :from="$categories->firstItem()"
                    :to="$categories->lastItem()"
                    :total="$categories->total()" />
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