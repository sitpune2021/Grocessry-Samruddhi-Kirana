<!DOCTYPE html>
<html>
<head>
    <title>POS Invoice</title>

    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 8px;
        }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; }
        td { vertical-align: top; }
        .right { text-align: right; }
    </style>
</head>

<body>

<div class="center">
    <strong>SAMRUDDH KIRANA</strong><br>
    Grocery & Daily Needs<br>
    ---------------------<br>
    Bill No: {{ $order->order_number }}<br>
    {{ $order->created_at->format('d-m-Y h:i A') }}
</div>

<div class="line"></div>

<table>
@foreach($order->items as $item)
<tr>
    <td colspan="2">
        {{ $item->product->name }}
    </td>
</tr>
<tr>
    <td>
        {{ $item->quantity }} x {{ number_format($item->price,2) }}
    </td>
    <td class="right">
        {{ number_format($item->line_total,2) }}
    </td>
</tr>
@endforeach
</table>

<div class="line"></div>

<table>
<tr>
    <td>Subtotal</td>
    <td class="right">{{ number_format($order->subtotal,2) }}</td>
</tr>
<tr>
    <td>GST</td>
    <td class="right">
        {{ number_format($order->items->sum('tax_amount'),2) }}
    </td>
</tr>
<tr>
    <td><strong>Total</strong></td>
    <td class="right"><strong>{{ number_format($order->total_amount,2) }}</strong></td>
</tr>
</table>

<div class="line"></div>

<div class="center">
    Payment: {{ strtoupper($order->payment->payment_gateway ?? 'CASH') }}<br>
    <br>
    Thank You 🙏<br>
    Visit Again
</div>

<script>
    window.onload = function () {
        window.print();

        // Redirect back to POS after print
        setTimeout(() => {
            window.location.href = "{{ route('pos.create') }}";
        }, 800);
    };
</script>

</body>
</html>
