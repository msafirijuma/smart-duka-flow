<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h2 { margin: 0 0 4px; }
        .muted { color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; }
        .right { text-align: right; }
        .total { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>{{ $shop->name ?? 'Shop' }} — Sales Report</h2>
    <div class="muted">Period: {{ $from }} to {{ $to }} · Generated {{ now()->format('d M Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Cashier</th>
                <th>Customer</th>
                <th class="right">Total (TZS)</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $sale->user->name ?? '—' }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td class="right">{{ number_format($sale->total, 0) }}</td>
                    <td>{{ strtoupper($sale->payment_method) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No sales in this period</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="total">Grand total: TZS {{ number_format($total, 0) }} · {{ $sales->count() }} sale(s)</p>
</body>
</html>