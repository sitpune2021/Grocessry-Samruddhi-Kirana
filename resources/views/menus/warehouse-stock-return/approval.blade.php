@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Raise Warehouse Stock Return</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('stock-returns.create') }}" class="btn btn-success">
                        Raise Return
                    </a>
                </div>
            </div>

            <!-- Search -->
            <x-datatable-search />

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Return No</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($returns as $return)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $return->return_no }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_',' ', $return->status)) }}
                                </span>
                            </td>

                            <td>
                                {{-- SEND FOR APPROVAL --}}
                                @if($return->status === 'draft')
                                <form method="POST"
                                    action="{{ route('stock-returns.sendForApproval', $return->id) }}">
                                    @csrf
                                    <button class="btn btn-warning btn-sm">
                                        Send for Approval
                                    </button>
                                </form>
                                @endif

                                {{-- APPROVE / REJECT --}}
                                @if($return->status === 'pending_approval')
                                <form method="POST"
                                    action="{{ route('stock-returns.approve', $return->id) }}"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST"
                                    action="{{ route('stock-returns.reject', $return->id) }}"
                                    style="display:inline">
                                    @csrf
                                    <input type="hidden" name="remark" value="Rejected">
                                    <button class="btn btn-danger btn-sm">
                                        Reject
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>