@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Overview</h4>
        <p class="text-muted small mb-0">
            Welcome back, {{ auth()->user()->name }}. Platform health — not shop sales.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.activity.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-journal-text"></i> Activity
        </a>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-shop"></i> All shops
        </a>
    </div>
</div>

{{-- Platform totals --}}
<p class="text-muted small fw-semibold text-uppercase mb-2">Platform</p>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-shop"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($totalShops) }}</div>
                <div class="text-muted small">Total shops</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-circle"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($activeShops) }}</div>
                <div class="text-muted small">Active shops</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-pause-circle"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($suspendedShops) }}</div>
                <div class="text-muted small">Suspended</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-info-subtle text-info"><i class="bi bi-people"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ number_format($totalUsers) }}</div>
                <div class="text-muted small">Users</div>
            </div>
        </div>
    </div>
</div>

{{-- This month only --}}
<p class="text-muted small fw-semibold text-uppercase mb-2">This month</p>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-plus-square"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ $newShopsMonth }}</div>
                <div class="text-muted small">New shops</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-person-plus"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ $newUsersMonth }}</div>
                <div class="text-muted small">New users</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-arrow-repeat"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ $planChangesMonth }}</div>
                <div class="text-muted small">Plan changes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <span class="stat-icon bg-secondary-subtle text-secondary"><i class="bi bi-credit-card"></i></span>
                <div class="fs-3 fw-bold mt-2">{{ $plansCount }}</div>
                <div class="text-muted small">Active plan types</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts: growth only --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New shops (6 months)</h6>
                <canvas id="shopsChart" height="130"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">New users (6 months)</h6>
                <canvas id="usersChart" height="130"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Lists --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">Shops by plan</div>
            <ul class="list-group list-group-flush">
                @foreach($planBreakdown as $plan)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $plan->name }}
                        <span class="badge text-bg-primary rounded-pill">{{ $plan->shops_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold d-flex justify-content-between">
                <span>Recent shops</span>
                <a href="{{ route('admin.shops.index') }}" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recentShops as $s)
                    <li class="list-group-item">
                        <a href="{{ route('admin.shops.show', $s) }}" class="text-decoration-none fw-semibold">
                            {{ $s->name }}
                        </a>
                        <div class="small text-muted">
                            {{ $s->plan->name ?? 'No plan' }}
                            ·
                            @if($s->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Suspended</span>
                            @endif
                            · {{ $s->created_at->diffForHumans() }}
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No shops</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold d-flex justify-content-between">
                <span>Recent activity</span>
                <a href="{{ route('admin.activity.index') }}" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recentLogs as $log)
                    <li class="list-group-item small">
                        <div class="fw-semibold text-truncate">{{ $log->description }}</div>
                        <div class="text-muted">
                            {{ $log->user->name ?? '—' }} · {{ $log->created_at->diffForHumans() }}
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No activity</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<style>
    .stat-icon {
        display: inline-flex;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const grid = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const tick = isDark ? '#94a3b8' : '#64748b';

    new Chart(document.getElementById('shopsChart'), {
        type: 'bar',
        data: {
            labels: @json($shopsChartLabels),
            datasets: [{
                label: 'New shops',
                data: @json($shopsChartData),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: tick } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { color: tick, stepSize: 1 } }
            }
        }
    });

    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: @json($usersChartLabels),
            datasets: [{
                label: 'New users',
                data: @json($usersChartData),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#10b981',
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: grid }, ticks: { color: tick } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { color: tick, stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush