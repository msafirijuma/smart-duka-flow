<nav id="sidebar" class="p-2">
    <!-- close button -->
    <div class="d-flex d-md-none justify-content-end px-3 pe-0 pt-0">
        <button type="button" id="sidebarClose" 
                class="btn btn-sm"
                style="background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; width: 36px; height: 36px;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="brand">
        <i class="bi bi-shop-window"></i>
        <span>DukaFlow</span>
    </div>

    <div class="py-2">
        <div class="section-title">Main</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('pos.index') }}"
           class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i>
            <span>POS / Sell</span>
        </a>

        @if(auth()->user()->isOwner())
            <div class="section-title">Management</div>
            <a href="{{ route('staff.index') }}"
                class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Staff</span>
            </a>
        @endif

        @if(auth()->user()->isManager())
            <div class="section-title">Inventory</div>
            <a href="{{ route('products.index') }}"
            class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>
            <a href="{{ route('categories.index') }}"
            class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>
            <a href="{{ route('suppliers.index') }}"
                class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>Suppliers</span>
            </a>
            <a href="{{ route('purchases.index') }}"
                class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <i class="bi bi-bag-plus"></i>
                <span>Purchases</span>
            </a>
        @endif

        <div class="section-title">Sales & Reports</div>
        <a href="{{ route('sales.index') }}"
           class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Sales History</span>
        </a>
        @if(auth()->user()->isManager())
            <a href="{{ route('reports.index') }}"
            class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                <span>Reports</span>
            </a>
        @endif

        <a href="{{ route('customers.index') }}"
            class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
             <i class="bi bi-people"></i>
             <span>Customers</span>
        </a>

        @if(auth()->user()->isManager())
            <a href="{{ route('expenses.index') }}"
                class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Expenses</span>
            </a>
            <div class="section-title">System</div>
            <a href="{{ route('shops.index') }}"
                class="nav-link {{ request()->routeIs('shops.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>My Shops</span>
            </a>
        @endif

        @if(auth()->user()->isOwner())
            <a href="{{ route('settings.index') }}"
            class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        @endif
    </div>

    <!-- Logout -->
    <div class="mt-auto pt-4 pb-3 border-top border-secondary">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center py-2">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
    </div>
</nav>