@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted small mb-0">
            {{ $shop->name }} · {{ now()->format('l, d M Y') }}
            @if($shop->plan)
                · <span class="badge bg-primary-subtle text-primary">{{ $shop->plan->name }}</span>
            @endif
        </p>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary">
        <i class="bi bi-cart"></i> New Sale
    </a>
</div>

{{-- TODAY --}}
<p class="text-muted small fw-semibold text-uppercase mb-2">Today</p>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 dash-card">
            <div class="card-body">
                <span class="dash-icon bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></span>
                <div class="fs-5 fw-bold text-primary mt-2">TZS {{ number_format($todaySales, 0) }}</div>
                <div class="text-muted small">Today's Sales</div>
                <div class="text-muted" style="font-size:11px">All sales incl. credit</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 dash-card">
            <div class="card-body">
                <span class="dash-icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></span>
                <div class="fs-5 fw-bold text-success mt-2">TZS {{ number_format($todayCollections, 0) }}</div>
                <div class="text-muted small">Today's Collections</div>
                <div class="text-muted" style="font-size:11px">Cash / M-Pesa / Bank</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 dash-card">
            <div class="card-body">
                <span class="dash-icon bg-warning-subtle text-warning"><i class="bi bi-credit-card"></i></span>
                <div class="fs-5 fw-bold text-warning mt-2">TZS {{ number_format($todayCredit, 0) }}</div>
                <div class="text-muted small">Today's Credit</div>
                <div class="text-muted" style="font-size:11px">Sold on credit today</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 dash-card">
            <div class="card-body">
                <span class="dash-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></span>
                <div class="fs-5 fw-bold text-danger mt-2">TZS {{ number_format($outstandingDebts, 0) }}</div>
                <div class="text-muted small">Outstanding Debts</div>
                <div class="text-muted" style="font-size:11px">Customers still owe</div>
            </div>
        </div>
    </div>
</div>

{{-- SNAPSHOT --}}
<p class="text-muted small fw-semibold text-uppercase mb-2">Shop snapshot</p>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="dash-icon bg-secondary-subtle text-secondary"><i class="bi bi-box-seam"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($productsCount) }}</div>
                <div class="text-muted small">Products</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="dash-icon bg-warning-subtle text-warning"><i class="bi bi-battery-half"></i></span>
                <div class="fs-3 fw-bold mt-2 {{ $lowStockCount > 0 ? 'text-warning' : '' }}">{{ $lowStockCount }}</div>
                <div class="text-muted small">Low / out stock</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="dash-icon bg-info-subtle text-info"><i class="bi bi-people"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($customersCount) }}</div>
                <div class="text-muted small">Customers</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="dash-icon bg-success-subtle text-success"><i class="bi bi-graph-up"></i></span>
                <div class="fs-5 fw-bold text-success mt-2">TZS {{ number_format($salesThisMonth, 0) }}</div>
                <div class="text-muted small">Sales this month</div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Low stock --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Sales — last 7 days</h6>
                <canvas id="weekSalesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Low stock</span>
                <a href="{{ route('stock.index') }}" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($lowStockItems as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <span class="small text-truncate me-2">{{ $p->name }}</span>
                        @if($p->stock_quantity <= 0)
                            <span class="badge bg-danger">Out</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $p->stock_quantity }}</span>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item text-muted small">All stock looks OK</li>
                @endforelse
            </ul>
            <div class="card-body border-top py-2">
                <div class="d-grid gap-1">
                    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-primary">POS</a>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Purchases + Expenses --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Purchases — last 7 days</h6>
                <canvas id="weekPurchasesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Expenses — last 7 days</h6>
                <canvas id="weekExpensesChart" height="110"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Profit + Customer trend --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Approx. profit — last 7 days</h6>
                <p class="text-muted small mb-2">Sales − COGS − expenses</p>
                <canvas id="weekProfitChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New customers — last 7 days</h6>
                <canvas id="weekCustomersChart" height="110"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent sales --}}
<div class="card border-0 shadow-sm">
    <div class="card-header text-white fw-semibold" style="background:#2563eb;">
        Recent Sales
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Cashier</th>
                    <th>Customer</th>
                    <th class="text-end">Amount</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSales as $i => $sale)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->user->name ?? '—' }}</td>
                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="text-end">TZS {{ number_format($sale->total, 0) }}</td>
                        <td><span class="badge bg-light text-dark text-uppercase">{{ $sale->payment_method }}</span></td>
                        <td class="small text-muted">{{ $sale->created_at->format('d M H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No sales yet. <a class="text-decoration-none" href="{{ route('sales.create') }}">Make your first sale</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .dash-icon {
        display: inline-flex;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        align-items: center;
        justify-content: center;
    }
    .dash-card .card-body { padding: 1.1rem; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const grid = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const tick = isDark ? '#94a3b8' : '#64748b';
    const labels = @json($chartLabels);

    const moneyTick = {
        color: tick,
        callback: v => v >= 1000000 ? (v/1e6).toFixed(1)+'M' : (v >= 1000 ? (v/1000)+'k' : v)
    };

    function lineChart(el, data, color, label) {
        return new Chart(document.getElementById(el), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data,
                    borderColor: color,
                    backgroundColor: 'rgba(37, 99, 235, 0.12)'
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: color,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: grid }, ticks: { color: tick } },
                    y: { beginAtZero: true, grid: { color: grid }, ticks: moneyTick }
                }
            }
        });
    }

    function barChart(el, data, color, label, isCount) {
        return new Chart(document.getElementById(el), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label,
                    data,
                    backgroundColor: bg || (color === '#10b981' ? 'rgba(16,185,129,0.12)' : 'rgba(37,99,235,0.12)'),
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: tick } },
                    y: {
                        beginAtZero: true,
                        grid: { color: grid },
                        ticks: isCount
                            ? { color: tick, stepSize: 1 }
                            : moneyTick
                    }
                }
            }
        });
    }

    // Sales (existing)
    lineChart('weekSalesChart', @json($chartData), '#2563eb', 'Sales');

    // Purchases + Expenses
    barChart('weekPurchasesChart', @json($purchaseChartData), '#f59e0b', 'Purchases', false);
    barChart('weekExpensesChart', @json($expenseChartData), '#ef4444', 'Expenses', false);

    // Profit + Customers
    lineChart('weekProfitChart', @json($profitChartData), '#10b981', 'Profit');
    barChart('weekCustomersChart', @json($customerChartData), '#8b5cf6', 'New customers', true);
});
</script>
@endpush