<!DOCTYPE html>

<html>

<head>
    <title>Invoice</title>
    <style>
        body { font-family: Arial; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        @media print {
            button { display: none; }
        }
    </style>
</head>

<body onload="window.print()">

    <h2 style="text-align:center;">INVOICE</h2>

    <table width="100%" style="border:none !important;" cellspacing="0" cellpadding="8">
        <tr style="text-align: left !important;  padding:10px">
            <td width="50%" style="border:none !important;">
                <strong>Invoice No:</strong> {{ $po->po_number }}<br>
                <strong>Order Date:</strong>
                {{ \Carbon\Carbon::parse($po->po_date)->format('d M, Y') }}
            </td>

            <td width="50%" style="text-align: right; border:none; padding:10px;">
                <img 
                    src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}" 
                    alt="Company Logo"
                    style="max-width:200px; margin-bottom:-20px;"
                >

                <p style="border-left:1px solid black; padding-right: 50px;">
                    <strong>Smrudh Kirana</strong><br>
                    Hadapser, Pune<br>
                    Phone: +918421309533
                </p>
            </td>

        </tr>
    </table>


    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">₹{{ $item->price }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">₹{{ $item->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table style="border-collapse:collapse;">
        <tr>
            
            <td class="text-right" style="border:none;">Subtotal &nbsp; ₹{{ $po->subtotal }}</td>
        </tr>
        <tr>
            
            <td class="text-right" style="border:none;">Tax &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  ₹{{ $po->tax }}</td>
        </tr>
        <tr>
            
            <td class="text-right" style="border:none;">Shipping &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₹{{ $po->shipping_charge }}</td>
        </tr>
        <tr>
            
            <td class="text-right" style="border:none;">Discount &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₹{{ $po->discount }}</td>
        </tr>
        <tr>
            
            <td class="text-right" style="border:none;"><strong>Grand Total</strong>&nbsp;<strong> ₹{{ $po->grand_total }}</strong></td>
        </tr>
    </table>

    <p>
    <br>
        Hello ,
        Thank you for shopping from our store and for your order. it is really awesome to have you as one of our paid users. We
        hope that you will be happy with Qlearly, if you ever have any questions, suggestions or concerns please do not hesitate to
        contact us.
    </p><br><br>

    <p style="text-align: left;">
        Best Regards, <br>
        Smrudh Kirana <br>
        Email: smridh@support.com <br>
        Website: http://samrudhi.kirana.com
    </p>

</body>

</html>
