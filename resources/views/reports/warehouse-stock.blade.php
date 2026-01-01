@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Warehouse Stock Report</h5>
            </div>

            <!-- Filters -->
            <form method="POST" action="{{ route('warehouse-stock.report') }}" class="row g-2 p-3">
                @csrf

                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>In</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Out</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Filter
                    </button>

                    <a href="{{ route('warehouse-stock.report') }}" class="btn btn-secondary btn-sm">
                        Reset
                    </a>

                    <!-- ✅ CSV DOWNLOAD BUTTON -->
                    <button type="submit" name="download" value="csv" class="btn btn-success btn-sm">
                        Download CSV
                    </button>
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
                            <th>Created Date</th>
                            <th>Updated Date</th>
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
                                <td>{{ \Carbon\Carbon::parse($ws['created_at'])->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($ws['updated_at'])->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-muted">No stock data found</td>
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
