@extends('layouts.storefront')

@section('seo_title', ($product->meta_title ?: $product->name) . ' — ' . ($storeName ?? 'Fashion Corner'))
@section('seo_description', $product->meta_description ?: Str::limit(strip_tags($product->short_description ?? $product->description ?? ''), 160))
@section('seo_image', $product->coverUrl())

@section('content')
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
                @if ($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5 mb-5">
            {{-- Gallery --}}
            <div class="col-lg-6">
                @php
                    $images = $product->images->isNotEmpty() ? $product->images : collect();
                    $mainImage = $images->first();
                @endphp

                <div class="fc-gallery-main">
                    <img
                        src="{{ $mainImage ? $mainImage->url() : $product->coverUrl() }}"
                        alt="{{ $mainImage?->alt ?: $product->name }}"
                        id="galleryMainImage"
                    >
                </div>

                @if ($images->count() > 1)
                    <div class="fc-gallery-thumbs">
                        @foreach ($images as $index => $image)
                            <button
                                type="button"
                                class="fc-gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                data-src="{{ $image->url() }}"
                                data-alt="{{ $image->alt ?: $product->name }}"
                            >
                                <img src="{{ $image->thumbnailUrl() }}" alt="{{ $image->alt ?: $product->name }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Product info --}}
            <div class="col-lg-6">
                @if ($product->category)
                    <div class="text-uppercase small text-muted mb-2">{{ $product->category->name }}</div>
                @endif

                <h1 class="display-font mb-3">{{ $product->name }}</h1>

                @if ($product->sku)
                    <p class="text-muted small mb-3">SKU: {{ $product->sku }}</p>
                @endif

                <div class="mb-4">
                    <span class="fs-4 fw-semibold">{{ $product->formattedPrice() }}</span>
                    @if ($product->isOnSale())
                        <span class="fc-product-price-old fs-5">{{ $product->formattedOldPrice() }}</span>
                        <span class="badge bg-dark ms-2">Sale</span>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="text-muted mb-4">{{ $product->short_description }}</p>
                @endif

                @if ($product->inStock())
                    <form method="POST" action="{{ route('cart.store') }}" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                            <div class="fc-qty-stepper" data-qty-stepper data-min="1" data-max="{{ min(99, $product->quantity) }}">
                                <button type="button" class="fc-qty-btn" data-qty-minus aria-label="Decrease quantity">&minus;</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ min(99, $product->quantity) }}" class="fc-qty-input" data-qty-input>
                                <button type="button" class="fc-qty-btn" data-qty-plus aria-label="Increase quantity">&plus;</button>
                            </div>
                            <button type="submit" class="btn btn-fc-primary flex-grow-1 flex-sm-grow-0">
                                Add to Cart
                            </button>
                        </div>
                        <p class="small text-muted mb-0">{{ $product->quantity }} in stock</p>
                    </form>
                @else
                    <div class="alert alert-secondary mb-4">This product is currently out of stock.</div>
                @endif

                @if ($product->description)
                    <div class="border-top pt-4 mt-4">
                        <h2 class="h6 text-uppercase fw-semibold mb-3">Description</h2>
                        <div class="text-muted">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="border-top pt-5">
                <h2 class="fc-section-title display-font mb-4">You May Also Like</h2>
                <div class="fc-product-grid">
                    @foreach ($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
