@extends('layouts.app')
@section('title', $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">{{ $customer->name }}</h4>
        <small class="text-muted">{{ $customer->phone ?? 'No phone' }}</small>
    </div>
    <div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
</div>

@if($customer->balance > 0)
    <a href="{{ route('customers.payment', $customer) }}" class="btn btn-success btn-md my-2 mb-4 px-3">
        <i class="bi bi-cash"></i> Record Payment
    </a>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Credit Limit</div>
                <div class="fs-4 fw-bold">TZS {{ number_format($customer->credit_limit, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Current Balance (Debt)</div>
                <div class="fs-4 fw-bold {{ $customer->balance > 0 ? 'text-danger' : 'text-success' }}">
                    TZS {{ number_format($customer->balance, 0) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Available Credit</div>
                <div class="fs-4 fw-bold">
                    TZS {{ number_format(max(0, $customer->credit_limit - $customer->balance), 0) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="card border-0 shadow-sm mb-5 py-3">
    <div class="card-header bg-white fw-semibold">Recent Payments</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                            </td>
                            <td>TZS {{ number_format($sale->total, 0) }}</td>
                            <td>{{ strtoupper($sale->payment_method) }}</td>
                            <td>{{ $sale->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No sales yet for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="card border-0 shadow-sm py-3">
    <div class="card-header bg-white fw-semibold">Recent Sales</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                            </td>
                            <td>TZS {{ number_format($sale->total, 0) }}</td>
                            <td>{{ strtoupper($sale->payment_method) }}</td>
                            <td>{{ $sale->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No sales yet for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection