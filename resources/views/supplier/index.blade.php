@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission('supplier.view');
            $canEdit = hasPermission('supplier.edit');
            $canDelete = hasPermission('supplier.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Supplier</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    @if(hasPermission('supplier.create'))
                    <a href="{{ route('supplier.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                         Add Supplier
                    </a>
                    @endif
                </div>
            </div>
            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

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
            
            <!-- Table -->
            <div class="table-responsive mt-3">
                <table id="batchTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:20px;">Sr No</th>
                            <th style="width:50%;">Name</th>
                            <th style="width:25%;">Phone</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center" style="width:150px;">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($suppliers as $index => $item)
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ $suppliers->firstItem() + $index }}
                            </td>
                            <td>{{ $item->supplier_name }}</td>
                            <td>{{ $item->mobile }}</td>

                            {{-- Actions --}}
                            @if($canView || $canEdit || $canDelete)

                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('supplier.view'))
                                <a href="{{ route('supplier.show', $item->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('supplier.edit'))
                                <a href="{{ route('supplier.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('supplier.delete'))
                                <form action="{{ route('supplier.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete supplier?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No suppliers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $suppliers->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection
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
                if (row.cells.length === 1) return;
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });
    });
</script>
@endpush