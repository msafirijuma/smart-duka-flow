@extends('layouts.app')
@section('title', 'Purchases Report')

@section('content')
<div class="d-md-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Purchases Report</h4>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('reports.purchases.export-pdf', request()->query()) }}" 
            class="btn btn-outline-danger btn-sm">
            <i class="bi bi-filetype-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.purchases.export', request()->query()) }}"
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
                <button class="btn btn-sm btn-outline-primary me-1">Filter</button>
                <a href="{{ route('reports.purchases') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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

<div class="card border-1 shadow-sm">
    <div class="card-body p-3">
        <!-- Search Bar Header -->
        <div class="card-header bg-transparent border-0 py-3">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="position-relative">
                        <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                            id="purchaseSearchInput" 
                            class="form-control border-secondary ps-5 pe-5" 
                            placeholder="Search purchase by reference, supplier or payment..." 
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
                        <td><a href="{{ route('purchases.show', $p) }}" class="text-decoration-none">{{ $p->reference }}</a></td>
                        <td>{{ $p->supplier->name ?? '—' }}</td>
                        <td class="fw-semibold">TZS {{ number_format($p->total, 0) }}</td>
                        <td><span class="badge bg-light text-dark text-uppercase">{{ $p->payment_method }}</span></td>
                        <td>{{ $p->purchase_date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
        <div class="card-footer bg-white">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('purchaseSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterPurchases() {
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
        searchInput.addEventListener('keyup', filterPurchases);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterPurchases(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush

