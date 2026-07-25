@extends('layouts.storefront')

@section('seo_title', 'Shop — ' . ($storeName ?? 'Fashion Corner'))
@section('seo_description', 'Browse our full collection of fashion at ' . ($storeName ?? 'Fashion Corner') . '.')

@section('content')
    <div class="container py-4">
        <div class="fc-page-header">
            <h1 class="display-font mb-1">Shop</h1>
            <p class="text-muted mb-0">Discover our full collection</p>
        </div>

        <div class="fc-shop-layout">
            {{-- Filters sidebar --}}
            <aside>
                <form method="GET" action="{{ route('shop.index') }}" class="fc-filter-panel">
                    <div class="fc-filter-title">Search</div>
                    <div class="mb-4">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('q') }}">
                    </div>

                    <div class="fc-filter-title">Category</div>
                    <div class="mb-4">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fc-filter-title">Price Range</div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}" min="0">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                    <div class="fc-filter-title">Sort By</div>
                    <div class="mb-4">
                        <select name="sort" class="form-select form-select-sm">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                            <option value="name" @selected(request('sort') === 'name')>Name</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-dark btn-sm w-100 text-uppercase">Apply Filters</button>
                    @if (request()->hasAny(['q', 'category', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('shop.index') }}" class="btn btn-link btn-sm w-100 mt-2 text-muted">Clear All</a>
                    @endif
                </form>
            </aside>

            {{-- Products grid --}}
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted small mb-0">{{ $products->total() }} products found</p>
                </div>

                @if ($products->isNotEmpty())
                    <div class="fc-product-grid mb-4">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="fc-empty-state">
                        <i class="bi bi-search d-block"></i>
                        <h3 class="h5 display-font">No products found</h3>
                        <p class="text-muted">Try adjusting your filters or search term.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-fc-primary">View All Products</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
