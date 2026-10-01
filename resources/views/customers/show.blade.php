@extends('layouts.app')
@section('title', $customer->name)

@section('content')

@php
    $available = max(0, $customer->credit_limit - $customer->balance);
    $usagePercent = $customer->credit_limit > 0
        ? ($customer->balance / $customer->credit_limit) * 100
        : 0;
@endphp

<div class="d-md-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            {{ $customer->name }}
            @if($customer->balance <= 0)
                <span class="badge bg-success ms-1">Clear</span>
            @elseif($usagePercent >= 80)
                <span class="badge bg-warning text-dark ms-1">Near Limit</span>
            @else
                <span class="badge bg-danger ms-1">Has Debt</span>
            @endif
        </h4>
        <small class="text-muted">{{ $customer->phone ?? 'No phone' }}</small>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-3 mt-2 mt-md-0">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-edit me-2"></i> Edit Info
        </a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
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
    <div class="card-header bg-white fw-semibold" style="background-color: mediumslateblue !important">
        <span>Payment History</span>
        @if($customer->balance > 0)
            <a href="{{ route('customers.payment', $customer) }}" class="btn btn-sm btn-success">
                + Payment
            </a>
        @endif
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Recorded by</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->payments as $payment)
                        <tr>
                            <td class="fw-semibold text-success">
                                        TZS {{ number_format($payment->amount, 0) }}
                                    </td>
                                    <td>
                                        @php
                                            $methodBadge = match($payment->payment_method) {
                                                'cash'  => 'bg-success',
                                                'mpesa' => 'bg-info',
                                                'bank'  => 'bg-primary',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $methodBadge }} text-uppercase">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Completed</span>
                                    </td>
                                    <td>{{ $payment->user->name ?? '—' }}</td>
                                    <td>{{ $payment->created_at->format('d M Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No payments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="card border-0 shadow-sm py-3">
    <div class="card-header bg-white fw-semibold" style="background-color: darkcyan !important">Recent Sales</div>
    <div class="card-body p-3">
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
                                <a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>TZS {{ number_format($sale->total, 0) }}</td>
                            <td>
                                @php
                                    $payBadge = match($sale->payment_method) {
                                        'cash'   => 'bg-success',
                                        'mpesa'  => 'bg-info',
                                        'bank'   => 'bg-primary',
                                        'credit' => 'bg-warning text-dark',
                                        default  => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $payBadge }} text-uppercase">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td>{{ $sale->created_at->format('d M Y H:i:s') }}</td>
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