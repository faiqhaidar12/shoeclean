<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #4F46E5; }
        .meta { text-align: center; margin-bottom: 20px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4F46E5; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total { margin-top: 20px; text-align: right; font-size: 14px; font-weight: bold; }
        .paid { color: green; }
        .unpaid { color: orange; }
    </style>
</head>
<body>
    <h1>👟 Shoe Clean - Orders Report</h1>
    <div class="meta">{{ $month }} {{ $year }}</div>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Outlet</th>
                <th>Status</th>
                <th>Payment</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->invoice_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->outlet->name }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td class="{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Revenue: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
    </div>
</body>
</html>
