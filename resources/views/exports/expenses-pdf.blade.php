<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 28px; }
        .header { margin-bottom: 24px; }
        .eyebrow { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-bottom: 6px; }
        .title { font-size: 24px; font-weight: bold; color: #7f1d1d; margin: 0; }
        .subtitle { margin-top: 6px; color: #475569; }
        .summary { width: 100%; margin: 20px 0 24px; border-collapse: separate; border-spacing: 10px 0; }
        .summary td { width: 33.33%; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 14px; vertical-align: top; }
        .summary-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1.6px; color: #9a3412; margin-bottom: 8px; }
        .summary-value { font-size: 16px; font-weight: bold; color: #7c2d12; }
        .summary-note { margin-top: 4px; font-size: 10px; color: #9a3412; }
        table.report { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.report th { background: #b42318; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 10px 8px; text-align: left; }
        table.report td { border-bottom: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: top; }
        table.report tbody tr:nth-child(even) { background: #fff7ed; }
        .text-right { text-align: right; }
        .footer { margin-top: 18px; font-size: 10px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">ShoeClean Report</div>
        <h1 class="title">Laporan Pengeluaran Outlet</h1>
        <div class="subtitle">Periode {{ $month }} {{ $year }} • Dibuat pada {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-value">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                <div class="summary-note">Akumulasi pengeluaran dalam periode ini</div>
            </td>
            <td>
                <div class="summary-label">Jumlah Catatan</div>
                <div class="summary-value">{{ number_format($expenses->count()) }}</div>
                <div class="summary-note">Semua transaksi pengeluaran yang tercatat</div>
            </td>
            <td>
                <div class="summary-label">Outlet Tercakup</div>
                <div class="summary-value">{{ number_format($outletCount) }}</div>
                <div class="summary-note">Jumlah outlet yang masuk ke laporan ini</div>
            </td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Outlet</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td>{{ $expense->outlet->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 18px; color: #64748b;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini membantu owner memantau arus biaya outlet dan meninjau kategori pengeluaran yang paling sering muncul.
    </div>
</body>
</html>
