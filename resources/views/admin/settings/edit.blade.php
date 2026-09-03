@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="admin-card-header">Store Information</div>
                    <div class="admin-card-body">
                        <div class="mb-4">
                            <label for="store_logo" class="form-label">Store Logo</label>
                            <p class="text-muted small mb-3">Shown beside the brand title in the header and footer. PNG, JPG, WEBP or SVG — max 2 MB.</p>

                            @if (!empty($logoUrl))
                                <div class="d-flex align-items-center gap-3 mb-3 p-3 border rounded bg-light">
                                    <img src="{{ $logoUrl }}" alt="Current logo" style="height: 48px; width: auto; max-width: 96px; object-fit: contain;">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="remove_store_logo" value="1" id="remove_store_logo" @checked(old('remove_store_logo'))>
                                        <label class="form-check-label" for="remove_store_logo">Remove current logo</label>
                                    </div>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('store_logo') is-invalid @enderror" id="store_logo" name="store_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                            @error('store_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" required>
                            @error('store_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="store_tagline" class="form-label">Tagline</label>
                            <input type="text" class="form-control @error('store_tagline') is-invalid @enderror" id="store_tagline" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline']) }}">
                            @error('store_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="store_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('store_phone') is-invalid @enderror" id="store_phone" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}">
                                @error('store_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="store_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('store_email') is-invalid @enderror" id="store_email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}">
                                @error('store_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="store_address" class="form-label">Address</label>
                            <textarea class="form-control @error('store_address') is-invalid @enderror" id="store_address" name="store_address" rows="2">{{ old('store_address', $settings['store_address']) }}</textarea>
                            @error('store_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">SEO Defaults</div>
                    <div class="admin-card-body">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Default Meta Title</label>
                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $settings['meta_title']) }}">
                            @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Default Meta Description</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="2">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                            @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <div class="admin-card-header">Commerce</div>
                    <div class="admin-card-body">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" value="{{ old('currency', $settings['currency'] ?? 'PKR') }}" required>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                            <input type="number" min="0" class="form-control @error('low_stock_threshold') is-invalid @enderror" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}">
                            @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="free_shipping_note" class="form-label">Free Shipping Note</label>
                            <input type="text" class="form-control @error('free_shipping_note') is-invalid @enderror" id="free_shipping_note" name="free_shipping_note" value="{{ old('free_shipping_note', $settings['free_shipping_note']) }}">
                            @error('free_shipping_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Save Settings</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
