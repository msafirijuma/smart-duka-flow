@extends('layouts.app')
@section('title', 'Edit Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Edit Expense</h5>
                <form method="POST" action="{{ route('expenses.update', $expense) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $expense->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (TZS) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                               value="{{ old('amount', $expense->amount) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">— Select —</option>
                            @foreach(['rent','transport','utilities','salary','supplies','other'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $expense->category) == $cat)>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control"
                               value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            @foreach(['cash','mpesa','bank'] as $method)
                                <option value="{{ $method }}" @selected(old('payment_method', $expense->payment_method) == $method)>
                                    {{ ucfirst($method) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $expense->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">Update Expense</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection