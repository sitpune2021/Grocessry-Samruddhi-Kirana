@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">
            @php
            $canView = hasPermission( 'offers.view');
            $canEdit = hasPermission('offers.edit');
            $canDelete = hasPermission('offers.delete');
            @endphp

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Offers</h5>
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('offers.create') }}" class="btn btn-success">
                        Add Offer
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
                            <th>Titel</th>
                            <th>Offer Type</th>
                            <th>Discount Value</th>
                            <th>Buy X Get Y</th>
                            <th>Minimum Amt</th>
                            <th>Validity</th>
                            <!-- <th>End Date</th> -->
                            <th>Status</th>
                            @if($canView || $canEdit || $canDelete)
                            <th class="text-center" style="width: 150px;">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offers as $off)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $off->title ?? '' }}</td>
                            <td>{{ $off->offer_type ?? ' ' }}</td>
                            <td>{{ $off->discount_value ?? '' }}</td>
                            <td>
                                {{ $off->buy_quantity }} ||
                                {{ $off->get_quantity }}
                            </td>
                            <td>{{ $off->min_order_amount }}</td>
                            <td>
                                {{ optional($off->start_date)->format('d-m-Y') ?? '-' }}
                                ||
                                {{ optional($off->end_date)->format('d-m-Y') ?? '-' }}
                            </td>
                            <td>
                                @if($off->status)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            @if($canView || $canEdit || $canDelete)
                            <td class="text-center" style="white-space:nowrap;">
                                @if(hasPermission('offer.view'))
                                <a href="{{ route('offers.show', $off->id) }}" class="btn btn-sm btn-primary">View</a>
                                @endif

                                @if(hasPermission('offer.edit'))
                                <a href="{{ route('offers.edit', $off->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                @endif

                                @if(hasPermission('offer.delete'))
                                <form action="{{ route('offers.destroy', $off->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete offer?')" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No offer found</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                {{ $offers->onEachSide(0)->links('pagination::bootstrap-5') }}
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