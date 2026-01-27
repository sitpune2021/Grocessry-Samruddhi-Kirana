@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">Stock Return Report</h5>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('stock-returns.report') }}" class="row g-2 p-3">

            <div class="col-md-3">
                <select name="warehouse_id" class="form-select">
                    <option value="">Warehouse (All)</option>
                    @foreach (DB::table('warehouses')->get() as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>

            <div class="col-md-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('stock-returns.report') }}" class="btn btn-secondary btn-sm">Reset</a>
                <button type="submit" name="download" value="csv" class="btn btn-success btn-sm">
                    Download CSV
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive p-3">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th>Sr No</th>
                        <th>Return No</th>
                        <th>From Warehouse</th>
                        <th>To Warehouse</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Return Qty</th>
                        <th>Received Qty</th>
                        <th>Damaged Qty</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th>Created Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->return_number }}</td>
                            <td>{{ $row->from_warehouse ?? '-' }}</td>
                            <td>{{ $row->to_warehouse ?? '-' }}</td>
                            <td>{{ $row->product_name ?? '-' }}</td>
                            <td>{{ $row->batch_no ?? '-' }}</td>
                            <td class="fw-bold text-danger">{{ $row->return_qty }}</td>
                            <td>{{ $row->received_qty }}</td>
                            <td>{{ $row->damaged_qty }}</td>
                            <td>{{ $row->condition }}</td>
                            <td>{{ $row->status }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-muted text-center">
                                No stock return data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>
@endsection
