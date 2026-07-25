@extends('layouts.storefront')

@section('content')
    {{-- Hero slider --}}
    @if ($banners->isNotEmpty())
        <section class="fc-hero fc-hero-fullbleed">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                @if ($banners->count() > 1)
                    <div class="carousel-indicators">
                        @foreach ($banners as $index => $banner)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

                <div class="carousel-inner">
                    @foreach ($banners as $index => $banner)
                        <div @class(['carousel-item', 'active' => $index === 0])>
                            <div class="fc-hero-slide" @if($banner->image) style="background-image: url('{{ $banner->imageUrl() }}')" @endif>
                                <div class="fc-hero-overlay"></div>
                                <div class="container fc-hero-content">
                                    @if ($banner->title)
                                        <h2 class="fc-hero-title">{{ $banner->title }}</h2>
                                    @endif
                                    @if ($banner->subtitle)
                                        <p class="fc-hero-subtitle">{{ $banner->subtitle }}</p>
                                    @endif
                                    @if ($banner->link)
                                        <a href="{{ $banner->link }}" class="btn btn-fc-outline text-white border-white">
                                            {{ $banner->button_text ?: 'Shop Now' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($banners->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </section>
    @else
        <section class="fc-hero fc-hero-fullbleed">
            <div class="fc-hero-placeholder">
                <div class="text-center px-3">
                    <h1 class="display-font mb-2">{{ $storeName }}</h1>
                    @if (!empty($storeTagline))
                        <p class="text-muted">{{ $storeTagline }}</p>
                    @endif
                    <a href="{{ route('shop.index') }}" class="btn btn-fc-primary mt-3">Shop Collection</a>
                </div>
            </div>
        </section>
    @endif

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="fc-section">
            <div class="container">
                <h2 class="fc-section-title display-font">Shop by Category</h2>
                <p class="fc-section-subtitle">Explore our curated collections</p>
                <div class="fc-category-grid">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.category', $category) }}" class="fc-category-card">
                            <div class="fc-category-name">{{ $category->name }}</div>
                            <div class="fc-category-count">{{ $category->products_count }} items</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featured->isNotEmpty())
        <section class="fc-section bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fc-section-title display-font mb-0">Featured</h2>
                        <p class="fc-section-subtitle mb-0">Handpicked for you</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="small text-uppercase fw-semibold d-none d-md-inline">View All</a>
                </div>
                <div class="fc-product-grid">
                    @foreach ($featured as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Hot Selling --}}
    @if ($hotSelling->isNotEmpty())
        <section class="fc-section">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fc-section-title display-font mb-0">Hot Selling</h2>
                        <p class="fc-section-subtitle mb-0">Trending right now</p>
                    </div>
                </div>
                <div class="fc-product-grid">
                    @foreach ($hotSelling as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- New Arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section class="fc-section bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fc-section-title display-font mb-0">New Arrivals</h2>
                        <p class="fc-section-subtitle mb-0">Fresh styles just in</p>
                    </div>
                </div>
                <div class="fc-product-grid">
                    @foreach ($newArrivals as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Recommended --}}
    @if ($recommended->isNotEmpty())
        <section class="fc-section">
            <div class="container">
                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="fc-section-title display-font mb-0">Recommended</h2>
                        <p class="fc-section-subtitle mb-0">You might also love</p>
                    </div>
                </div>
                <div class="fc-product-grid">
                    @foreach ($recommended as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
