@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card shadow-sm p-2">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title mb-0">Coupons</h5>
                </div>

                <div class="col-md-auto ms-auto">
                    @if(hasPermission('coupons', 'create'))
                    <a href="{{ route('coupons.create') }}"
                        class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        Add Coupon
                    </a>
                    @endif
                </div>
            </div>

            <!-- Search -->
            <div class="px-3 pt-2">
                <x-datatable-search />
            </div>

            <!-- Table -->
            <div class="table-responsive mt-4">
                <table id="batchTable" class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:80px;">Sr No</th>
                            <th>Coupon Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Cart</th>
                            <th>Validity</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width:150px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($coupons as $index => $coupon)
                        <tr>

                            {{-- Sr No --}}
                            <td class="text-center fw-semibold">
                                {{ $coupons->firstItem() + $index }}
                            </td>

                            {{-- Coupon Code --}}
                            <td class="fw-medium">
                                {{ $coupon->code }}
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="badge bg-info text-uppercase">
                                    {{ $coupon->type }}
                                </span>
                            </td>

                            {{-- Value --}}
                            <td>
                                {{ $coupon->type === 'percent' ? $coupon->value.'%' : '₹'.$coupon->value }}
                            </td>

                            {{-- Min Cart --}}
                            <td>
                                {{ $coupon->min_cart_amount ? '₹'.$coupon->min_cart_amount : '—' }}
                            </td>

                            {{-- Validity --}}
                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($coupon->start_date)->format('d M Y') }}
                                →
                                {{ \Carbon\Carbon::parse($coupon->end_date)->format('d M Y') }}
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($coupon->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center" style="white-space:nowrap;">
                                <x-action-buttons
                                    :view-url="route('coupons.show', $coupon->id)"
                                    :edit-url="route('coupons.edit', $coupon->id)"
                                    :delete-url="route('coupons.destroy', $coupon->id)" />
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No coupons found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2">
                <x-pagination
                    :from="$coupons->firstItem()"
                    :to="$coupons->lastItem()"
                    :total="$coupons->total()" />
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("dt-search-1");
    const table = document.getElementById("batchTable");

    if (!searchInput || !table) return;

    const rows = table.querySelectorAll("tbody tr");

    searchInput.addEventListener("keyup", function () {
        const value = this.value.toLowerCase().trim();

        rows.forEach(row => {
            if (row.cells.length === 1) return;

            row.style.display = row.textContent
                .toLowerCase()
                .includes(value) ? "" : "none";
        });
    });

});
</script>
@endpush
