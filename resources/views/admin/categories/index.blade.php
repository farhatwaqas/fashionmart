@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-muted mb-0">{{ $categories->total() }} categories</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-dark btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </a>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="fw-medium">{{ $category->name }}</td>
                            <td class="text-muted">{{ $category->slug }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" data-confirm="Delete this category?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
@endsection
