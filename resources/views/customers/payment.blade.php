@extends('layouts.app')
@section('title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Record Payment</h5>
                <p class="text-muted small mb-4">
                    Customer: <strong>{{ $customer->name }}</strong><br>
                    Outstanding Balance: <strong class="text-danger">TZS {{ number_format($customer->balance, 0) }}</strong>
                </p>

                <form method="POST" action="{{ route('customers.payment.store', $customer) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}" min="0.01" max="{{ $customer->balance }}" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success">Record Payment</button>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection