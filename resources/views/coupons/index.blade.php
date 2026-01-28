@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission( 'coupons.view');
            $canEdit = hasPermission('coupons.edit');
            $canDelete = hasPermission('coupons.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Coupon</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('coupons.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bx bx-plus"></i> Add Coupon
                    </a>
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
            <div class="table-responsive mt-5">
                <table id="batchTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">Sr No</th>
                            <th>Code</th>
                            <th>Discount Type</th>
                            <th>Discount Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Minimum Amt</th>
                            <th>Maximum Amt</th>
                            <th>Status</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center" style="width: 150px;">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($offers as $index => $offer)
                        <tr>
                            <td class="text-center fw-semibold">
                                {{ $offers->firstItem() + $index }}
                            </td>

                            <td>{{ $offer->code }}</td>
                            <td>
                                {{ $offer->discount_type }}
                            </td>
                            <td>{{ $offer->discount_value }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }}
                            </td>
                            <td> {{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}
                            </td>
                            <td>{{ $offer->min_amount }}</td>
                            <td>{{ $offer->max_usage }}</td>

                            <td>{{ $offer->status ? 'Active' : 'Inactive' }}</td>

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('coupons.view'))
                                <a href="{{ route('coupons.show', $offer->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif
                                @if(hasPermission('coupons.edit'))
                                <a href="{{ route('coupons.edit', $offer->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif
                                @if(hasPermission('coupons.delete'))
                                <form action="{{ route('coupons.destroy', $offer->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete offers?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No offers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{-- <x-pagination :from="$offers->firstItem()" :to="$offers->lastItem()" :total="$offers->total()" /> --}}
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
                if (row.cells.length === 1) return; // skip empty row
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });
    });
</script>
@endpush