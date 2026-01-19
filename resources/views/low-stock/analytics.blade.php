@extends('layouts.app')

@section('content')
<div class="container-xxl">

    <div class="card mt-5 p-3">
        
        <h4>Low Stock Analytics</h4>

        <div class="row mt-3">

            <div class="col-md-4">
                <div class="card text-center p-3">
                    <h6>Total Low Stock Products</h6>
                    <h2 class="text-danger">{{ $totalLowStock }}</h2>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-3">
                    <h6>Warehouse Wise</h6>

                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Warehouse</th>
                                <th>Low Stock Product Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouseWise as $row)
                            <tr>
                                <td>{{ $row->warehouse->name ?? '-' }}</td>
                                <td class="text-danger fw-bold">
                                    {{ $row->total }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection
