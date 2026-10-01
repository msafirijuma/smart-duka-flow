<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $sale->invoice_number }}</title>
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0; padding: 0; 
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #111;
            background: #f1f5f9;
            padding: 20px;
        }
        .receipt {
            max-width: 350px;
            margin: 35px auto;
            background: #fff;
            padding: 20px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }
        .center { 
            text-align: center; 
        }
        .bold { 
            font-weight: 700; 
        }
        .shop-name { 
            font-size: 16px; 
            font-weight: 700; 
            margin-bottom: 4px; 
        }
        .location {
            margin-bottom: 6px;
        }
        .muted { 
            color: #555; font-size: 11px; 
        }
        .divider {
            border: none;
            border-top: 1px dashed #999;
            margin: 10px 0;
        }
        table { 
            width: 100%; border-collapse: collapse; 
        }
        td { 
            padding: 3px 0; vertical-align: top; 
        }
        .right { 
            text-align: right; 
        }
        .item-name { 
            max-width: 140px; 
        }
        .totals td { 
            padding: 2px 0; 
        }
        .grand { 
            font-size: 15px; font-weight: 700; 
        }
        .actions {
            max-width: 320px;
            margin: 16px auto 0;
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .actions button, .actions a {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: pointer;
            text-decoration: none;
            color: #111;
            font-size: 13px;
        }
        .actions .btn-print {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
        .powered-by {
            display: block;
            margin-top: 34px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; max-width: 100%; }
            .actions { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <div class="shop-name">{{ $sale->shop->name ?? 'DukaFlow' }}</div>
            @if($sale->shop->address ?? null)
                <div class="muted location">Location: {{ $sale->shop->address }}</div>
            @endif
            @if($sale->shop->phone ?? null)
                <div class="muted">Tel: {{ $sale->shop->phone }}</div>
            @endif
        </div>

        <hr class="divider">

        <table>
            <tr>
                <td>Invoice</td>
                <td class="right bold">{{ $sale->invoice_number }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td class="right">{{ $sale->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td>Cashier</td>
                <td class="right">{{ $sale->user->name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td class="right">{{ $sale->customer->name ?? '—' }}</td>
            </tr>
        </table>

        <hr class="divider">

        <table>
            @foreach($sale->items as $item)
                <tr>
                    <td colspan="2"><b>Item(s)</b></td>
                </tr>
                <tr>
                    <td class="item-name" colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td class="muted">
                        {{ $item->quantity }} × {{ number_format($item->unit_price, 0) }}
                    </td>
                    <td class="right">{{ number_format($item->total, 0) }}</td>
                </tr>
            @endforeach
        </table>

        <hr class="divider">

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td class="right">{{ number_format($sale->subtotal, 0) }}</td>
            </tr>
            @if($sale->discount > 0)
                <tr>
                    <td>Discount</td>
                    <td class="right">-{{ number_format($sale->discount, 0) }}</td>
                </tr>
            @endif
            <tr class="grand">
                <td>TOTAL</td>
                <td class="right">TZS {{ number_format($sale->total, 0) }}</td>
            </tr>
            <tr>
                <td>Paid ({{ strtoupper($sale->payment_method) }})</td>
                <td class="right">{{ number_format($sale->amount_paid, 0) }}</td>
            </tr>
            @if($sale->change_amount > 0)
                <tr>
                    <td>Change</td>
                    <td class="right">{{ number_format($sale->change_amount, 0) }}</td>
                </tr>
            @endif
        </table>

        <hr class="divider">

        <div class="center muted">
            Thank you for buying!
            <span class="powered-by">Powered by {{ config('app.name', 'DukaFlow') }}</span>
        </div>
    </div>

    <div class="actions">
        <button type="button" class="btn-print" onclick="window.print()">Print Receipt</button>
        <a href="{{ route('sales.show', $sale) }}">Back</a>
        <a href="{{ route('pos.index') }}">New Sale</a>
    </div>

    <script>
        // Optional: auto-open print dialog
        // window.onload = () => window.print();
    </script>
</body>
</html>