@extends('layouts.admin')
@section('title', $plan->exists ? 'Edit plan' : 'Add plan')

@section('content')
<h4 class="fw-bold mb-4">{{ $plan->exists ? 'Edit plan' : 'Add plan' }}</h4>

<form method="POST"
      action="{{ $plan->exists ? route('admin.plans.update', $plan) : route('admin.plans.store') }}"
      class="card border-0 shadow-sm">
    @csrf
    @if($plan->exists) @method('PUT') @endif

    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Price label</label>
            <input type="text" name="price_label" class="form-control" placeholder="TZS 25,000/mo"
                   value="{{ old('price_label', $plan->price_label) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Price (number)</label>
            <input type="number" step="0.01" name="price" class="form-control"
                   value="{{ old('price', $plan->price ?? 0) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Max products (empty = unlimited)</label>
            <input type="number" name="max_products" class="form-control"
                   value="{{ old('max_products', $plan->max_products) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Max staff</label>
            <input type="number" name="max_staff" class="form-control"
                   value="{{ old('max_staff', $plan->max_staff) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Max shops</label>
            <input type="number" name="max_shops" class="form-control"
                   value="{{ old('max_shops', $plan->max_shops) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sort order</label>
            <input type="number" name="sort_order" class="form-control"
                   value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
        </div>
        <div class="col-md-9 d-flex align-items-end gap-4">
            <div class="form-check">
                <input type="checkbox" name="has_reports" value="1" class="form-check-input" id="hr"
                       @checked(old('has_reports', $plan->has_reports ?? true))>
                <label class="form-check-label" for="hr">Reports</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="has_exports" value="1" class="form-check-input" id="he"
                       @checked(old('has_exports', $plan->has_exports ?? false))>
                <label class="form-check-label" for="he">Exports</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="ia"
                       @checked(old('is_active', $plan->is_active ?? true))>
                <label class="form-check-label" for="ia">Active</label>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white">
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection