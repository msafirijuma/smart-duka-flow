@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Add New Product</h5>

                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">— Select —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="pcs" @selected(old('unit') == 'pcs')>pcs</option>
                                <option value="kg" @selected(old('unit') == 'kg')>kg</option>
                                <option value="g" @selected(old('unit') == 'g')>g</option>
                                <option value="ltr" @selected(old('unit') == 'ltr')>ltr</option>
                                <option value="ml" @selected(old('unit') == 'ml')>ml</option>
                                <option value="box" @selected(old('unit') == 'box')>box</option>
                                <option value="dozen" @selected(old('unit') == 'dozen')>dozen</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cost Price (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="cost_price"
                                   class="form-control @error('cost_price') is-invalid @enderror"
                                   value="{{ old('cost_price', 0) }}" required>
                            @error('cost_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Selling Price (TZS) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price"
                                   class="form-control @error('selling_price') is-invalid @enderror"
                                   value="{{ old('selling_price', 0) }}" required>
                            @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity"
                                   class="form-control @error('stock_quantity') is-invalid @enderror"
                                   value="{{ old('stock_quantity', 0) }}" required>
                            @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold"
                                   class="form-control"
                                   value="{{ old('low_stock_threshold', 5) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Product image</label>
                            <input type="file" name="image" id="imageInput"
                                class="form-control @error('image') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG or WebP · Max 2MB</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="" alt="" class="rounded border d-none"
                                    style="max-height: 120px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Save Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection