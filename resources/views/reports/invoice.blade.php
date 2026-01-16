<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Thermal printer style */
            font-size: 14px;
            color: #333;
            max-width: 80mm; /* Initial width for thermal, can be overridden by @media print */
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .details {
            margin-bottom: 10px;
        }

        .details p {
            margin: 2px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .table th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        .table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            border-top: 2px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            border-top: 1px dotted #000;
            padding-top: 10px;
        }
        
        @media print {
            body { 
                max-width: 100%; 
                margin: 0; 
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            text-align: center;
            text-decoration: none;
            margin-bottom: 20px;
            font-family: sans-serif;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <a href="#" onclick="window.print(); return false;" class="btn-print no-print">Print Invoice</a>

    <div class="header">
        <h1>SHOE CLEAN</h1>
        <p>{{ $order->outlet->name }}</p>
        <p>{{ $order->outlet->address ?? 'Shoe Care Specialist' }}</p>
    </div>

    <div class="details">
        <p><strong>Invoice:</strong> {{ $order->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Cust:</strong> {{ $order->customer->name }}</p>
        <p><strong>Telp:</strong> {{ $order->customer->phone }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->service->name }}<br>
                    <small>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</small>
                </td>
                <td class="text-right">
                    {{ number_format($item->total_price, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table class="table" style="margin-bottom: 0;">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($order->items->sum('total_price'), 0, ',', '.') }}</td>
            </tr>
            @if($order->pickup_delivery_fee > 0)
            <tr>
                <td>Delivery Fee</td>
                <td class="text-right">{{ number_format($order->pickup_delivery_fee, 0, ',', '.') }}</td>
            </tr>
            @endif
             @if($order->discount_amount > 0)
            <tr>
                <td>Discount</td>
                <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Status Payment: <strong>{{ strtoupper($order->payment_status) }}</strong></p>
        <p>Terima kasih telah mempercayakan sepatu Anda kepada kami!</p>
        <p>www.shoeclean.com</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
