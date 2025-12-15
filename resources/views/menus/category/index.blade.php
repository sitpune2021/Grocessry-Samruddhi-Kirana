@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Category</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('category.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Add Category
                    </a>
                </div>
            </div>

            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Category Name</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $index => $category)
                    <tr>
                        <td>{{ $categories->firstItem() + $index }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>
                            <x-action-buttons
                                :view-url="route('category.show', $category->id)"
                                :edit-url="route('category.edit', $category->id)"
                                :delete-url="route('category.destroy', $category->id)" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No categories found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <x-pagination
                :from="$categories->firstItem()"
                :to="$categories->lastItem()"
                :total="$categories->total()" />

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush