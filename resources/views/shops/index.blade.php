@extends('layouts.app')

@section('title', 'My Shops')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">My Shops</h4>
    @if(auth()->user()->isOwner())
        <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> New Shop
        </a>
    @endif
</div>

<!-- Search Bar Section -->
<div class="row mb-4">
    <div class="col-md-5 col-12">
        <div class="position-relative">
            <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                   id="shopSearchInput" 
                   class="form-control bg-dark text-white border-secondary ps-5 pe-5" 
                   placeholder="Search shop by name or address..." 
                   autocomplete="off">
            <!-- Clear (X) Button -->
            <button type="button" 
                    id="clearShopSearchBtn" 
                    class="btn-close btn-close-white position-absolute top-50 end-0 translate-middle-y me-3 d-none" 
                    aria-label="Clear search">
            </button>
        </div>
    </div>
</div>

<div class="row g-3" id="shopsContainer">
    @forelse($shops as $shop)
        <div class="col-md-6 col-lg-4 shop-item">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0 shop-name">{{ $shop->name }}</h5>
                        @if(session('current_shop_id') == $shop->id)
                            <span class="badge bg-success">Current</span>
                        @endif
                    </div>

                    <p class="text-muted small mb-3 shop-address">
                        {{ $shop->address ?? 'No address' }}
                    </p>

                    <div class="d-flex gap-2">
                        @if(session('current_shop_id') != $shop->id)
                            <form action="{{ route('shops.switch', $shop) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    Switch to this shop
                                </button>
                            </form>
                        @else
                            <button class="btn btn-success btn-sm" disabled>Active</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                You don’t have any shop yet.
                <a href="{{ route('shops.create') }}">Create your first shop</a>
            </div>
        </div>
    @endforelse

    <!-- Dynamic Alert For Empty Live Search Results -->
    <div class="col-12 d-none" id="noShopFoundMsg">
        <div class="alert alert-warning text-center py-3 m-0">
            No shops matched your search query.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('shopSearchInput');
    const clearBtn = document.getElementById('clearShopSearchBtn');
    const shopItems = document.querySelectorAll('.shop-item');
    const noResultMsg = document.getElementById('noShopFoundMsg');

    if (searchInput) {
        function filterShops() {
            const filter = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            // Toggle clear button (X) visibility
            if (filter.length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }

            // Filter shop grid cards
            shopItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filter)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            // Show/Hide "No shops found" alert if matching results are zero
            if (visibleCount === 0 && shopItems.length > 0) {
                noResultMsg?.classList.remove('d-none');
            } else {
                noResultMsg?.classList.add('d-none');
            }
        }

        // Live filtering event listener
        searchInput.addEventListener('keyup', filterShops);

        // Clear button event listener
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                filterShops();
                searchInput.focus();
            });
        }
    }
});
</script>
@endpush