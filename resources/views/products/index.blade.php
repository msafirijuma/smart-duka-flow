@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Products <span class="d-none d-md-inline">Management</span></h4>
    @if(auth()->user()->isManager())
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    @endif
</div>

@php
    $shop = \App\Models\Shop::with('plan')->find(session('current_shop_id'));
    $plan = $shop?->plan;
    $productCount = \App\Models\Product::where('shop_id', session('current_shop_id'))->count();
@endphp

@if($plan && $plan->max_products !== null)
    <p class="d-block text-muted mt-2 mb-1">
        Products: {{ $productCount }} / {{ $plan->max_products }} <span class="badge bg-success ms-1">{{ $plan->name }} Plan</span>
    </p>
    <small class="d-block text-muted mb-3">Upgrade your plan to add more products</small>
@endif

<div class="card border-1 shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th style="width: 140px; min-width: 140px">Name</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Selling</th>
                        <th style="width: 100px; min-width: 100px">Stock</th>
                        <th>Status</th>
                        <th>Thumbnail</th>
                        <th style="width: 140px; min-width: 140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if($product->sku)
                                    <small class="text-muted">SKU: {{ $product->sku }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>TZS {{ number_format($product->cost_price, 0) }}</td>
                            <td class="fw-semibold">TZS {{ number_format($product->selling_price, 0) }}</td>
                            <td>
                                <span class="{{ $product->isLowStock() ? 'text-warning fw-bold' : '' }}">
                                    {{ $product->stock_quantity }} {{ $product->unit }}
                                </span>
                                @if($product->isLowStock())
                                    <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Low stock"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt=""
                                            class="rounded"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-box"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        @if($product->sku)
                                            <small class="text-muted">{{ $product->sku }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @if(auth()->user()->isManager())
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(auth()->user()->isOwner())
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No products yet.
                                <a class="text-decoration-none" href="{{ route('products.create') }}">Add your first product</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
        <div class="card-footer bg-white">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection