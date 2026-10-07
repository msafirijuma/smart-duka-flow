@extends('layouts.app')
@section('title', 'Sales Report')

@section('content')
<div class="d-md-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Sales Report</h4>
    <div class="d-md-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.sales.export-pdf', request()->query()) }}"
            class="btn btn-outline-danger btn-sm">
             <i class="bi bi-filetype-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.sales.export', request()->query()) }}"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">← All Reports</a>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
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
                    @foreach(['cash','mpesa','bank','credit'] as $m)
                        <option value="{{ $m }}" @selected(request('payment_method') == $m)>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-primary me-1">Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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

<div class="card border-1 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <!-- Search Bar Header -->
            <div class="card-header bg-transparent border-0 py-3">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="position-relative">
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                id="saleSearchInput" 
                                class="form-control border-secondary ps-5 pe-5" 
                                placeholder="Search sale by title or category..." 
                                autocomplete="off">

                            <!-- Clear (X) Button (Hidden by default) -->
                            <button type="button" 
                                    id="clearSearchBtn" 
                                    class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3 d-none" 
                                    aria-label="Clear search">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- table -->
            <table class="table table-striped table-hover table-sm mb-0">
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
                                <a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->invoice_number }}</a>
                            </td>
                            <td>{{ $sale->user->name ?? '—' }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="fw-semibold">TZS {{ number_format($sale->total, 0) }}</td>
                            <td><span class="badge bg-light text-dark text-uppercase">{{ $sale->payment_method }}</span></td>
                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No sales found.</td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('saleSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterSales() {
            const filter = searchInput.value.toLowerCase().trim();
            const rows = document.querySelectorAll('tbody tr');

            // show/hide (X) icon
            if (filter.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }

            // Filter table rows
            rows.forEach(row => {
                if (row.id === 'noDataRow') return;

                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // keyup handler
        searchInput.addEventListener('keyup', filterSales);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterSales(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush



