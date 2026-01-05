@extends('layouts.app')

@section('content')
<div class="container-xxl">

    

    <div class="card mt-5 p-3">

        <h4 class="mb-3">
            Low Stock Alert (Below - {{ $threshold }}) QTY
        </h4>

        <div class="table-responsive p-3">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Warehouse</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($stocks as $stock)
                        <tr style="background:#fff3cd">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $stock->warehouse->name ?? '-' }}</td>
                            <td>{{ $stock->product->name ?? '-' }}</td>
                            <td>{{ $stock->category->name ?? '-' }}</td>
                            <td class="fw-bold text-danger">
                                {{ $stock->quantity }}
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    LOW STOCK
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                ✅ No Low Stock Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
