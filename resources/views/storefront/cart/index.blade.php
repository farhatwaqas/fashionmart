@extends('layouts.storefront')

@section('seo_title', 'Cart — ' . ($storeName ?? 'Fashion Corner'))

@section('content')
    <div class="container py-4">
        <div class="fc-page-header">
            <h1 class="display-font mb-1">Shopping Cart</h1>
            <p class="text-muted mb-0">{{ $items->count() }} item(s)</p>
        </div>

        @if ($items->isNotEmpty())
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table align-middle fc-cart-table">
                            <thead class="small text-uppercase text-muted">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th style="width: 140px">Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="{{ route('product.show', $item->product) }}">
                                                    <img src="{{ $item->product->coverUrl() }}" alt="{{ $item->product->name }}" loading="lazy">
                                                </a>
                                                <div>
                                                    <a href="{{ route('product.show', $item->product) }}" class="fw-medium text-dark text-decoration-none">
                                                        {{ $item->product->name }}
                                                    </a>
                                                    @if ($item->product->sku)
                                                        <div class="small text-muted">SKU: {{ $item->product->sku }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->product->formattedPrice() }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.update') }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                <div class="fc-qty-stepper" data-qty-stepper data-min="0" data-max="{{ min(99, $item->product->quantity) }}">
                                                    <button type="button" class="fc-qty-btn" data-qty-minus aria-label="Decrease">&minus;</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="{{ min(99, $item->product->quantity) }}" class="fc-qty-input" data-qty-input onchange="this.form.submit()">
                                                    <button type="button" class="fc-qty-btn" data-qty-plus aria-label="Increase">&plus;</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="fw-semibold">PKR {{ number_format($item->line_total, 0) }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.destroy') }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                <button type="submit" class="btn btn-link text-muted p-0" aria-label="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('shop.index') }}" class="btn btn-fc-outline btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>

                <div class="col-lg-4">
                    <div class="fc-order-summary">
                        <h2 class="h6 text-uppercase fw-semibold mb-3">Order Summary</h2>
                        <div class="fc-order-summary-row">
                            <span>Subtotal</span>
                            <span>PKR {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="fc-order-summary-row fc-order-summary-total">
                            <span>Total</span>
                            <span>PKR {{ number_format($total, 0) }}</span>
                        </div>
                        <a href="{{ route('checkout.create') }}" class="btn btn-fc-primary w-100 mt-3">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="fc-empty-state">
                <i class="bi bi-bag d-block"></i>
                <h2 class="h5 display-font">Your cart is empty</h2>
                <p class="text-muted">Looks like you haven't added anything yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-fc-primary">Start Shopping</a>
            </div>
        @endif
    </div>
@endsection
