<!DOCTYPE html>

<html>

<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <h2 style="text-align:center;">Perchase Order</h2>

    <table width="100%" style="border:none !important;" cellspacing="0" cellpadding="8">
        <tr style="text-align: left !important;  padding:10px">
            <td width="50%" style="border:none !important;">
                <strong>PO No:</strong> {{ $po->po_number }}<br>
                <strong>Order Date:</strong>
                {{ \Carbon\Carbon::parse($po->po_date)->format('d M, Y') }}
            </td>

            <td width="50%" style="text-align: right; border:none; padding:10px;">
                <img
                    src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
                    alt="Company Logo"
                    style="max-width:200px; margin-bottom:-20px;">

                    @php
    $user = auth()->user();
@endphp
                @if($warehouse)
                <p style="border-left:1px solid black; padding-right:50px;">
                    <strong>{{ $warehouse->name }}</strong><br>
                    {{ $warehouse->address }}<br>
                    Phone: {{ $user->mobile ?? 'N/A' }}
                </p>
                @else
                <p class="text-danger">
                    Warehouse details not available
                </p>
                @endif
            </td>

        </tr>
    </table>


    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td colspan="3" class="text-right">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>


    <p style="text-align: left;">
        Best Regards, <br>
        Smrudh Kirana <br>
        Email: smridh@support.com <br>
        Website: http://samrudhi.kirana.com
    </p>

</body>

</html>