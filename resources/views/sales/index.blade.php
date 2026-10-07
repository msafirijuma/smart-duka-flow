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

<!-- Filters -->
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
</div>

<!-- Sales Table -->
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
                            id="saleSearchInput" 
                            class="form-control border-secondary ps-5 pe-5" 
                            placeholder="Search sale by name or phone..." 
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
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm mb-0 align-middle">
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