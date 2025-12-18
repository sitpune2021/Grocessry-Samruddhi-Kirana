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

                <table class="table table-bordered">
                    
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Batch</th>
                            <th>Qty</th>
                            <th>MFG</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($batches as $batch)
                        <tr 
                            @if($batch->expiry_date < now()->toDateString())
                                style="background-color:#f8d7da"  {{-- expired --}}
                            @elseif($batch->expiry_date <= now()->addDays(7)->toDateString())
                                style="background-color:#fff3cd"  {{-- expiring soon --}}
                            @endif
                        >
                            <td>{{ $batch->product->name }}</td>
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>{{ $batch->mfg_date }}</td>
                            <td>{{ $batch->expiry_date }}</td>
                            {{-- STATUS COLUMN --}}
                            <td class="text-center">
                                @if($batch->expiry_date < now())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($batch->expiry_date <= now()->addDays(7))
                                    <span class="badge bg-warning text-dark">Expiring Soon</span>
                                @else
                                    <span class="badge bg-success">Normal</span>
                                @endif
                            </td>

                            <td align="center">
                                @if($batch->quantity > 0 && $batch->expiry_date >= now()->toDateString())
                                    <a href="/sale/{{ $batch->product_id }}" title="Sell Product">
                                        🛒 Sell 
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
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('admin/assets/js/datatable-search.js') }}"></script>
@endpush
