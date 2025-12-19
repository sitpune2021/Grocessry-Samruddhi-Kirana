@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Role</h5>
                </div>

                <div class="col-md-auto ms-auto d-flex gap-2">
                    <div class="col-md-auto ms-auto d-flex gap-2">
                        <!-- Add Role Button -->
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Add Role
                        </a>
                    </div>
                </div>
            </div>


            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <div class="table-responsive mt-3">
                <table id="batchTable" class="table table-bordered table-striped">
                    <thead class="">
                        <tr>
                            <th>Sr No</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description ?? '-' }}</td>

                            <td>
                                <x-action-buttons :view-url="route('roles.show', $role->id)" :edit-url="route('roles.edit', $role->id)" :delete-url="route('roles.destroy', $role->id)" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No role found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <x-pagination :from="$roles->firstItem()" :to="$roles->lastItem()" :total="$roles->total()" />

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush