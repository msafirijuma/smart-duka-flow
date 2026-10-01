@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Edit Supplier</h5>
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Update Supplier</button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection