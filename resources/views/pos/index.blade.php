@extends('layouts.app')

@section('title', 'POS - New Sale')

@section('content')
<div class="row g-3">
    <!-- LEFT: Products -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0"
                           placeholder="Search name, barcode or SKU... (Press Enter)" autofocus>
                </div>
            </div>
            <div class="card-body p-2" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-2" id="productsGrid">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4 col-xl-3 product-item"
                             data-id="{{ $product->id }}"
                             data-name="{{ $product->name }}"
                             data-price="{{ $product->selling_price }}"
                             data-stock="{{ $product->stock_quantity }}"
                             data-unit="{{ $product->unit }}">
                            <div class="card product-card h-100 border shadow-sm" role="button">
                                <div class="card-body p-2 text-center">
                                    <div class="fw-semibold small text-truncate">{{ $product->name }}</div>
                                    <div class="text-primary fw-bold">TZS {{ number_format($product->selling_price, 0) }}</div>
                                    <small class="text-muted">{{ $product->stock_quantity }} {{ $product->unit }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="noProducts" class="text-center text-muted py-5 d-none">
                    No products found
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Cart -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-cart3"></i> Current Sale</span>
                <button class="btn btn-sm btn-light" id="clearCart">Clear</button>
            </div>

            <div class="card-body p-0">
                <div id="cartItems" style="max-height: 320px; overflow-y: auto;">
                    <div class="text-center text-muted py-5" id="emptyCart">
                        <i class="bi bi-cart display-6"></i>
                        <p class="mb-0 mt-2">Cart is empty</p>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between mb-1">
                    <span>Subtotal</span>
                    <span id="subtotal">TZS 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>
                    <input type="number" id="discount" class="form-control form-control-sm w-50 text-end" value="0" min="0">
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mb-3">
                    <span>Total</span>
                    <span id="total" class="text-primary">TZS 0</span>
                </div>

                <div class="mb-2">
                    <label class="form-label small mb-1">Customer (optional)</label>
                    <select id="customer_id" class="form-select form-select-sm">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small mb-1">Payment Method</label>
                    <select id="payment_method" class="form-select form-select-sm">
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank">Bank</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small mb-1">Amount Paid</label>
                    <input type="number" id="amount_paid" class="form-control" value="0" min="0">
                </div>

                <button class="btn btn-success w-100 btn-lg" id="btnCheckout" disabled>
                    <i class="bi bi-check2-circle"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-card:hover {
        border-color: #2563eb !important;
        background: #f0f7ff;
    }
    .cart-item {
        border-bottom: 1px solid #eee;
        padding: 0.6rem 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    let cart = [];

    const productsGrid = document.getElementById('productsGrid');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const searchInput = document.getElementById('searchInput');

    // Add product to cart
    function addToCart(id, name, price, stock, unit) {
        id = parseInt(id);
        price = parseFloat(price);
        stock = parseInt(stock);

        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.quantity + 1 > stock) {
                alert('Not enough stock');
                return;
            }
            existing.quantity += 1;
        } else {
            cart.push({ id, name, price, quantity: 1, stock, unit });
        }
        renderCart();
    }

    // Click on product card
    document.querySelectorAll('.product-item').forEach(el => {
        el.addEventListener('click', function () {
            addToCart(
                this.dataset.id,
                this.dataset.name,
                this.dataset.price,
                this.dataset.stock,
                this.dataset.unit
            );
        });
    });

    // Render cart
    function renderCart() {
        if (cart.length === 0) {
            cartItems.innerHTML = `<div class="text-center text-muted py-5" id="emptyCart">
                <i class="bi bi-cart display-6"></i>
                <p class="mb-0 mt-2">Cart is empty</p>
            </div>`;
            document.getElementById('btnCheckout').disabled = true;
            updateTotals();
            return;
        }

        let html = '';
        cart.forEach((item, index) => {
            html += `
                <div class="cart-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold small">${item.name}</div>
                        <div class="text-muted small">TZS ${Number(item.price).toLocaleString()} × 
                            <input type="number" value="${item.quantity}" min="1" max="${item.stock}"
                                   class="form-control form-control-sm d-inline-block qty-input"
                                   style="width:60px" data-index="${index}">
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">TZS ${Number(item.price * item.quantity).toLocaleString()}</div>
                        <button class="btn btn-sm btn-link text-danger p-0 remove-item" data-index="${index}">Remove</button>
                    </div>
                </div>
            `;
        });
        cartItems.innerHTML = html;
        document.getElementById('btnCheckout').disabled = false;
        updateTotals();

        // Qty change
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function () {
                const idx = this.dataset.index;
                let val = parseInt(this.value) || 1;
                if (val > cart[idx].stock) val = cart[idx].stock;
                if (val < 1) val = 1;
                cart[idx].quantity = val;
                renderCart();
            });
        });

        // Remove
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function () {
                cart.splice(this.dataset.index, 1);
                renderCart();
            });
        });
    }

    function updateTotals() {
        const subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const total = Math.max(0, subtotal - discount);

        document.getElementById('subtotal').innerText = 'TZS ' + subtotal.toLocaleString();
        document.getElementById('total').innerText = 'TZS ' + total.toLocaleString();
        document.getElementById('amount_paid').value = total;
    }

    document.getElementById('discount').addEventListener('input', updateTotals);

    document.getElementById('clearCart').addEventListener('click', () => {
        cart = [];
        renderCart();
    });

    // Checkout
    document.getElementById('btnCheckout').addEventListener('click', function () {
        if (cart.length === 0) return;

        const payload = {
            items: cart.map(i => ({
                id: i.id,
                quantity: i.quantity,
                price: i.price
            })),
            payment_method: document.getElementById('payment_method').value,
            amount_paid: parseFloat(document.getElementById('amount_paid').value) || 0,
            discount: parseFloat(document.getElementById('discount').value) || 0,
            customer_id: document.getElementById('customer_id').value || null,
            _token: '{{ csrf_token() }}'
        };

        this.disabled = true;
        this.innerHTML = 'Processing...';

        fetch('{{ route('pos.checkout') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Sale completed!\nInvoice: ${data.invoice}\nChange: TZS ${Number(data.change).toLocaleString()}`);
                cart = [];
                renderCart();
                // Optional: location.reload();
            } else {
                alert(data.message || 'Error completing sale');
            }
        })
        .catch(err => {
            alert('Something went wrong');
            console.error(err);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check2-circle"></i> Complete Sale';
        });
    });

    // Simple search (client side for now)
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.product-item').forEach(el => {
            const name = el.dataset.name.toLowerCase();
            const show = name.includes(q);
            el.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('noProducts').classList.toggle('d-none', visible > 0);
    });
</script>
@endpush