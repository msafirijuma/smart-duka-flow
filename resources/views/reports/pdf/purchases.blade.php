<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchases Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h2 { margin: 0 0 4px; font-size: 16px; }
        .muted { color: #555; font-size: 10px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; }
        th { background: #f1f5f9; font-size: 10px; }
        .right { text-align: right; }
        .total { font-weight: bold; margin-top: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <h2>{{ $shop->name }} — Purchases Report</h2>
    <div class="muted">Period: {{ $from }} to {{ $to }} · Generated {{ now()->format('d M Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Date</th>
                <th>Supplier</th>
                <th class="right">Total (TZS)</th>
                <th>Payment</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td>{{ $p->reference }}</td>
                    <td>{{ $p->purchase_date?->format('d M Y') }}</td>
                    <td>{{ $p->supplier->name ?? '—' }}</td>
                    <td class="right">{{ number_format($p->total, 0) }}</td>
                    <td>{{ strtoupper($p->payment_method) }}</td>
                    <td>{{ $p->user->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No purchases in this period</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="total">Grand total: TZS {{ number_format($total, 0) }} · {{ $purchases->count() }} purchase(s)</p>
</body>
</html>