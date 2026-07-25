@php
    $banner = $banner ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner?->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="subtitle" class="form-label">Subtitle</label>
                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}">
                    @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="link" class="form-label">Link URL</label>
                        <input type="text" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $banner?->link) }}" placeholder="/shop or https://...">
                        @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="button_text" class="form-label">Button Text</label>
                        <input type="text" class="form-control @error('button_text') is-invalid @enderror" id="button_text" name="button_text" value="{{ old('button_text', $banner?->button_text) }}" placeholder="Shop Now">
                        @error('button_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort Order</label>
                    <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-body">
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn btn-dark w-100">
                    {{ $banner ? 'Update Banner' : 'Create Banner' }}
                </button>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-body">
                <label for="image" class="form-label">Banner Image @unless($banner)<span class="text-danger">*</span>@endunless</label>

                @if ($banner?->image)
                    <div class="mb-3">
                        <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" class="img-fluid mb-2" style="max-height:160px;width:100%;object-fit:cover">
                        <p class="small text-muted mb-0">Upload a new image to replace.</p>
                    </div>
                @else
                    <div class="mb-3 p-4 text-center text-muted small border" style="background:#f5f5f5">No image uploaded</div>
                @endif

                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" @unless($banner) required @endunless>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
