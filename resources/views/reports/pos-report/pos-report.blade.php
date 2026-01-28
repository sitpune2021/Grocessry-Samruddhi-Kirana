@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="card shadow-sm">

            <div class="card-header">
                <h5 class="mb-0">POS Walk-in Sales Report</h5>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('pos-report') }}" class="row g-2 p-3">

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
                    <button class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('pos-report') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                            <th>SR NO</th>
                            <th>Order No</th>
                            <th>Warehouse</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Line Total</th>
                            <th>Order Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->order_number }}</td>
                                <td>{{ $row->warehouse_name ?? '-' }}</td>
                                <td>{{ $row->product_name ?? '-' }}</td>
                                <td>{{ $row->quantity }}</td>
                                <td>{{ number_format($row->price, 2) }}</td>
                                <td>{{ number_format($row->line_total, 2) }}</td>
                                <td class="fw-bold">{{ number_format($row->total_amount, 2) }}</td>
                                <td>{{ $row->payment_method ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $row->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ strtoupper($row->payment_status) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-muted text-center">
                                    No walk-in POS data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
@endsection
