<nav id="sidebar">
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

        <div class="section-title">Sales & Reports</div>
        <a href="{{ route('sales.index') }}"
           class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Sales History</span>
        </a>

        <a href="{{ route('customers.index') }}"
            class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
             <i class="bi bi-people"></i>
             <span>Customers</span>
        </a>

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

        <a href="{{ route('settings.index') }}"
           class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>
</nav>