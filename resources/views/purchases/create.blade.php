@extends('layouts.app')
@section('title', 'New Purchase')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">New Purchase</h4>
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>

<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
    @csrf

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header fw-semibold">Items</div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <div class="row g-2 item-row mb-2">
                            <div class="col-md-5">
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="">— Select product —</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-cost="{{ $product->cost_price }}"
                                                data-unit="{{ $product->unit }}">
                                            {{ $product->name }} (Stock: {{ $product->stock_quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="items[0][unit_cost]"
                                       class="form-control unit-cost" placeholder="Unit cost" required min="0">
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]"
                                       class="form-control qty" placeholder="Qty" value="1" required min="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100 remove-row" disabled>×</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addRow">
                        + Add item
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">— Optional —</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Required if paying by credit</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control"
                               value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Discount (TZS)</label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', 0) }}" min="0">
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span id="subtotalDisplay">TZS 0</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total to pay</span>
                            <span id="totalDisplay" class="text-primary">TZS 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount Paid (TZS) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control"
                               value="{{ old('amount_paid', 0) }}" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check2-circle"></i> Save Purchase
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = 1;
const productsOptions = `@foreach($products as $product)<option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}">{{ $product->name }} (Stock: {{ $product->stock_quantity }})</option>@endforeach`;

function formatMoney(n) {
    return 'TZS ' + Number(n).toLocaleString();
}

function calcTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const cost = parseFloat(row.querySelector('.unit-cost')?.value) || 0;
        const qty  = parseInt(row.querySelector('.qty')?.value) || 0;
        subtotal += cost * qty;
    });

    const discount = parseFloat(document.querySelector('[name="discount"]')?.value) || 0;
    const total = Math.max(0, subtotal - discount);

    document.getElementById('subtotalDisplay').innerText = formatMoney(subtotal);
    document.getElementById('totalDisplay').innerText = formatMoney(total);

    // Auto-fill amount paid (user can still change)
    const amountInput = document.querySelector('[name="amount_paid"]');
    if (amountInput && !amountInput.dataset.manual) {
        amountInput.value = total;
    }

    return { subtotal, discount, total };
}

document.querySelector('[name="amount_paid"]')?.addEventListener('input', function () {
    this.dataset.manual = '1';
});

document.querySelector('[name="discount"]')?.addEventListener('input', calcTotals);

document.getElementById('addRow').addEventListener('click', function () {
    const html = `
        <div class="row g-2 item-row mb-2">
            <div class="col-md-5">
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
                    <option value="">— Select product —</option>
                    ${productsOptions}
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="items[${rowIndex}][unit_cost]"
                       class="form-control unit-cost" placeholder="Unit cost" required min="0">
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${rowIndex}][quantity]"
                       class="form-control qty" value="1" required min="1">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">×</button>
            </div>
        </div>`;
    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
    rowIndex++;
    bindEvents();
    calcTotals();
});

function bindEvents() {
    document.querySelectorAll('.product-select').forEach(select => {
        select.onchange = function () {
            const cost = this.options[this.selectedIndex].dataset.cost || 0;
            this.closest('.item-row').querySelector('.unit-cost').value = cost;
            calcTotals();
        };
    });

    document.querySelectorAll('.unit-cost, .qty').forEach(input => {
        input.oninput = calcTotals;
    });

    document.querySelectorAll('.remove-row').forEach(btn => {
        btn.onclick = function () {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                this.closest('.item-row').remove();
                calcTotals();
            }
        };
    });
}

// Submit validation + SweetAlert
document.getElementById('purchaseForm').addEventListener('submit', function (e) {
    const { total } = calcTotals();
    const paymentMethod = document.getElementById('payment_method').value;
    const amountPaid = parseFloat(document.querySelector('[name="amount_paid"]').value) || 0;
    const supplierId = document.querySelector('[name="supplier_id"]').value;

    if (total <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Invalid total',
            text: 'Add at least one item with cost and quantity.',
            confirmButtonColor: '#2563eb'
        });
        return;
    }

    if (paymentMethod === 'credit' && !supplierId) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Supplier required',
            text: 'Please select a supplier for credit purchases.',
            confirmButtonColor: '#2563eb'
        });
        return;
    }

    if (['cash', 'mpesa', 'bank'].includes(paymentMethod) && amountPaid < total) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Insufficient payment',
            text: `Amount paid (TZS ${amountPaid.toLocaleString()}) is less than total (TZS ${total.toLocaleString()}).`,
            confirmButtonColor: '#2563eb'
        });
        return;
    }
});

bindEvents();
calcTotals();
</script>
@endpush