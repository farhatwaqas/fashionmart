@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
    </form>

    @if ($product->images->isNotEmpty())
        <div class="admin-card mt-4">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <span>Product Images</span>
                <small class="text-muted">Drag to reorder</small>
            </div>
            <div class="admin-card-body">
                <form id="admin-image-reorder-form" method="POST" action="{{ route('admin.products.images.reorder', $product) }}">
                    @csrf
                    <div data-order-inputs>
                        @foreach ($product->images as $image)
                            <input type="hidden" name="order[]" value="{{ $image->id }}">
                        @endforeach
                    </div>
                </form>

                <ul class="admin-image-list" id="admin-image-reorder-list">
                    @foreach ($product->images as $image)
                        <li class="admin-image-item" data-image-id="{{ $image->id }}">
                            <img src="{{ $image->thumbnailUrl() }}" alt="{{ $image->alt ?: $product->name }}">
                            @if ($image->is_cover)
                                <span class="cover-badge">Cover</span>
                            @endif
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" data-confirm="Remove this image?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-btn" aria-label="Remove">&times;</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
