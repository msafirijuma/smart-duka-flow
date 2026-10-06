@extends('layouts.app')
@section('title', 'Sales History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Sales History</h4>
        <small class="text-muted">
            Today: <strong>TZS {{ number_format($todaySales, 0) }}</strong> |
            All time: <strong>TZS {{ number_format($totalSales, 0) }}</strong>
        </small>
    </div>
    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-cart-plus"></i> New Sale
    </a>
</div>

<!-- Search -->
<form method="GET" action="{{ route('sales.index') }}" id="salesFilterForm" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-11">
                <label class="form-label small mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="q"
                           id="salesSearch"
                           value="{{ request('q', $q ?? '') }}"
                           class="form-control"
                           placeholder="Invoice, product, customer, amount, payment…"
                           autocomplete="off"
                           autofocus>
                    @if(request('q'))
                        <button type="button" class="btn btn-outline-secondary" id="clearSearch" title="Clear">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" id="salesFrom" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" id="salesTo" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Payment</label>
                <select name="payment_method" id="salesPayment" class="form-select">
                    <option value="">All</option>
                    @foreach(['cash','mpesa','bank','credit','mixed'] as $m)
                        <option value="{{ $m }}" @selected(request('payment_method') === $m)>
                            {{ strtoupper($m) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100" title="Search">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </div>

        @if(request()->hasAny(['q','from','to','payment_method']))
            <div class="mt-2">
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Clear all</a>
            </div>
        @endif
    </div>
</form>

<!-- Filters -->
<!-- <div class="card border-0 shadow-sm mb-3">
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
                <label class="form-label small mb-1">Payment</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="cash" @selected(request('payment_method') == 'cash')>Cash</option>
                    <option value="mpesa" @selected(request('payment_method') == 'mpesa')>Mobile DPO</option>
                    <option value="bank" @selected(request('payment_method') == 'bank')>Bank</option>
                    <option value="credit" @selected(request('payment_method') == 'credit')>Credit</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-primary me-1">Filter</button>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div> -->

<!-- Sales Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th style="width: 120px; min-width: 120px">Date</th>
                        <th style="width: 90px; min-width: 90px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->user->name ?? '—' }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="fw-bold">TZS {{ number_format($sale->total, 0) }}</td>
                            <td>
                                <span class="badge bg-light text-dark text-uppercase">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td>{{ $sale->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No sales found. 
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($sales->hasPages())
        <div class="card-footer bg-white">
            {{ $sales->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const form   = document.getElementById('salesFilterForm');
    const search = document.getElementById('salesSearch');
    const from   = document.getElementById('salesFrom');
    const to     = document.getElementById('salesTo');
    const pay    = document.getElementById('salesPayment');
    const clear  = document.getElementById('clearSearch');

    if (!form || !search) return;

    let timer = null;
    const DELAY = 1000; // ms — change if you want faster/slower

    function submitForm() {
        form.submit();
    }

    // Type → wait → submit
    search.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(submitForm, DELAY);
    });

    // Enter → submit immediately (no wait)
    search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(timer);
            submitForm();
        }
    });

    // Date / payment change → submit now
    [from, to, pay].forEach(function (el) {
        if (!el) return;
        el.addEventListener('change', function () {
            clearTimeout(timer);
            submitForm();
        });
    });

    // X button → clear only q and submit
    if (clear) {
        clear.addEventListener('click', function () {
            search.value = '';
            clearTimeout(timer);
            submitForm();
        });
    }
})();
</script>
@endpush

