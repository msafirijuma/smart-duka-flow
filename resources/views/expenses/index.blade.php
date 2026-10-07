@extends('layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Expenses</h4>
        <small class="text-muted">
            This month: <strong>TZS {{ number_format($monthExpenses, 0) }}</strong> |
            All time: <strong>TZS {{ number_format($totalExpenses, 0) }}</strong>
        </small>
    </div>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Expense
    </a>
</div>

<!-- Filters -->
<div class="card border-1 shadow-sm mb-3">
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
                <label class="form-label small mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['rent','transport','utilities','salary','supplies','other'] as $cat)
                        <option value="{{ $cat }}" @selected(request('category') == $cat)>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-primary me-1">Filter</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
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
                                id="expenseSearchInput" 
                                class="form-control border-secondary ps-5 pe-5" 
                                placeholder="Search expense by title, category, date..." 
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
            <table class="table table-striped table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>By</th>
                        <th style="width: 130px; min-width: 130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $expense->title }}</td>
                            <td>{{ $expense->category ? ucfirst($expense->category) : '—' }}</td>
                            <td class="fw-bold text-danger">TZS {{ number_format($expense->amount, 0) }}</td>
                            <td><span class="badge bg-light text-dark text-uppercase">{{ $expense->payment_method }}</span></td>
                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            <td>{{ $expense->user->name ?? '—' }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No expenses recorded yet. <a class="text-decoration-none" href="{{ route('expenses.create') }}">Add your first expense</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
        <div class="card-footer bg-white">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('expenseSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        // filter rows method
        function filterExpenses() {
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
        searchInput.addEventListener('keyup', filterExpenses);

        // clear search when X btn is clicked
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterExpenses(); // Re-filter
                searchInput.focus(); // return cursor to input
            });
        }
    }
});
</script>
@endpush