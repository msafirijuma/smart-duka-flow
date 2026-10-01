@extends('layouts.app')
@section('title', 'Sales Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Sales Report</h4>
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
                <label class="form-label small mb-1">Payment</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['cash','mpesa','bank','credit'] as $m)
                        <option value="{{ $m }}" @selected(request('payment_method') == $m)>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Sales</div>
                <div class="fs-5 fw-bold text-primary">TZS {{ number_format($totalSales, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Collections</div>
                <div class="fs-5 fw-bold text-success">TZS {{ number_format($collections, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Credit Sales</div>
                <div class="fs-5 fw-bold text-warning">TZS {{ number_format($creditSales, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Transactions</div>
                <div class="fs-5 fw-bold">{{ $salesCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                            </td>
                            <td>{{ $sale->user->name ?? '—' }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="fw-semibold">TZS {{ number_format($sale->total, 0) }}</td>
                            <td><span class="badge bg-light text-dark text-uppercase">{{ $sale->payment_method }}</span></td>
                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No sales in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sales->hasPages())
        <div class="card-footer bg-white">{{ $sales->links() }}</div>
    @endif
</div>
@endsection