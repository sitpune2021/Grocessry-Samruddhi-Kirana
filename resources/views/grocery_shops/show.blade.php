@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Grocery Shop Details</h5>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('grocery-shops.edit', $shop->id) }}" class="btn btn-warning btn-sm">
                Edit
            </a>

            <a href="{{ route('grocery-shops.index') }}" class="btn btn-secondary btn-sm">
                Back
            </a>
        </div>

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Shop Name:</strong></div>
                <div class="col-md-6">{{ $shop->shop_name }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Owner Name:</strong></div>
                <div class="col-md-6">{{ $shop->owner_name ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Mobile No:</strong></div>
                <div class="col-md-6">{{ $shop->mobile_no ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>District:</strong></div>
                <div class="col-md-6">{{ $shop->district->name ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Taluka:</strong></div>
                <div class="col-md-6">{{ $shop->taluka->name ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Address:</strong></div>
                <div class="col-md-6">{{ $shop->address ?? '-' }}</div>
            </div>
        </div>

        
    </div>
</div>
@endsection
