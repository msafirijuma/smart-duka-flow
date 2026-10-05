@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="d-flex align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Shop Settings</h4>
        <small class="text-muted">These details appear on receipts and in the app</small>
    </div>
</div>

<a href="{{ route('settings.data-export') }}" class="btn btn-outline-success btn-sm mb-4">
    <i class="bi bi-download"></i> Export my data
</a>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-2">Your plan</h6>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="fs-5 fw-bold">{{ $shop->plan->name ?? 'Free' }}</span>
                <span class="text-muted small ms-1">{{ $shop->plan->price_label ?? '' }}</span>
                @if($shop->subscription_ends_at)
                    <div class="small text-muted">Renews / ends {{ $shop->subscription_ends_at->format('d M Y') }}</div>
                @endif
            </div>
            <span class="badge bg-primary">Current</span>
        </div>
        @if($shop->plan)
            <hr>
            <div class="small text-muted">
                Products:
                {{ $shop->plan->max_products === null ? 'Unlimited' : 'up to '.$shop->plan->max_products }}
                · Staff:
                {{ $shop->plan->max_staff === null ? 'Unlimited' : 'up to '.$shop->plan->max_staff }}
                · Exports Reports: {{ $shop->plan->has_exports ? 'Yes' : 'No' }}
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
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

        <div class="card border-0 shadow-sm mt-3 mb-4">
            <div class="card-body py-3 small text-muted">
                <!-- <strong>Note:</strong> App name on the public website remains <strong>DukaFlow</strong>.
                Your shop name is what customers see on receipts and inside the system. -->
                <strong>Note:</strong> Your shop name is what customers see on receipts and inside the system.
            </div>
        </div>
    </div>
</div>
@endsection