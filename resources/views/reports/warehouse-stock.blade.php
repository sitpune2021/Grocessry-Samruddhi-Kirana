@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Warehouse Stock Report</h5>
            </div>

            <!-- Filters -->
            <form method="GET" class="row g-2 p-3">
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>IN</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>OUT</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>TRANSFER</option>
                    </select>
                </div>


                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('warehouse-stock.report') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Sr. No</th>
                            <th>Warehouse From</th>
                            <th>Warehouse To</th>
                            <th>Stock In</th>
                            <th>Stock Out</th>
                            <th>Transfer In</th>
                            <th>Transfer Out</th>
                            <th>Remaining Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouseStock as $index => $ws)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ws['warehouse_from'] }}</td>
                                <td>{{ $ws['warehouse_name'] }}</td>
                                <td>{{ $ws['stock_in'] }}</td>
                                <td>{{ $ws['stock_out'] }}</td>
                                <td>{{ $ws['transfer_in'] }}</td>
                                <td>{{ $ws['transfer_out'] }}</td>
                                <td class="fw-bold">{{ $ws['remaining'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">No stock data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
            <div class="p-2">
                {{-- {{ $stocks->links() }} --}}
            </div>
        </div>
    </div>
@endsection
