@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Stock Movement Report</h5>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('stock-movement.report') }}" class="row g-2 p-3">

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

                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>IN</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>OUT</option>
                        <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>TRANSFER</option>
                        <option value="dispatch" {{ request('type') == 'dispatch' ? 'selected' : '' }}>DISPATCH</option>
                        <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>RETURN</option>
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
                    <a href="{{ route('stock-movement.report') }}" class="btn btn-secondary btn-sm">Reset</a>
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
                            <th>Warehouse</th>
                            <th>Type</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Remaining Total Qty</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ DB::table('warehouses')->where('id', $row['warehouse_id'])->value('name') }}
                                </td>

                                <td class="text-uppercase fw-bold">
                                    {{ $row['type'] }}
                                </td>
                                <td>
                                    {{ $row['product_name'] }}
                                </td>
                                <td class="{{ $row['quantity'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $row['quantity'] }}
                                </td>

                                <td class="fw-bold">
                                    {{ (int) $row['remaining_qty'] }}
                                </td>


                                <td>
                                    {{ \Carbon\Carbon::parse($row['created_at'])->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($row['updated_at'])->format('d-m-Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No records found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
