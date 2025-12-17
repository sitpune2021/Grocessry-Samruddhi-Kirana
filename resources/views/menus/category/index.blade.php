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
                    <a href="{{ route('category.create') }}"
                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Category
                    </a>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-5">
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

                            <td class="text-muted">
                                {{ $category->slug }}
                            </td>
                            <td class="text-center"> <x-action-buttons
                                    :view-url="route('category.show', $category->id)"
                                    :edit-url="route('category.edit', $category->id)"
                                    :delete-url="route('category.destroy', $category->id)" />
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
@endpush