<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ $storeName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body class="admin-body">
    <div class="admin-sidebar-backdrop"></div>

    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-brand">
                @if (!empty($storeLogoUrl))
                    <img src="{{ $storeLogoUrl }}" alt="{{ $storeName }}" class="admin-sidebar-logo mb-1">
                @endif
                {{ $storeName }}
                <small>Admin Panel</small>
            </div>

            <ul class="admin-nav">
                <li class="admin-nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Orders
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.customers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Customers
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.banners.index') }}" class="admin-nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <i class="bi bi-images"></i> Banners
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="{{ route('admin.backup.index') }}" class="admin-nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                        <i class="bi bi-cloud-download"></i> Backup
                    </a>
                </li>

                <li class="admin-nav-divider"></li>

                <li class="admin-nav-item">
                    <a href="{{ route('home') }}" class="admin-nav-link" target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> View Store
                    </a>
                </li>
                <li class="admin-nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="admin-nav-link admin-nav-link--logout border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="admin-sidebar-toggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="admin-page-title mb-0">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="text-muted small">
                    {{ auth()->user()->name ?? auth()->user()->email }}
                </div>
            </header>

            <div class="admin-content">
                <x-alert />
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
