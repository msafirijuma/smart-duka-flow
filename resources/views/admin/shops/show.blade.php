@extends('layouts.admin')
@section('title', $shop->name)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">
            {{ $shop->name }}
            @if($shop->is_active ?? true)
                <span class="badge bg-success fs-6 align-middle">Active</span>
            @else
                <span class="badge bg-danger fs-6 align-middle">Suspended</span>
            @endif
        </h4>
        <small class="text-muted">
            {{ $shop->phone ?? '—' }}
            @if($shop->email) · {{ $shop->email }} @endif
            @if($shop->address) · {{ $shop->address }} @endif
        </small>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.shops.toggle', $shop) }}" method="POST" class="d-inline"
              onsubmit="return confirm('{{ ($shop->is_active ?? true) ? 'Suspend' : 'Activate' }} this shop?')">
            @csrf
            <button class="btn btn-sm {{ ($shop->is_active ?? true) ? 'btn-outline-danger' : 'btn-outline-success' }}">
                {{ ($shop->is_active ?? true) ? 'Suspend' : 'Activate' }}
            </button>
        </form>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Products</div>
                <div class="fs-4 fw-bold">{{ $productsCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Customers</div>
                <div class="fs-4 fw-bold">{{ $customersCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total sales</div>
                <div class="fs-5 fw-bold text-primary">TZS {{ number_format($salesTotal, 0) }}</div>
                <small class="text-muted">{{ $salesCount }} transactions</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">This month</div>
                <div class="fs-5 fw-bold text-success">TZS {{ number_format($salesThisMonth, 0) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Last sale -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="text-muted small mb-1">Last sale</div>
        @if($lastSale)
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>{{ $lastSale->invoice_number }}</strong>
                    <span class="text-muted small ms-2">{{ $lastSale->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="fw-bold text-primary">TZS {{ number_format($lastSale->total, 0) }}</div>
            </div>
        @else
            <span class="text-muted">No sales yet</span>
        @endif
    </div>
</div>

<!-- Subscription -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-success fw-semibold">Subscription</div>
    <div class="card-body">
        <div class="mb-3">
            <span class="text-muted small">Current plan</span>
            <div class="fs-5 fw-bold">
                {{ $shop->plan->name ?? 'None' }}
                @if($shop->plan)
                    <span class="text-muted fw-normal small">({{ $shop->plan->price_label }})</span>
                @endif
            </div>
            @if($shop->subscription_ends_at)
                <small class="text-muted">Ends: {{ $shop->subscription_ends_at->format('d M Y') }}</small>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.shops.update-plan', $shop) }}">
            @csrf
            @method('PUT')
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Plan</label>
                    <select name="plan_id" class="form-select form-select-sm" required>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" @selected($shop->plan_id == $plan->id)>
                                {{ $plan->name }} — {{ $plan->price_label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Ends at (optional)</label>
                    <input type="date" name="subscription_ends_at" class="form-control form-control-sm"
                           value="{{ $shop->subscription_ends_at?->format('Y-m-d') }}">
                </div>
                <div class="col-md-8 mt-2">
                    <label class="form-label small">Note (optional)</label>
                    <input type="text" name="note" class="form-control form-control-sm"
                        placeholder="e.g. Paid via M-Pesa, trial extended...">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-sm btn-primary">Update plan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-primary fw-semibold d-flex justify-content-between align-items-center">
        <span>Subscription history</span>
        <small class="text-muted">Last {{ $shop->subscriptionHistories->count() }} changes</small>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Ends at</th>
                        <th>By</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shop->subscriptionHistories as $h)
                        <tr>
                            <td class="text-nowrap">{{ $h->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $h->previousPlan->name ?? '—' }}</td>
                            <td>
                                <span class="fw-semibold">{{ $h->plan->name ?? '—' }}</span>
                            </td>
                            <td>
                                {{ $h->subscription_ends_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td>{{ $h->changedByUser->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $h->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No plan changes yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Team -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">Team ({{ $shop->users->count() }})</div>
    <div class="card-body p-3">
        <table class="table table-hover table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Default</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shop->users as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge bg-light text-dark text-uppercase">{{ $u->pivot->role }}</span></td>
                        <td>{{ $u->pivot->is_default ? 'Yes' : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No users</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Meta -->
<div class="card border-0 shadow-sm">
    <div class="card-body small text-muted">
        <div>Slug: <code>{{ $shop->slug ?? '—' }}</code></div>
        <div>Created: {{ $shop->created_at->format('d M Y H:i') }}</div>
        <div>Updated: {{ $shop->updated_at->format('d M Y H:i') }}</div>
    </div>
</div>
@endsection