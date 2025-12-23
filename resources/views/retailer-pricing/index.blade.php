@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Retailer pricing</h5>
                </div>
                <div class="col-md-auto ms-auto mt-5">
                    <a href="{{ route('retailer-pricing.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Add Retailer Price
                    </a>
                </div>
            </div><br><br>
            <!-- Search -->
            <x-datatable-search />
            <table id="transfersTable" class="table table-bordered table-striped mt-4 mb-5">
                                            <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Retailer</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Base Price (₹)</th>
                        <th>Discount %</th>
                        <th>Discount Amt (₹)</th>
                        <th>Effective Price (₹)</th>
                        <th>Effective From</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pricings as $index => $p)
                        <tr>
                            <td>{{ $pricings->firstItem() + $index }}</td>
                            <td>{{ $p->retailer->name ?? '-' }}</td>
                            <td>{{ $p->category->name ?? '-' }}</td>
                            <td>{{ $p->product->name ?? '-' }}</td>
                            <td>{{ number_format($p->base_price, 2) }}</td>
                            <td>{{ $p->discount_percent ?? 0 }}%</td>
                            <td>{{ number_format($p->discount_amount, 2) }}</td>
                            <td><strong>{{ number_format($p->effective_price, 2) }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($p->effective_from)->format('d-m-Y') }}</td>

                            <td>
                                <span style="color: {{ $p->is_active ? 'green' : 'red' }}">
                                    {{ $p->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('retailer-pricing.edit', $p->id) }}">Edit</a>

                                <form action="{{ route('retailer-pricing.delete', $p->id) }}"
                                    method="POST"
                                    style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Delete pricing?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" align="center">No pricing found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $pricings->links() }}


</div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#transfersTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'desc']
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search transfers..."
            }
        });
    });
</script>

<!-- table search box script -->


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

            // Skip "No role found" row
            if (row.cells.length === 1) return;

            row.style.display = row.textContent
                .toLowerCase()
                .includes(value)
                ? ""
                : "none";
        });
    });

});
</script>

@endsection