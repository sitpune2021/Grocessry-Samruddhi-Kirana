@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white fw-semibold">
                        @if ($mode === 'add')
                            Add Retailer Offer
                        @elseif($mode === 'edit')
                            Edit Retailer Offer
                        @endif
                    </div>

                    <div class="card-body">
                        @php $readonly = $mode === 'view' ? 'readonly' : ''; @endphp

                        <form
                            action="{{ $mode === 'edit' ? route('retailer-offers.update', $offer->id) : route('retailer-offers.store') }}"
                            method="POST">
                            @csrf
                            @if ($mode === 'edit')
                                @method('PUT')
                            @endif

                            <div class="row g-3">

                                {{-- Retailer --}}
                                <div class="col-md-4">
                                    <label class="form-label">Retailer <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select" {{ $readonly }}>
                                        <option value="">Select Retailer</option>
                                        @foreach ($retailers as $retailer)
                                            <option value="{{ $retailer->id }}"
                                                {{ old('user_id', $offer->user_id ?? '') == $retailer->id ? 'selected' : '' }}>
                                                {{ $retailer->first_name }} {{ $retailer->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Offer Name --}}
                                <div class="col-md-4">
                                    <label class="form-label">Offer Name <span class="text-danger">*</span></label>
                                    <input type="text" name="offer_name" class="form-control"
                                        value="{{ $offer->offer_name ?? old('offer_name') }}" placeholder="Enter offer name"
                                        {{ $readonly }}>
                                    @error('offer_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Discount Type --}}
                                <div class="col-md-4">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select name="discount_type" class="form-select" {{ $readonly }}>
                                        <option value="">Select Type</option>
                                        <option value="percentage"
                                            {{ old('discount_type', $offer->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                            Percentage</option>
                                        <option value="flat"
                                            {{ old('discount_type', $offer->discount_type ?? '') == 'flat' ? 'selected' : '' }}>
                                            Flat</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Discount Value --}}
                                <div class="col-md-4">
                                    <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                    <input type="number" name="discount_value" class="form-control"
                                        value="{{ $offer->discount_value ?? old('discount_value') }}"
                                        placeholder="Enter discount value" {{ $readonly }}>
                                    @error('discount_value')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-4">
                                    <label class="form-label">Start Date </label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ $offer->start_date ?? old('start_date') }}" {{ $readonly }}>
                                    @error('start_date')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-4">
                                    <label class="form-label">End Date </label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ $offer->end_date ?? old('end_date') }}" {{ $readonly }}>
                                    @error('end_date')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Status --}}
                                <div class="col-md-4">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" {{ $readonly }}>
                                        <option value="1"
                                            {{ old('status', $offer->status ?? '') == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ old('status', $offer->status ?? '') == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Buttons --}}
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('retailer-offers.index') }}" class="btn btn-success">Back</a>
                                @if ($mode === 'add')
                                    <button type="submit" class="btn btn-success">Save Offer</button>
                                @elseif($mode === 'edit')
                                    <button type="submit" class="btn btn-primary">Update Offer</button>
                                @endif
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
