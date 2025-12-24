@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-datatable text-nowrap">

                <!-- Header -->
                <div class="row card-header flex-column flex-md-row pb-0">
                    <div class="col-md-auto me-auto">
                        <h5 class="card-title">Batch List</h5>
                    </div>

                </div>
                <x-datatable-search />

                <table id="batchTable" class="table table-bordered table-striped dt-responsive nowrap w-100 mt-4 mb-5">
                    <thead class="table-light">
                        <tr>
                            <th>Sr.no</th>
                            <th>Warehouse </th>
                            <th>Category </th>
                            <th>Product </th>
                            <th>Quantity </th>
                            <th>Type </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sellProducts as $item)
                            <tr>
                                <td style="width: 30px;">{{ $loop->iteration }}</td>
                                <td>{{ $item->warehouse->name ?? '-' }}</td>

                                <td>{{ $item->batch->product->category->name ?? '-' }}</td>

                                <td>{{ $item->batch->product->name ?? '-' }}</td>

                                <td>{{ $item->quantity }}</td>

                                <td>
                                    <span class="badge {{ $item->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                        {{ strtoupper($item->type) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


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
