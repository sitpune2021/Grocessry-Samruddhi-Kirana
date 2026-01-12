@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
             @php
            $canView = hasPermission( 'distribution_center.view');
            $canEdit = hasPermission('distribution_center.edit');
            $canDelete = hasPermission('distribution_center.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">SHOP LIST</h5>
                </div>
                <div class="col-md-auto ms-auto mt-5">
                    <a href="{{ route('grocery-shops.create') }}" class="btn btn-success">
                        ADD SHOP
                    </a>
                </div>
            </div><br><br>

            <!-- Search -->
            <x-datatable-search />

            <table id="transfersTable" class="table table-bordered table-striped mt-4 mb-5">
                <thead class="table-light">
                    <tr>
                        <th>sr no</th>
                        <th>Shop Name</th>
                        <th>Owner</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        @if($canView || $canEdit || $canDelete)
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($shops as $shopItem)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $shopItem->shop_name }}</td>
                            <td>{{ $shopItem->owner_name }}</td>
                            <td>{{ $shopItem->mobile_no }}</td>
                            <td>{{ $shopItem->address }}</td>

                             @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('distribution_center.view'))
                                <a href="{{ route('grocery-shops.show', $shopItem->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('distribution_center.edit'))
                                <a href="{{ route('grocery-shops.edit', $shopItem->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endif
                                    @if(hasPermission('distribution_center.delete'))
                                    <form action="{{ route('grocery-shops.destroy', $shopItem->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete distribution_center?')" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                            @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No shops found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

@endsection
