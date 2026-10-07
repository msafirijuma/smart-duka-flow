@extends('layouts.app')
@section('title', 'Stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Stock Overview</h4>
        <small class="text-muted">Monitor inventory levels across your products</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bag-plus"></i> Restock
        </a>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('stock.index', ['filter' => 'all']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 {{ $filter === 'all' ? 'border-primary border-2' : '' }}">
                <div class="card-body">
                    <div class="text-muted small">All products</div>
                    <div class="fs-4 fw-bold">{{ $totalProducts }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('stock.index', ['filter' => 'all']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">In stock (OK)</div>
                    <div class="fs-4 fw-bold text-success">{{ $okCount }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('stock.index', array_merge(request()->only('q', 'category_id'), ['filter' => 'low'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 {{ $filter === 'low' ? 'border-warning border-2' : '' }}">
                <div class="card-body">
                    <div class="text-muted small">Low stock</div>
                    <div class="fs-4 fw-bold text-warning">{{ $lowCount }}</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('stock.index', array_merge(request()->only('q', 'category_id'), ['filter' => 'out'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 {{ $filter === 'out' ? 'border-danger border-2' : '' }}">
                <div class="card-body">
                    <div class="text-muted small">Out of stock</div>
                    <div class="fs-4 fw-bold text-danger">{{ $outCount }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Name, SKU, barcode..." value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Category</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-primary me-1">Filter</button>
                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Threshold</th>
                        <th>Status</th>
                        <th style="width; 100px; min-width: 100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $qty = $product->stock_quantity;
                            $threshold = $product->low_stock_threshold;
                            if ($qty <= 0) {
                                $status = 'out';
                                $badge = 'bg-danger';
                                $label = 'Out of stock';
                                $pct = 0;
                            } elseif ($qty <= $threshold) {
                                $status = 'low';
                                $badge = 'bg-warning text-dark';
                                $label = 'Low stock';
                                $pct = $threshold > 0 ? min(100, ($qty / max($threshold * 2, 1)) * 100) : 50;
                            } else {
                                $status = 'ok';
                                $badge = 'bg-success';
                                $label = 'OK';
                                $pct = min(100, ($qty / max($threshold * 3, $qty)) * 100);
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if($product->sku)
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>
                                <div class="fw-bold {{ $status === 'out' ? 'text-danger' : ($status === 'low' ? 'text-warning' : '') }}">
                                    {{ $qty }} <span class="text-muted fw-normal small">{{ $product->unit }}</span>
                                </div>
                                <div class="progress mt-1" style="height: 4px; max-width: 100px;">
                                    <div class="progress-bar {{ $status === 'out' ? 'bg-danger' : ($status === 'low' ? 'bg-warning' : 'bg-success') }}"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $threshold }}</td>
                            <td>
                                <span class="badge {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                @if($filter === 'low')
                                    No low-stock products. Great!
                                @elseif($filter === 'out')
                                    No out-of-stock products.
                                @else
                                    No products found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white">{{ $products->links() }}</div>
    @endif
</div>
@endsection