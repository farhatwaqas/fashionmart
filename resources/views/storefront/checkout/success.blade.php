@extends('layouts.storefront')

@section('seo_title', 'Order Confirmed — ' . ($storeName ?? 'Fashion Corner'))

@section('content')
    <div class="container py-5">
        <div class="text-center mx-auto" style="max-width: 560px">
            <div class="mb-4">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem"></i>
            </div>

            <h1 class="display-font mb-2">Thank You!</h1>
            <p class="text-muted mb-4">Your order has been placed successfully. We'll contact you shortly to confirm delivery.</p>

            <div class="fc-order-summary text-start mb-4">
                <div class="fc-order-summary-row">
                    <span class="text-muted">Order Number</span>
                    <span class="fw-semibold">{{ $order->order_number }}</span>
                </div>
                <div class="fc-order-summary-row">
                    <span class="text-muted">Total</span>
                    <span class="fw-semibold">{{ $order->formattedTotal() }}</span>
                </div>
                <div class="fc-order-summary-row">
                    <span class="text-muted">Payment</span>
                    <span>Cash on Delivery</span>
                </div>
                <div class="fc-order-summary-row">
                    <span class="text-muted">Status</span>
                    <span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
                </div>
            </div>

            @if ($order->items->isNotEmpty())
                <div class="text-start mb-4">
                    <h2 class="h6 text-uppercase fw-semibold mb-3">Order Items</h2>
                    @foreach ($order->items as $item)
                        <div class="d-flex justify-content-between small py-2 border-bottom">
                            <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                            <span>PKR {{ number_format($item->line_total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <a href="{{ route('shop.index') }}" class="btn btn-fc-primary">Continue Shopping</a>
                <a href="{{ route('home') }}" class="btn btn-fc-outline">Back to Home</a>
            </div>
        </div>
    </div>
@endsection
