<div class="top-navbar">
    <div class="d-flex align-items-center gap-3">
        {{-- Mobile toggle --}}
        <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        {{-- Current Shop --}}
        @php
            $currentShop = auth()->user()?->currentShop();
        @endphp

        @if($currentShop)
            <span class="shop-badge">
                <i class="bi bi-shop"></i>
                {{ $currentShop->name }}
            </span>
        @else
            <span class="text-muted small ms-1">Choose or create a new shop</span>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        {{-- Quick POS button --}}
        <a href="{{ route('pos.index') }}"
           class="btn btn-primary btn-sm d-none d-md-inline-flex align-items-center gap-1">
            <i class="bi bi-cart-plus"></i> New Sale
        </a>

        {{-- User Dropdown --}}
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark"
                id="userDropdown"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                role="button">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; font-weight: 600;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="ms-2 d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('shops.index') }}">
                        <i class="bi bi-building me-2"></i> My Shops
                    </a>
                </li>
                @if(auth()->user()->isOwner())
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </li>
                @endif
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>