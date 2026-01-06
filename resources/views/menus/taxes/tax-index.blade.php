@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Taxes</h5>
                </div>
                <div class="col-md-auto ms-auto">

                    <a href="{{ route('taxes.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Tax
                    </a>
                </div>

            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>


@endpush