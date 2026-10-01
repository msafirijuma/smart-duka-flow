@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <small class="text-muted">{{ now()->format('l, d M Y') }}</small>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-cart-plus"></i> New Sale
    </a>
</div>

<!-- Stats Cards -->
<!-- 4 Main Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Today's Sales</div>
                <div class="fs-5 fw-bold text-primary">
                    TZS {{ number_format($todaySales, 0) }}
                </div>
                <small class="text-muted">All sales incl. credit</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Today's Collections</div>
                <div class="fs-5 fw-bold text-success">
                    TZS {{ number_format($todayCollections, 0) }}
                </div>
                <small class="text-muted">Cash / M-Pesa / Bank</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Today's Credit</div>
                <div class="fs-5 fw-bold text-warning">
                    TZS {{ number_format($todayCredit, 0) }}
                </div>
                <small class="text-muted">Sold on credit today</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Outstanding Debts</div>
                <div class="fs-5 fw-bold {{ $outstandingDebts > 0 ? 'text-danger' : 'text-success' }}">
                    TZS {{ number_format($outstandingDebts, 0) }}
                </div>
                <small class="text-muted">Customers still owe</small>
            </div>
        </div>
    </div>
</div>

<!-- Secondary stats (optional) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">Products</div>
                <div class="fs-5 fw-bold">{{ $totalProducts }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="text-muted small">Low Stock</div>
                <div class="fs-5 fw-bold {{ $lowStock > 0 ? 'text-warning' : '' }}">{{ $lowStock }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
        Recent Sales
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->user->name ?? '—' }}</td>
                            <td class="fw-semibold">TZS {{ number_format($sale->total, 0) }}</td>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td>{{ $sale->created_at->format('d M, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No sales yet. <a class="text-decoration-none" href="{{ route('pos.index') }}">Make your first sale</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection