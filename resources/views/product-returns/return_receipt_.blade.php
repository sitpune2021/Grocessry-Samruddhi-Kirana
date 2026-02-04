<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return Handover Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td, th { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>

<div class="title">Return Handover Receipt</div>

<table>
    <tr>
        <th>Return ID</th>
        <td>#{{ $return->id }}</td>
    </tr>
    <tr>
        <th>Customer</th>
        <td>{{ $return->customer->name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th>Product</th>
        <td>{{ $return->product->name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th>Quantity</th>
        <td>{{ $return->quantity }}</td>
    </tr>
    <tr>
        <th>Warehouse</th>
        <td>{{ $return->warehouse->name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th>Delivered By</th>
        <td>{{ $agent->first_name }} {{ $agent->last_name }}</td>
    </tr>
    <tr>
        <th>Returned At</th>
        <td>{{ $return->returned_at }}</td>
    </tr>
</table>

<p style="margin-top: 20px;">
    ✔ This receipt confirms successful handover of returned item to warehouse.
</p>

</body>
</html>
