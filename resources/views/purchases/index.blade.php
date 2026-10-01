@extends('layouts.app')
@section('title', 'Purchases')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Purchases</h4>
        <small class="text-muted">This month: <strong>TZS {{ number_format($monthTotal, 0) }}</strong></small>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> New Purchase
    </a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-primary me-1">Filter</button>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $purchase->reference }}</td>
                            <td>{{ $purchase->supplier->name ?? '—' }}</td>
                            <td>TZS {{ number_format($purchase->total, 0) }}</td>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase">
                                    {{ $purchase->payment_method }}
                                </span>
                            </td>
                            <td>{{ $purchase->purchase_date->format('d M Y H:i:s') }}</td>
                            <td>{{ $purchase->user->name ?? '—' }}</td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No purchases yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchases->hasPages())
        <div class="card-footer bg-white">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection