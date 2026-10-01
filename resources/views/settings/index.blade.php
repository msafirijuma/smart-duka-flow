@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Shop Settings</h4>
        <small class="text-muted">These details appear on receipts and in the app</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold mb-3">Shop details</h6>

                    <div class="mb-3">
                        <label class="form-label">Shop name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $shop->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $shop->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $shop->email) }}">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $shop->address) }}</textarea>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3">Receipt</h6>

                    <div class="mb-3">
                        <label class="form-label">Receipt footer message</label>
                        <input type="text" name="receipt_footer" class="form-control"
                               value="{{ old('receipt_footer', $shop->receipt_footer) }}"
                               placeholder="e.g. Thank you for buying from us.">
                        <small class="text-muted">Shown at the bottom of printed receipts</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Currency</label>
                        <input type="text" class="form-control" value="TZS" disabled>
                        <small class="text-muted">Fixed for now (Tanzania)</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Save settings
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body py-3 small text-muted">
                <strong>Note:</strong> App name on the public website remains <strong>DukaFlow</strong>.
                Your shop name is what customers see on receipts and inside the system.
            </div>
        </div>
    </div>
</div>
@endsection