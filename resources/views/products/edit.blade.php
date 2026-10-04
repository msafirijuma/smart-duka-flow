@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Edit Product</h5>

                <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">— Select —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(old('category_id', $product->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                @foreach(['pcs','kg','g','ltr','ml','box','dozen'] as $unit)
                                    <option value="{{ $unit }}" @selected(old('unit', $product->unit) == $unit)>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cost Price (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="cost_price" class="form-control"
                                   value="{{ old('cost_price', $product->cost_price) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Selling Price (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control"
                                   value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control"
                                   value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold" class="form-control"
                                   value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required>
                        </div>

                        <div class="col-8">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Product image</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="rounded border"
                                        style="max-height: 100px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="image" id="imageInput" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Leave empty to keep current image</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="" alt="" class="rounded border d-none" style="max-height: 120px;">
                            </div>
                        </div>

                        
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection