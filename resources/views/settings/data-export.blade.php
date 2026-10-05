@extends('layouts.app')
@section('title', 'Export my data')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h4 class="fw-bold mb-1">Export my data</h4>
        <p class="text-muted small mb-4">
            Download a copy of <strong>{{ $shop->name }}</strong> records (products, sales, customers, etc.).
            This matches your right to obtain a copy of your business data.
        </p>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold">What is included</h6>
                <ul class="small text-muted mb-0">
                    <li>Shop profile</li>
                    <li>Products, customers, suppliers</li>
                    <li>Sales &amp; sale lines</li>
                    <li>Purchases &amp; expenses</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('settings.data-export.download') }}"
                      onsubmit="if(typeof showPageLoader==='function') showPageLoader('Preparing your export…','This may take a moment');">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download ZIP
                    </button>
                    <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary">Back to Settings</a>
                </form>
                <p class="small text-muted mt-3 mb-0">
                    File format: ZIP with CSV files (open in Excel) + shop.json.
                    Keep it private — it contains business records.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection