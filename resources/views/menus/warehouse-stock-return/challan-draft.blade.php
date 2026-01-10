<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background: #f2f2f2;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2 class="center">STOCK RETURN CHALLAN</h2>

    <p>
        <strong>Return No:</strong> SR-{{ $return->id }}<br>
        <strong>Date:</strong> {{ $return->created_at->format('d-m-Y') }}<br>
        <strong>Status:</strong> {{ ucfirst($return->status) }}
    </p>

    <hr>

    <h4>Warehouse Details</h4>
    <table>
        <tr>
            <td width="50%">
                <strong>From Warehouse</strong><br>
                {{ $return->fromWarehouse->name }}
            </td>
            <td width="50%">
                <strong>To Warehouse</strong><br>
                {{ $return->toWarehouse->name }}
            </td>
        </tr>
    </table>

    <br>

    <h4>Returned Items</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Batch</th>
                <th>Return Qty</th>
                <th>Received Qty</th>
                <th>Damaged Qty</th>
            </tr>
        </thead>
        <tbody>
            @if($return->WarehouseStockReturnItem->count())
            @foreach($return->WarehouseStockReturnItem as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->batch_no ?? '-' }}</td>
                <td class="text-center">{{ $item->return_qty }}</td>
                <td class="text-center">{{ $item->received_qty }}</td>
                <td class="text-center">{{ $item->damaged_qty }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="6" class="text-center">No items found</td>
            </tr>
            @endif
        </tbody>

    </table>

    <br>

    <p>
        <strong>Return Reason:</strong> {{ $return->return_reason }}<br>
        <strong>Remarks:</strong> {{ $return->remarks }}
    </p>

    <br><br>

    <table>
        <tr>
            <td class="center">Prepared By</td>
            <td class="center">Approved By</td>
            <td class="center">Received By</td>
        </tr>
    </table>

    <br><br>
    <div style="text-align:center; margin-bottom:10px;">
        <button onclick="window.print()" style="
        padding:6px 12px;
        background:#000;
        color:#fff;
        border:none;
        cursor:pointer;
        font-size:12px;
    ">
            🖨 Print
        </button>
    </div>

</body>


</html>