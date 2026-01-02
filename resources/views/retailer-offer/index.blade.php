@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm p-2">
            <div class="card-datatable text-nowrap">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row align-items-center pb-2">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title mb-0">Retailer Offers</h5>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <a href="{{ route('retailer-offers.create') }}"
                            class="btn btn-success btn-sm d-flex align-items-center gap-1">
                            <i class="bx bx-plus"></i> Add Retailer Offer
                        </a>
                    </div>
                </div>

                <!-- Search -->
                <div class="px-3 pt-2">
                    <x-datatable-search />
                </div>

                <!-- Table -->
                <div class="table-responsive mt-5">
                    <table id="offerTable" class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">Sr No</th>
                                <th style="width: 20%;">Retailer</th>
                                <th style="width: 20%;">Offer Name</th>
                                <th style="width: 15%;">Discount Type</th>
                                <th style="width: 15%;">Discount Value</th>
                                <th style="width: 15%;">Start Date</th>
                                <th style="width: 15%;">End Date</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($offers as $index => $offer)
                                <tr>
                                    <td class="text-center fw-semibold">
                                        {{ $offers->firstItem() + $index }}
                                    </td>
                                    <td>{{ $offer->user->first_name ?? '' }} {{ $offer->user->last_name ?? '' }}</td>
                                    <td>{{ $offer->offer_name }}</td>
                                    <td>{{ $offer->discount_type }}</td>
                                    <td>{{ $offer->discount_value }}</td>
                                    <td>{{ $offer->start_date }}</td>
                                    <td>{{ $offer->end_date }}</td>
                                    <td>
                                        <x-action-buttons :view-url="route('retailer-offers.show', $offer->id)" :edit-url="route('retailer-offers.edit', $offer->id)" :delete-url="route('retailer-offers.destroy', $offer->id)" />
                                    </td>
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
                    <x-pagination :from="$offers->firstItem()" :to="$offers->lastItem()" :total="$offers->total()" />
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
            const table = document.getElementById("offerTable");
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
