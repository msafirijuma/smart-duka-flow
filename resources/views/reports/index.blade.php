@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<h4 class="fw-bold mb-4">Reports</h4>

<div class="row g-3">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.sales') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-receipt display-6 text-primary"></i>
                    <h6 class="fw-bold mt-3 mb-1">Sales Report</h6>
                    <small class="text-muted">Mauzo, collections, credit</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.purchases') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-bag-plus display-6 text-success"></i>
                    <h6 class="fw-bold mt-3 mb-1">Purchases Report</h6>
                    <small class="text-muted">Manunuzi na suppliers</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-wallet2 display-6 text-warning"></i>
                    <h6 class="fw-bold mt-3 mb-1">Expenses Report</h6>
                    <small class="text-muted">Matumizi kwa category</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.profit') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <i class="bi bi-graph-up-arrow display-6 text-danger"></i>
                    <h6 class="fw-bold mt-3 mb-1">Profit Summary</h6>
                    <small class="text-muted">Revenue − COGS − Expenses</small>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection