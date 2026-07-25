@extends('layouts.admin')

@section('title', 'Banners')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-muted mb-0">{{ $banners->total() }} banners</p>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-dark btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Banner
        </a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Link</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($banners as $banner)
                        <tr>
                            <td>
                                @if ($banner->image)
                                    <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" class="admin-thumb" style="width:80px;height:48px">
                                @else
                                    <div class="admin-thumb d-flex align-items-center justify-content-center text-muted small" style="width:80px;height:48px">No image</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium">{{ $banner->title }}</div>
                                @if ($banner->subtitle)
                                    <div class="text-muted small">{{ Str::limit($banner->subtitle, 50) }}</div>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $banner->link ?: '—' }}</td>
                            <td>{{ $banner->sort_order }}</td>
                            <td>
                                @if ($banner->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="d-inline" data-confirm="Delete this banner?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No banners yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $banners->links('pagination::bootstrap-5') }}
    </div>
@endsection
