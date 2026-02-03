@extends('layouts.app')

@section('title', 'Service Areas')

@section('content')
<div class="container-xxl">
    
@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

    <div class="card">

        {{-- HEADER --}}
        <div class="card-header align-items-center" style="display:flex;">
            <h5 class="mb-0 flex-grow-1">
                Service Areas
                @if($warehouse)
                    – {{ $warehouse->name }}
                @endif
            </h5>

            <a href="{{ route('warehouse.index') }}" class="btn btn-sm btn-success">
                Back
            </a>
        </div>

        <div class="card-body">
            {{-- SUPER ADMIN DC SELECTOR --}}
            @if(auth()->user()->role_id == 1)
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="warehouse_id"
                                    class="form-select"
                                    onchange="this.form.submit()">
                                <option value="">Select Distribution Center</option>

                                @foreach($distributionCenters as $dc)
                                    <option value="{{ $dc->id }}"
                                        {{ optional($warehouse)->id == $dc->id ? 'selected' : '' }}>
                                        {{ $dc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            @endif

            {{-- ❗ NO DC SELECTED --}}
            @if(!$warehouse)
                <div class="alert alert-info">
                    Please select a Distribution Center to manage service areas.
                </div>
                @return
            @endif

            {{-- ADD PINCODE --}}
            <form method="POST"
                  action="{{ route('warehouse.service-areas.store') }}"
                  class="row g-2 mb-4">
                @csrf

                <div class="col-md-4">
                    <input type="text"
                           name="pincode"
                           maxlength="6"
                           class="form-control"
                           placeholder="Enter Pincode"
                           required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">
                        + Add
                    </button>
                </div>
            </form>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- PINCODE LIST --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="80">#</th>
                            <th>Pincode</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pincodes as $i => $pin)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $pin->pincode }}</td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('warehouse.service-areas.destroy', $pin->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No service pincodes added
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
