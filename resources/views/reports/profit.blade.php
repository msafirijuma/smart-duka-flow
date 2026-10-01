@extends('layouts.app')
@section('title', 'Profit Summary')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Profit Summary</h4>
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">← All Reports</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-4">
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

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Revenue (Sales)</div>
                <div class="fs-4 fw-bold text-primary">TZS {{ number_format($revenue, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">COGS (Cost of goods)</div>
                <div class="fs-4 fw-bold">TZS {{ number_format($cogs, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Expenses</div>
                <div class="fs-4 fw-bold text-danger">TZS {{ number_format($expenses, 0) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Gross Profit</div>
                <div class="fs-3 fw-bold {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    TZS {{ number_format($grossProfit, 0) }}
                </div>
                <small class="text-muted">Revenue − COGS</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Net Profit</div>
                <div class="fs-3 fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    TZS {{ number_format($netProfit, 0) }}
                </div>
                <small class="text-muted">Gross Profit − Expenses</small>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4 mb-0 small">
    <strong>Note:</strong> COGS inakadiriwa kutoka cost price ya product × quantity iliyouzwa.
    Si exact FIFO accounting, lakini inatosha kuona faida ya takribani.
</div>
@endsection