@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-datatable text-nowrap">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">User</h5>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <a href="{{ route('user.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i> Add User
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
                                <th>User Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->mobile ?? '-' }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>{{ $user->role->name ?? '-' }}</td>

                                    <td class= "text-primary">{{ $user->status == 1 ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <x-action-buttons :view-url="route('user.show', $user->id)" :edit-url="route('user.edit', $user->id)" :delete-url="route('user.destroy', $user->id)" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No user found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <x-pagination :from="$users->firstItem()" :to="$users->lastItem()" :total="$users->total()" />

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush
