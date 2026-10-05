<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h2 { margin: 0 0 4px; font-size: 16px; }
        .muted { color: #555; font-size: 10px; margin-bottom: 16px; }
        table { width: 60%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; }
        th { background: #f1f5f9; text-align: left; }
        .right { text-align: right; }
        .net { font-weight: bold; background: #ecfdf5; }
    </style>
</head>
<body>
    <h2>{{ $shop->name }} — Profit Summary</h2>
    <div class="muted">Period: {{ $from }} to {{ $to }} · Generated {{ now()->format('d M Y H:i') }}</div>

    <table>
        <tr>
            <th>Metric</th>
            <th class="right">Amount (TZS)</th>
        </tr>
        <tr>
            <td>Revenue (Sales)</td>
            <td class="right">{{ number_format($revenue, 0) }}</td>
        </tr>
        <tr>
            <td>COGS (approx.)</td>
            <td class="right">{{ number_format($cogs, 0) }}</td>
        </tr>
        <tr>
            <td>Gross Profit</td>
            <td class="right">{{ number_format($gross, 0) }}</td>
        </tr>
        <tr>
            <td>Expenses</td>
            <td class="right">{{ number_format($expensesTotal, 0) }}</td>
        </tr>
        <tr class="net">
            <td>Net Profit</td>
            <td class="right">{{ number_format($net, 0) }}</td>
        </tr>
    </table>

    <p class="muted" style="margin-top: 16px;">
        Note: COGS uses product cost price × qty sold.
    </p>
</body>
</html>