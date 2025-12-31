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
            <select name="warehouse_id" class="form-select">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>
                        {{ $w->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="product_id" class="form-select">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('warehouse-stock.report') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Sr No</th>
                    <th>Warehouse</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $index => $stock)
                <tr>
                    <td>{{ $stocks->firstItem() + $index }}</td>
                    <td>{{ $stock->warehouse->name ?? '-' }}</td>
                    <td>{{ $stock->category->name ?? '-' }}</td>
                    <td>{{ $stock->product->name ?? '-' }}</td>
                    <td class="fw-bold">{{ $stock->quantity }}</td>
                    <td>{{ $stock->updated_at->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No stock found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-2">
        {{ $stocks->links() }}
    </div>
</div>

</div>
@endsection
