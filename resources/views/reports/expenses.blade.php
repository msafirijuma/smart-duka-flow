@extends('layouts.app')
@section('title', 'Expenses Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Expenses Report</h4>
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
                <button class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Expenses</div>
                <div class="fs-4 fw-bold text-danger">TZS {{ number_format($totalExpenses, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small mb-2">By Category</div>
                @forelse($byCategory as $row)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $row->category ? ucfirst($row->category) : 'Other' }}</span>
                        <strong>TZS {{ number_format($row->total, 0) }}</strong>
                    </div>
                @empty
                    <span class="text-muted small">No data</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $e)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $e->title }}</td>
                        <td>{{ $e->category ? ucfirst($e->category) : '—' }}</td>
                        <td class="fw-semibold text-danger">TZS {{ number_format($e->amount, 0) }}</td>
                        <td>{{ $e->expense_date->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No expenses in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="card-footer bg-white">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection