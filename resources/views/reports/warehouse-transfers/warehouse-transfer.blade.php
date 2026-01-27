@extends('layouts.app')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Warehouse transfer Report</h5>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('warehouse-stock.report') }}" class="row g-2 p-3">


                <div class="col-md-3">
                    <select name="from_warehouse" class="form-select">
                        <option value="">Approved Warehouse (All)</option>
                        @foreach (DB::table('warehouses')->get() as $wh)
                            <option value="{{ $wh->id }}"
                                {{ request('from_warehouse') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="to_warehouse" class="form-select">
                        <option value="">Request Warehouse (All)</option>
                        @foreach (DB::table('warehouses')->get() as $wh)
                            <option value="{{ $wh->id }}" {{ request('to_warehouse') == $wh->id ? 'selected' : '' }}>
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
                    <a href="{{ route('warehouse-stock.report') }}" class="btn btn-secondary btn-sm">Reset</a>
                    <button type="submit" name="download" value="csv" class="btn btn-success btn-sm">Download
                        CSV</button>
                </div>

            </form>


            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Sr. No</th>
                            <th>Warehouse Request</th>
                            <th>Warehouse Approved</th>                   
                            <th>Transfer In</th>
                             <th>Product</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouseStock as $index => $ws)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $ws['warehouse_name'] }}</td>
                                <td>{{ $ws['warehouse_from'] }}</td>                               
                                <td>{{ $ws['transfer_in'] }}</td>
                                <td>{{ $ws['product_name'] }}</td>
                                <!-- <td class="fw-bold">{{ $ws['quantity'] }}</td> -->
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
