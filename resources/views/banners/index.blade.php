@extends('layouts.app')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm p-2">
            <div class="card-datatable">
                 @php
                    $canView = hasPermission('banners.view');
                    $canEdit = hasPermission('banners.edit');
                    $canDelete = hasPermission('banners.delete');  
                @endphp

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title"> Banner List</h5>
                    </div>
                    @if(hasPermission('banners.create'))
                    <div class="col-md-auto ms-auto mt-5">
                        <a href="{{ route('banners.create') }}" class="btn btn-success">
                           Add Banner
                        </a>
                    </div>
                    @endif
                </div><br><br>

                <!-- Search -->
                <x-datatable-search />
                <div class="table-responsive mt-3">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banners as $banner)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $banner->name }}</td>
                                <td>
                                    <img src="{{ asset('storage/'.$banner->image) }}" width="120">
                                </td>
                                @if($canView || $canEdit || $canDelete )
                                <td>
                                    @if($canEdit)
                                    <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endif
                                    @if($canDelete) 
                                    <form action="{{ route('banners.delete', $banner->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete banner?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No banners found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection


