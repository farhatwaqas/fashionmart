@extends('layouts.storefront')

@section('seo_title', 'Checkout — ' . ($storeName ?? 'Fashion Corner'))

@section('content')
    <div class="container py-4">
        <div class="fc-page-header">
            <h1 class="display-font mb-1">Checkout</h1>
            <p class="text-muted mb-0">Cash on Delivery — no online payment required</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('checkout.store') }}">
                    @csrf

                    <div class="mb-4">
                        <h2 class="h6 text-uppercase fw-semibold mb-3">Contact Information</h2>

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <h2 class="h6 text-uppercase fw-semibold mb-3">Delivery Address</h2>

                        <div class="mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes <span class="text-muted">(optional)</span></label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Delivery instructions, etc.">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-fc-primary w-100 w-md-auto">
                        Place Order (COD)
                    </button>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="fc-order-summary">
                    <h2 class="h6 text-uppercase fw-semibold mb-3">Your Order</h2>

                    @foreach ($items as $item)
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                            <img src="{{ $item->product->coverUrl() }}" alt="{{ $item->product->name }}" width="60" height="80" class="object-fit-cover" style="object-fit:cover;background:#f5f5f5">
                            <div class="flex-grow-1">
                                <div class="fw-medium small">{{ $item->product->name }}</div>
                                <div class="text-muted small">Qty: {{ $item->quantity }}</div>
                            </div>
                            <div class="small fw-semibold">PKR {{ number_format($item->line_total, 0) }}</div>
                        </div>
                    @endforeach

                    <div class="fc-order-summary-row">
                        <span>Subtotal</span>
                        <span>PKR {{ number_format($subtotal, 0) }}</span>
                    </div>
                    <div class="fc-order-summary-row fc-order-summary-total">
                        <span>Total</span>
                        <span>PKR {{ number_format($total, 0) }}</span>
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        <i class="bi bi-cash-coin me-1"></i> Pay with cash when your order is delivered.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
