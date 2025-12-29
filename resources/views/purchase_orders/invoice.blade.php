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

<h2>INVOICE</h2>

    <p>
        <strong>Invoice No:</strong> {{ $po->po_number }} <br>
        <strong>Order Date:</strong> {{ \Carbon\Carbon::parse($po->po_date)->format('d M, Y') }}
    </p>

<hr>

    <p>
        <strong>Smrudh Kirana</strong><br>
        Hadapser, Pune<br>
        Phone: +8421309533
    </p>

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

    <table>
        <tr>
            <td class="text-right">Subtotal</td>
            <td class="text-right">₹{{ $po->subtotal }}</td>
        </tr>
        <tr>
            <td class="text-right">Tax</td>
            <td class="text-right">₹{{ $po->tax }}</td>
        </tr>
        <tr>
            <td class="text-right">Shipping</td>
            <td class="text-right">₹{{ $po->shipping_charge }}</td>
        </tr>
        <tr>
            <td class="text-right">Discount</td>
            <td class="text-right">₹{{ $po->discount }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Grand Total</strong></td>
            <td class="text-right"><strong>₹{{ $po->grand_total }}</strong></td>
        </tr>
    </table>

    <p>
    <br>
        Hello ,
        Thank you for shopping from our store and for your order. it is really awesome to have you as one of our paid users. We
        hope that you will be happy with Qlearly, if you ever have any questions, suggestions or concerns please do not hesitate to
        contact us.
    </p><br><br>

    <p>
        Best Regards,
        Smrudh Kirana
        Email: smridh@support.com
        Website: http://samrudhi.kirana.com
    </p>

</body>

</html>
