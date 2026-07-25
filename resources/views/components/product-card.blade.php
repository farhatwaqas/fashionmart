@props(['product'])

<a href="{{ route('product.show', $product) }}" class="fc-product-card text-decoration-none">
    <div class="fc-product-image-wrap">
        <img
            src="{{ $product->coverUrl() }}"
            alt="{{ $product->name }}"
            class="fc-product-image"
            loading="lazy"
            width="400"
            height="533"
        >
        @if ($product->isOnSale())
            <span class="fc-product-badge fc-product-badge--sale">Sale</span>
        @elseif ($product->hot_selling)
            <span class="fc-product-badge">Hot</span>
        @elseif ($product->featured)
            <span class="fc-product-badge">Featured</span>
        @endif
    </div>
    @if ($product->relationLoaded('category') && $product->category)
        <div class="fc-product-category">{{ $product->category->name }}</div>
    @endif
    <div class="fc-product-name text-dark">{{ $product->name }}</div>
    <div class="fc-product-price">
        {{ $product->formattedPrice() }}
        @if ($product->isOnSale())
            <span class="fc-product-price-old">{{ $product->formattedOldPrice() }}</span>
        @endif
    </div>
</a>
