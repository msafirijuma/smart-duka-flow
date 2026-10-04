@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
        <small class="text-muted">Product details</small>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->isManager())
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        @endif
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Image --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 text-center">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="img-fluid rounded"
                         style="max-height: 280px; object-fit: contain;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                         style="height: 220px;">
                        <div>
                            <i class="bi bi-image fs-1 d-block mb-2"></i>
                            <span class="small">No image</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Category</small>
                        <strong>{{ $product->category->name ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">SKU</small>
                        <strong>{{ $product->sku ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Barcode</small>
                        <strong>{{ $product->barcode ?? '—' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Unit</small>
                        <strong>{{ $product->unit ?? 'pcs' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Cost price</small>
                        <strong>TZS {{ number_format($product->cost_price, 0) }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Selling price</small>
                        <strong class="text-primary">TZS {{ number_format($product->selling_price, 0) }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Stock</small>
                        @php
                            $qty = (int) $product->stock_quantity;
                            $thr = (int) ($product->low_stock_threshold ?? 0);
                        @endphp
                        <strong class="{{ $qty <= 0 ? 'text-danger' : ($thr > 0 && $qty <= $thr ? 'text-warning' : '') }}">
                            {{ $qty }} {{ $product->unit ?? 'pcs' }}
                        </strong>
                        @if($qty <= 0)
                            <span class="badge bg-danger ms-1">Out</span>
                        @elseif($thr > 0 && $qty <= $thr)
                            <span class="badge bg-warning text-dark ms-1">Low</span>
                        @else
                            <span class="badge bg-success ms-1">OK</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Low stock threshold</small>
                        <strong>{{ $product->low_stock_threshold ?? 0 }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status</small>
                        @if($product->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                @if($product->description ?? null)
                    <hr>
                    <small class="text-muted d-block mb-1">Description</small>
                    <p class="mb-0">{{ $product->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection