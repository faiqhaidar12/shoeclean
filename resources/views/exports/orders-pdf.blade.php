<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 28px; }
        .header { margin-bottom: 24px; }
        .eyebrow { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-bottom: 6px; }
        .title { font-size: 24px; font-weight: bold; color: #0f2e27; margin: 0; }
        .subtitle { margin-top: 6px; color: #475569; }
        .summary { width: 100%; margin: 20px 0 24px; border-collapse: separate; border-spacing: 10px 0; }
        .summary td { width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; vertical-align: top; }
        .summary-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1.6px; color: #64748b; margin-bottom: 8px; }
        .summary-value { font-size: 16px; font-weight: bold; color: #0f172a; }
        .summary-note { margin-top: 4px; font-size: 10px; color: #64748b; }
        table.report { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.report th { background: #0f2e27; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 10px 8px; text-align: left; }
        table.report td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: top; }
        table.report tbody tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-muted { background: #e2e8f0; color: #475569; }
        .footer { margin-top: 18px; font-size: 10px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">ShoeClean Report</div>
        <h1 class="title">Laporan Pesanan Outlet</h1>
        <div class="subtitle">Periode {{ $month }} {{ $year }} • Dibuat pada {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-label">Total Pesanan</div>
                <div class="summary-value">{{ number_format($orders->count()) }}</div>
                <div class="summary-note">Seluruh order dalam periode ini</div>
            </td>
            <td>
                <div class="summary-label">Nilai Order</div>
                <div class="summary-value">Rp {{ number_format($totalOrderValue, 0, ',', '.') }}</div>
                <div class="summary-note">Akumulasi total nilai transaksi</div>
            </td>
            <td>
                <div class="summary-label">Sudah Lunas</div>
                <div class="summary-value">Rp {{ number_format($paidOrderValue, 0, ',', '.') }}</div>
                <div class="summary-note">{{ number_format($orders->where('payment_status', 'paid')->count()) }} order terverifikasi</div>
            </td>
            <td>
                <div class="summary-label">Butuh Tindak Lanjut</div>
                <div class="summary-value">{{ number_format($waitingConfirmationCount + $unpaidCount) }}</div>
                <div class="summary-note">{{ number_format($waitingConfirmationCount) }} menunggu verifikasi • {{ number_format($unpaidCount) }} belum lunas</div>
            </td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Outlet</th>
                <th>Status Order</th>
                <th>Metode Bayar</th>
                <th>Status Pembayaran</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->outlet->name ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                    <td>{{ $order->paymentMethodLabel() }}</td>
                    <td>
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : ($order->payment_status === 'waiting_confirmation' ? 'badge-warning' : 'badge-muted') }}">
                            {{ $order->paymentStatusLabel() }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 18px; color: #64748b;">Tidak ada data pesanan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini menampilkan nilai order dan status pembayaran agar owner dapat membedakan transaksi yang sudah lunas dan yang masih perlu ditindaklanjuti.
    </div>
</body>
</html>
