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

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }
    </style>
</head>

<body>

    {{-- ================= HEADER ================= --}}
    <div class="center bold">SAMRUDDH KIRANA</div>
    <div class="center small">
        GSTIN: 27ABCDE1234F1Z5<br>
        FSSAI Lic No: 11521036000569<br>
        Pune, Maharashtra
    </div>

    <div class="line"></div>

    {{-- ================= SAVINGS ================= --}}
    <div class="center bold">
        YOU HAVE SAVED Rs. {{ number_format($order->discount,2) }}
    </div>

    <div class="line"></div>

    {{-- ================= BILL META ================= --}}
    <div class="center bold small">
        TAX INVOICE<br>
        ******** Original for Recipient ********
    </div>

    <div class="center small">
        Place of Supply & State Code: 27 MH<br>
        Customer Type: {{ $order->user_id ? 'REG' : 'URD' }}
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

    {{-- ================= ITEMS HEADER ================= --}}
    <table class="small bold">
        <tr>
            <td>HSN</td>
            <td>Item</td>
            <td class="right">Rate</td>
            <td class="right">Qty</td>
            <td class="right">Value</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- ================= ITEMS ================= --}}
    @php
    $gstGroups = $order->items->groupBy('tax_percent');
    @endphp

    <table class="small">
        @foreach($gstGroups as $rate => $rows)

        <tr>
            <td colspan="5" class="center bold">
                CGST @ {{ $rate/2 }}% &nbsp; SGST @ {{ $rate/2 }}%
            </td>
        </tr>

        @foreach($rows as $item)
        <tr>
            <td>{{ $item->product->barcode ?? '-' }}</td>
            <td>{{ $item->product->name }}</td>
            <td class="left">{{ number_format($item->price,2) }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->line_total,2) }}</td>
        </tr>
        @endforeach

        @endforeach
    </table>

    <div class="line"></div>

    {{-- ================= SUMMARY ================= --}}
    @php
    $totalQty = $order->items->sum('quantity');
    @endphp

    <table class="small">
        <tr>
            <td>Items</td>
            <td class="right">{{ $order->items->count() }}</td>
        </tr>
        <tr>
            <td>Quantity</td>
            <td class="right">{{ $totalQty }}</td>
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
    </table>

    <div class="line"></div>

    {{-- ================= GST BREAKUP ================= --}}
    <div class="center bold small">
        ----- GST Breakup Details (INR) -----
    </div>

    <table class="small">
        <tr class="bold">
            <td>GST%</td>
            <td class="right">Taxable</td>
            <td class="right">CGST</td>
            <td class="right">SGST</td>
            <td class="right">CESS</td>
            <td class="right">Total</td>
        </tr>

        @foreach($gstGroups as $rate => $rows)
        @php
        $taxable = $rows->sum('line_total') - $rows->sum('tax_amount');
        $cgst = $rows->sum('tax_amount') / 2;
        $sgst = $rows->sum('tax_amount') / 2;
        $total = $rows->sum('line_total');
        @endphp
        <tr>
            <td>{{ $rate }}%</td>
            <td class="right">{{ number_format($taxable,2) }}</td>
            <td class="right">{{ number_format($cgst,2) }}</td>
            <td class="right">{{ number_format($sgst,2) }}</td>
            <td class="right">0.00</td>
            <td class="right">{{ number_format($total,2) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    {{-- ================= PAYMENT ================= --}}
    <div class="center small">
        Payment Mode: {{ strtoupper($order->payment->payment_gateway ?? 'CASH') }}<br>
        Amount Paid: Rs {{ number_format($order->total_amount,2) }}
    </div>

    <div class="line"></div>

    <div class="center small">
        * Thank You For Shopping With Us *<br>
        Visit Again 🙏<br>
        This Invoice is Computer Generated
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => {
                window.location.href = "{{ route('pos.create') }}";
            }, 800);
        };
    </script>

</body>

</html>