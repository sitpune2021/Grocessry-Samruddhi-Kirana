<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Batch Expiry Alert</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f6f6; padding:20px;">

    <div style="max-width:700px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">
        <h2 style="color:#d9534f; text-align:center;">
            ⚠️ Product Batch Expiry Alert
        </h2>

        <p>Hello User,</p>

        <p>
            The following product batches are <strong>expiring soon or already expired</strong>.
            Please take necessary action.
        </p>

        <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">
            <thead style="background:#f2f2f2;">
                <tr>
                    <th align="left">Product</th>
                    <th align="left">Batch No</th>
                    <th align="center">Quantity</th>
                    <th align="center">Expiry Date</th>
                    <th align="center">Days Left</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batches as $batch)
                    <tr>
                        <td>{{ $batch['product'] }}</td>
                        <td>{{ $batch['batch_no'] }}</td>
                        <td align="center">{{ $batch['quantity'] }}</td>
                        <td align="center">{{ $batch['expiry_date'] }}</td>
                        <td align="center"
                            style="color:{{ $batch['days_left'] < 0 ? 'red' : '#b8860b' }}">
                            {{ $batch['days_left'] < 0 ? 'Expired' : $batch['days_left'].' days' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top:20px;">
            Regards,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>

</body>
</html>
