@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Retailer profile</h5>
                </div>
                <div class="col-md-auto ms-auto mt-5">
                    <a href="{{ route('retailers.create') }}" class="btn btn-primary">
                         Add Retailer
                    </a>
                </div>
            </div><br><br>
            <!-- Search -->
            <x-datatable-search />
                <table id="transfersTable" class="table table-bordered table-striped mt-4 mb-5">
                                <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($retailers as $retailer)
                        <tr>
                            <td>{{ $retailer->name }}</td>
                            <td>{{ $retailer->mobile }}</td>

                            <td>
                                @if($retailer->is_active)
                                    <span class="text-green-600">Active</span>
                                @else
                                    <span class="text-red-600">Inactive</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('retailers.edit', $retailer->id) }}">Edit</a>

                                <form method="POST" action="/retailers/{{ $retailer->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>


                                <form method="POST" action="{{ route('retailers.toggle.status', $retailer->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit">
                                        {{ $retailer->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

{{ $retailers->links() }}

 </div>
    </div>
</div>

@endsection



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