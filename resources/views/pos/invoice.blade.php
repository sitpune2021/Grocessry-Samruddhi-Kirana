<!DOCTYPE html>
<!-- <html> -->
<!-- <head>
    <title>POS Invoice</title>

    <style>
          @page {
        size: 80mm auto;
        margin: 0;
    }
     body {
        width: 80mm;
        font-family: monospace;
        font-size: 11px;
        margin: 0;
        padding: 6px;
    }

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
</head> -->

<!-- <body>

<div class="center">
    <strong>SAMRUDDH KIRANA</strong><br>
    GST No :DEMO-DE1234F1Z5<br>
    ------------------------------------------<br>
    YOU HAVE SAVED RS.432,222.00<br>
    ------------------------------------------<br>
    TAX Invoice<br>
    ********* Original for Recipiant *********<br>
    Place of Supply & StateCode : 27 MH(Demo) <br>
    Customer Type : URD <br>
    date :{{ $order->created_at->format('d-m-Y h:i A') }} <br>
    Bill No: {{ $order->order_number }}<br>
    

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

</body> -->
<!-- </html> -->
<!DOCTYPE html>
<html>
<head>
<title>POS Invoice</title>

<style>
@page {
    size: 80mm auto;
    margin: 0;
}
body {
    width: 80mm;
    font-family: monospace;
    font-size: 11px;
    margin: 0;
    padding: 6px;
}
.center { text-align: center; }
.right { text-align: right; }
.line { border-top: 1px dashed #000; margin: 6px 0; }
table { width: 100%; border-collapse: collapse; }
td { vertical-align: top; }
.small { font-size: 10px; }
.bold { font-weight: bold; }
</style>
</head>

<body>

<!-- HEADER -->
<div class="center bold">
    SAMRUDDH KIRANA<br>
</div>

<div class="center small">
    GSTIN: 27ABCDE1234F1Z5<br>
    FSSAI Lic No: 11521036000569<br>
    Pune, Maharashtra<br>
</div>

<div class="line"></div>

<div class="center bold">
    YOU HAVE SAVED Rs. {{ number_format($order->discount,2) }}<br>
</div>

<div class="line"></div>

<div class="center bold">
    TAX INVOICE<br>
    ******** Original for Recipient ********
</div>

<div class="small" style="text-align: center;">
    Place of Supply & State Code: 27 MH<br>
    Customer Type: {{ $order->user_id ? 'REG' : 'URD' }}<br>
    
</div>
<div class="small" style="width:90%; overflow:hidden;">
    <div style="float:left; text-align:left;">
        Date: {{ $order->created_at->format('d/m/Y H:i:s') }}<br>
        Bill No: {{ $order->order_number }}
    </div>

    <div style="float:right; text-align:right;">
        POS: {{ $order->warehouse_id }}<br>
        Cashier: {{ $order->createdBy->name ?? 'SYSTEM' }}
    </div>
</div>

<div class="line"></div>

<!-- ITEM HEADER -->
<table class="small bold" style="width:90%">
<tr>
     <td>HSN Code</td>
    <td>Item Description</td>
    <td class="right">Rate</td>
    <td class="right">Qty</td>
    <td class="right">Value</td>
</tr>
</table>

<div class="line"></div>

<!-- ITEMS -->
<table class="small" style="width:90%">
@foreach($order->items as $item)
<tr>
    <td colspan="5" style="text-align: center; font-weight: 800;">
        1) CGST @ 0.00% SGST @ 0.00%
    </td>
</tr>

<tr>
    <td >12345678</td>
    <td  >{{ $item->product->name }}</td>
  
    <td class="right">{{ number_format($item->price,2) }}</td>
    <td class="right">{{ $item->quantity }}</td>
    <td class="right">{{ number_format($item->line_total,2) }}</td>
</tr>
@endforeach
</table>

<div class="line"></div>

<!-- SUMMARY --> 
<table class="small" style="width:90%">
<tr>
    <td>Items:</td>
    <td class="right">{{ $order->items->count() }}</td>
</tr>
<tr>
    <td>Gross Sales Value</td>
    <td class="right">{{ number_format($order->subtotal + $order->discount,2) }}</td>
</tr>
<tr>
    <td>Total Discount</td>
    <td class="right">{{ number_format($order->discount,2) }}</td>
</tr>
<tr class="bold">
    <td>Net Sales Value (Incl GST)</td>
    <td class="right">{{ number_format($order->total_amount,2) }}</td>
</tr>
<tr class="bold">
    <td>Total Amount Paid</td>
    <td class="right">{{ number_format($order->total_amount,2) }}</td>
</tr>
<tr class="bold">
    <td colspan="2" style="text-align: center;">{ AMOUNT INCLUSIVE OF APPLICABLE TAXES }</td>
   
</tr>
</table>

<div class="line"></div>

<!-- GST BREAKUP -->
<div class="bold small" style="text-align: center; font-weight: 800;">------GST Breakup Details----- Amount (INR)</div>

<table class="small" style="width:90%">
<tr class="bold">
    <td>GST IND</td>
    <td class="right">Taxable Amount</td>
    <td class="right">CGST</td>
    <td class="right">SGST</td>
     <td class="right">CESS</td>
     <td class="right">Total Amount</td>
</tr>

@php
$gstGroups = $order->items->groupBy('tax_percent');
@endphp

@foreach($gstGroups as $rate => $rows)
@php
$taxable = $rows->sum('line_total') - $rows->sum('tax_amount');
$cgst = $rows->sum('tax_amount') / 2;
$sgst = $rows->sum('tax_amount') / 2;
@endphp
<tr>
    <td>{{ $rate }}%</td>
    <td class="right">{{ number_format($taxable,2) }}</td>
    <td class="right">{{ number_format($cgst,2) }}</td>
    <td class="right">{{ number_format($sgst,2) }}</td>
     <td class="right">{{ number_format($sgst,2) }}</td>
        <td class="right">{{ number_format($sgst,2) }}</td>
</tr>
@endforeach
</table>

<div class="line"></div>

<!-- PAYMENT -->
<div class="center small">
    Payment: {{ strtoupper($order->payment->payment_gateway ?? 'CASH') }}<br>
    Amount Paid: Rs {{ number_format($order->total_amount,2) }}<br>
</div>

<div class="line"></div>

<div class="center small">
    * Thank You For Shopping With Us *<br>
    Visit Again 🙏
    <br>
    This Invoice Computer Generated
</div>

<script>
window.onload = function () {
    window.print();
    setTimeout(() => {
        window.location.href = "{{ route('pos.create') }}";
    }, 800);
};
</script>

</body>
</html>
