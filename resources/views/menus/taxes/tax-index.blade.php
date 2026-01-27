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
                <div class="col-md-auto ms-auto mt-5">

                    <a href="{{ route('taxes.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Tax
                    </a>
                </div>

            </div>

            <!-- Search -->
            <x-datatable-search />

            @if(session('success'))
            <div id="successAlert"
                class="alert alert-success alert-dismissible fade show mx-auto mt-3 w-100 w-sm-75 w-md-50 w-lg-25 text-center"
                role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <script>
                setTimeout(function() {
                    let alert = document.getElementById('successAlert');
                    if (alert) {
                        let bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 10000); // 15 seconds
            </script>
            @endif


            <div class="table-responsive mt-2 p-3">
                <table id="taxTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>GST</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxes as $key => $tax)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $tax->name }}</td>
                            <td>{{ $tax->cgst }}%</td>
                            <td>{{ $tax->sgst }}%</td>
                            <td>{{ $tax->gst }}%</td>
                            <td>
                                @if($tax->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center" style="white-space:nowrap;">
                                <a href="{{ route('taxes.show', $tax->id) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{route('taxes.edit', $tax->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('taxes.destroy', $tax->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete tax?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No tax records found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Pagination -->
    <div class="px-3 py-2">
        {{ $taxes->onEachSide(0)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

<!-- table search box script -->

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("dt-search-1");
        const table = document.getElementById("taxTable");

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

@endpush