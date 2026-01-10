@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('batches.view');
            $canEdit = hasPermission('batches.edit');
            $canDelete = hasPermission('batches.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Batch List</h5>
                </div>

                @if(hasPermission('batches.create'))
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('batches.create') }}" class="btn btn-success">
                        <i class="bx bx-plus"></i> Add New Batch
                    </a>
                </div>
                @endif


            </div>
            <x-datatable-search />
            <div class="table-responsive mt-5 p-3">
                <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.no</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                        <tr>
                            <td style="width: 30px;">{{ $loop->iteration }}</td>
                            <td>{{ $batch->product?->name }}</td>
                            <td>{{ $batch->unit?->name }}</td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>{{ $batch->mfg_date }}</td>
                            <td>{{ $batch->expiry_date }}</td>


                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('batches.view'))
                                <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('batches.edit'))
                                <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('batches.delete'))
                                <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete batch?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("batchTable");

        if (!searchInput || !table) return;

        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {

                // Skip "No role found" row
                if (row.cells.length === 1) return;

                row.style.display = row.textContent
                    .toLowerCase()
                    .includes(value) ?
                    "" :
                    "none";
            });
        });

    });
</script>


<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("batchTable");

        if (!searchInput || !table) return;

        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("keyup", function() {
            const value = this.value.toLowerCase().trim();

            rows.forEach(row => {

                // Skip "No role found" row
                if (row.cells.length === 1) return;

                row.style.display = row.textContent
                    .toLowerCase()
                    .includes(value) ?
                    "" :
                    "none";
            });
        });

    });
</script>