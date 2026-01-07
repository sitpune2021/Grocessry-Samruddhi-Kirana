@extends('layouts.app')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm p-2">
            <div class="card-datatable">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">User Contact List</h5>
                    </div>
                </div><br><br>

                <!-- Search -->
                <x-datatable-search />

                <div class="table-responsive mt-3">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $key => $contact)
                                <tr>
                                    <td>{{ $contacts->firstItem() + $key }}</td>
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ $contact->message }}</td>
                                    <td>{{ $contact->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $contacts->links('pagination::bootstrap-5') }}
                    </div>

                    <div class="text-muted mt-2">
                        Showing {{ $contacts->firstItem() }} to {{ $contacts->lastItem() }} of {{ $contacts->total() }} results
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
