@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-datatable text-nowrap">

            <!-- Header -->
            <div class="row card-header flex-column flex-md-row pb-0">
                <div class="col-md-auto me-auto">
                    <h5 class="card-title">Expiring Batches (Next 30 Days)</h5>
                </div>
            </div>


            <!-- Search -->
            <x-datatable-search />
            <div class="table-responsive mt-5 p-3">
                <table id="expiry" class="table table-bordered">

                    <thead class="table-light">
                        <tr>
                            <th>Sr No</th>
                            @if(auth()->user()->role_id == 1)
                            <th>Warehouse</th>
                            @endif
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($batches as $batch)

                        @php
                        $rowStyle = '';

                        if ($batch->expiry_date < now()) {
                            // expired
                            $rowStyle='background-color:#f8d7da' ;
                            } elseif ($batch->expiry_date <= now()->addDays(7)) {
                                // expiring soon
                                $rowStyle = 'background-color:#fff3cd';
                                }
                                @endphp

                                <tr style="{{ $rowStyle }}">
                                    <td style="width: 30px;">{{ $loop->iteration }}</td>
                                    @if(auth()->user()->role_id == 1)
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $batch->warehouse->name ?? '-' }}
                                        </span>
                                    </td>
                                    @endif
                                    <td>{{ $batch->product->name ?? '' }}</td>
                                    <td>{{ $batch->batch_no }}</td>
                                    <td>{{ $batch->quantity }}</td>
                                    <td style="width: 50px;">
                                        {{ $batch->mfg_date ? \Carbon\Carbon::parse($batch->mfg_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td style="width: 50px;">
                                        {{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') : '-' }}
                                    </td>

                                    <td align="center" class="text-success">
                                        @if($batch->quantity > 0 && $batch->expiry_date >= now())
                                        <a href="{{ route('sale.create', ['batch_id' => $batch->id]) }}"
                                            title="Sale Product"
                                            class="btn btn-success btn-sm text-white">
                                             Sale <i class="bx bx-cart me-1"></i>
                                        </a>
                                        @else
                                        ❌
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2">
                {{ $batches->onEachSide(0)->links('pagination::bootstrap-5') }}
            </div>
        </div>
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
        const table = document.getElementById("expiry");

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