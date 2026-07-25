@php
    $product = $product ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-body">
                <div class="admin-form-section">
                    <div class="admin-form-section-title">Basic Information</div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product?->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product?->slug) }}" placeholder="Auto-generated if empty">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product?->sku) }}">
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2">{{ old('short_description', $product?->short_description) }}</textarea>
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description', $product?->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="admin-form-section">
                    <div class="admin-form-section-title">Pricing & Inventory</div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product?->price) }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="old_price" class="form-label">Old Price</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('old_price') is-invalid @enderror" id="old_price" name="old_price" value="{{ old('old_price', $product?->old_price) }}">
                            @error('old_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $product?->quantity ?? 0) }}" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="admin-form-section">
                    <div class="admin-form-section-title">SEO</div>

                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $product?->meta_title) }}">
                        @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="2">{{ old('meta_description', $product?->meta_description) }}</textarea>
                        @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-body">
                <div class="admin-form-section mb-0">
                    <div class="admin-form-section-title">Publish</div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $product?->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" @checked(old('featured', $product?->featured))>
                        <label class="form-check-label" for="featured">Featured</label>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="hot_selling" name="hot_selling" value="1" @checked(old('hot_selling', $product?->hot_selling))>
                        <label class="form-check-label" for="hot_selling">Hot Selling</label>
                    </div>
                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="recommended" name="recommended" value="1" @checked(old('recommended', $product?->recommended))>
                        <label class="form-check-label" for="recommended">Recommended</label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        {{ $product ? 'Update Product' : 'Create Product' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-body">
                <div class="admin-form-section-title">Images</div>
                <div class="mb-3">
                    <label for="images" class="form-label">Upload Images</label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple>
                    @error('images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @error('images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">JPG, PNG, WebP. Max 6MB each. Up to 20 images.</div>
                </div>
            </div>
        </div>
    </div>
</div>
