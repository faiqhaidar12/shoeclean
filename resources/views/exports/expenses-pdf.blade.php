<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expenses Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #EF4444; }
        .meta { text-align: center; margin-bottom: 20px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #EF4444; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .total { margin-top: 20px; text-align: right; font-size: 14px; font-weight: bold; color: #EF4444; }
    </style>
</head>
<body>
    <h1>👟 Shoe Clean - Expenses Report</h1>
    <div class="meta">{{ $month }} {{ $year }}</div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Outlet</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->description ?? '-' }}</td>
                <td>{{ $expense->outlet->name }}</td>
                <td style="text-align: right;">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Expenses: Rp {{ number_format($totalExpenses, 0, ',', '.') }}
    </div>
</body>
</html>
