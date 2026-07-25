<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/storefront.css') }}" rel="stylesheet">

    <x-seo
        :title="View::hasSection('seo_title') ? trim(View::yieldContent('seo_title')) : null"
        :description="View::hasSection('seo_description') ? trim(View::yieldContent('seo_description')) : null"
        :image="View::hasSection('seo_image') ? trim(View::yieldContent('seo_image')) : null"
        :url="View::hasSection('seo_url') ? trim(View::yieldContent('seo_url')) : null"
    />

    @stack('head')
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="fc-header">
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container">
                <a class="fc-brand navbar-brand me-lg-4" href="{{ route('home') }}">{{ $storeName }}</a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#fcNav" aria-controls="fcNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="fcNav">
                    <ul class="navbar-nav mx-lg-auto gap-lg-4 mb-3 mb-lg-0">
                        <li class="nav-item">
                            <a class="fc-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="fc-nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('cart.index') }}" class="position-relative text-dark" aria-label="Cart">
                            <i class="bi bi-bag fs-5"></i>
                            @if ($cartCount > 0)
                                <span class="fc-cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <x-alert />

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="fc-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="fc-footer-brand">{{ $storeName }}</div>
                    @if (!empty($storeTagline))
                        <p class="small mb-0">{{ $storeTagline }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6 class="text-white text-uppercase small letter-spacing mb-3">Shop</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('shop.index') }}">All Products</a></li>
                        <li class="mb-2"><a href="{{ route('home') }}">New Arrivals</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white text-uppercase small letter-spacing mb-3">Account</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('cart.index') }}">Cart</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4 opacity-25">
            <p class="small mb-0 text-center">&copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/storefront.js') }}"></script>
    @stack('scripts')
</body>
</html>
