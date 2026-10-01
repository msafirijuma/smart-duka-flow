@extends('layouts.app')
@section('title', 'Purchases Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Purchases Report</h4>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">← All Reports</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="text-muted small">Total Purchases</div>
        <div class="fs-4 fw-bold text-primary">TZS {{ number_format($totalPurchases, 0) }}</div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Reference</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="{{ route('purchases.show', $p) }}">{{ $p->reference }}</a></td>
                        <td>{{ $p->supplier->name ?? '—' }}</td>
                        <td class="fw-semibold">TZS {{ number_format($p->total, 0) }}</td>
                        <td><span class="badge bg-light text-dark text-uppercase">{{ $p->payment_method }}</span></td>
                        <td>{{ $p->purchase_date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No purchases in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
        <div class="card-footer bg-white">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection