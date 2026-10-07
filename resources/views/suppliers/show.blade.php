@extends('layouts.app')
@section('title', $supplier->name)

@section('content')
<div class="d-md-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            {{ $supplier->name }}
            @if($supplier->balance > 0)
                <span class="badge bg-warning text-dark ms-1">Owed</span>
            @else
                <span class="badge bg-success ms-1">Clear</span>
            @endif
        </h4>
        <small class="text-muted">{{ $supplier->phone ?? 'No phone' }}</small>
    </div>
    <div class="d-flex justify-content-between align-items-center gap-3 mt-2 mt-md-0">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

@if($supplier->balance > 0)
    <a href="{{ route('suppliers.payment', $supplier) }}" class="btn btn-success btn-sm">
        <i class="bi bi-cash"></i> Pay Supplier
    </a>
@endif

<div class="row g-3 mb-4 mt-2">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Balance (Debt)</div>
                <div class="fs-4 fw-bold {{ $supplier->balance > 0 ? 'text-danger' : 'text-success' }}">
                    TZS {{ number_format($supplier->balance, 0) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Purchases</div>
                <div class="fs-4 fw-bold text-primary">
                    TZS {{ number_format($totalPurchases, 0) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Purchased on Credit</div>
                <div class="fs-4 fw-bold text-warning">
                    TZS {{ number_format($totalCredit, 0) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact -->
<div class="card border-0 shadow-sm mb-5">
    <div class="card-body py-3">
        <div class="row">
            <div class="col-md-4">
                <small class="text-muted d-block">Email</small>
                <strong>{{ $supplier->email ?? '—' }}</strong>
            </div>
            <div class="col-md-4 mt-2 mt-md-0">
                <small class="text-muted d-block">Phone</small>
                <strong>{{ $supplier->phone ?? '—' }}</strong>
            </div>
            <div class="col-md-4 mt-2 mt-md-0">
                <small class="text-muted d-block">Address</small>
                <strong>{{ $supplier->address ?? '—' }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History -->
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-primary fw-semibold d-flex justify-content-between align-items-center">
        <span>Purchase History</span>
        <small class="text-muted">Last {{ $supplier->purchases->count() }} purchases</small>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Amount Paid</th>
                        <th>Recorded by</th>
                        <th style="width: 140px; min-width: 140px">Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $purchase->reference }}</td>
                            <td>TZS {{ number_format($purchase->total, 0) }}</td>
                            <td>
                                @php
                                    $badge = match($purchase->payment_method) {
                                        'cash'   => 'bg-success',
                                        'mpesa'  => 'bg-info',
                                        'bank'   => 'bg-primary',
                                        'credit' => 'bg-warning text-dark',
                                        default  => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} text-uppercase">
                                    {{ $purchase->payment_method }}
                                </span>
                            </td>
                            <td>TZS {{ number_format($purchase->amount_paid, 0) }}</td>
                            <td>{{ $purchase->user->name ?? '—' }}</td>
                            <td>{{ $purchase->purchase_date->format('d M Y H:m') }}</td>
                            <td>
                                <!-- After Purchase ---- show route -->
                                @if(Route::has('purchases.show'))
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No purchases from this supplier yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment history -->
<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-success fw-semibold">Payment History</div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Paid By</th>
                        <th>Paid To</th>
                        <th style="width: 140px; min-width: 140px">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplier->payments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-success">
                                TZS {{ number_format($payment->amount, 0) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td>{{ $payment->user->name ?? '—' }}</td>
                            <td>{{ $supplier->name ?? '—' }}</td>
                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No payments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection