    <aside class="admin-sidebar" id="adminSidebar">
        <!-- close button -->
        <div class="d-flex d-md-none justify-content-end px-3 pe-0 pt-0">
            <button type="button" id="sidebarClose" 
                    class="btn btn-sm"
                    style="background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; width: 36px; height: 36px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="admin-brand">
            @if(setting('platform_logo'))
                <img src="{{ asset('storage/' . setting('platform_logo')) }}"
                    alt="{{ setting('platform_name', 'DukaFlow') }}"
                    class="brand-logo">
                    {{ setting('platform_name', 'DukaFlow') }}
            @else
                <i class="bi bi-shop"></i> {{ setting('platform_name', 'DukaFlow') }}
            @endif
        </div>
        
        <nav class="nav flex-column px-1">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>

            <div class="section-title">MANAGEMENT</div>
            <a href="{{ route('admin.shops.index') }}"
               class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}">
                <i class="bi bi-building me-2"></i> Shops
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
            <a href="{{ route('admin.plans.index') }}"
                class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                 <i class="bi bi-credit-card me-2"></i> Plans
            </a>

            <!-- System -->
             <div class="section-title">SYSTEM</div>
            <a href="{{ route('admin.activity.index') }}"
                class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text me-2"></i> Activity logs
            </a>
            <a href="{{ route('admin.settings.edit') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
            <hr class="border-secondary mx-3">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="bi bi-arrow-left me-2"></i> Back to {{ setting('platform_name', 'DukaFlow') }}
            </a>
        </nav>
    </aside>