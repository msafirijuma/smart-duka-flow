@extends('layouts.app')
@section('title', 'Sale Details')

@section('content')
<div class="d-md-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Invoice: {{ $sale->invoice_number }}</h4>
        <small class="text-muted">{{ $sale->created_at->format('d M Y, H:i') }}</small>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-2 mt-2 mt-md-0">
        <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer me-2"></i>Print Receipt
        </a>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to Sales
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Sale Info -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Sale Information</h6>

                <div class="mb-2">
                    <small class="text-muted d-block">Cashier</small>
                    <strong>{{ $sale->user->name ?? '—' }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Customer</small>
                    <strong>{{ $sale->customer->name ?? 'Walk-in Customer' }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="badge bg-primary text-uppercase">{{ $sale->payment_method }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-success">{{ ucfirst($sale->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Totals -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Payment Summary</h6>

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>TZS {{ number_format($sale->subtotal, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>
                    <span>TZS {{ number_format($sale->discount, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <span>TZS {{ number_format($sale->tax, 0) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary">TZS {{ number_format($sale->total, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Amount Paid</span>
                    <span>TZS {{ number_format($sale->amount_paid, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Change</span>
                    <span>TZS {{ number_format($sale->change_amount, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items -->
<div class="card border-0 shadow-sm mt-3">
    <div class="card-header fw-semibold">
        Items Sold
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <td>#</td>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>TZS {{ number_format($item->unit_price, 0) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-semibold">TZS {{ number_format($item->total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection