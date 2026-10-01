@extends('layouts.app')
@section('title', $purchase->reference)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">{{ $purchase->reference }}</h4>
        <small class="text-muted">{{ $purchase->purchase_date->format('d M Y') }}</small>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Purchase Info</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Supplier</small>
                    <strong>{{ $purchase->supplier->name ?? '—' }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Recorded by</small>
                    <strong>{{ $purchase->user->name ?? '—' }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Payment</small>
                    <span class="badge bg-primary text-uppercase">{{ $purchase->payment_method }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Summary</h6>
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span>TZS {{ number_format($purchase->subtotal, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Discount</span>
                    <span>TZS {{ number_format($purchase->discount, 0) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-primary">TZS {{ number_format($purchase->total, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Amount Paid</span>
                    <span>TZS {{ number_format($purchase->amount_paid, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-5">
    <div class="card-header bg-white fw-semibold">Items</div>
    <div class="card-body p-3">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Payment</th>
                    <th>Unit Cost</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-uppercase">{{ $purchase->payment_method }}</td>
                        <td>TZS {{ number_format($item->unit_cost, 0) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="fw-semibold">TZS {{ number_format($item->total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection