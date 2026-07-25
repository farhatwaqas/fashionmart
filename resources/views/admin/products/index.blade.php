@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-muted mb-0">{{ $products->total() }} products</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-dark btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>

    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Name, SKU, slug..." value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->coverUrl() }}" alt="" class="admin-thumb">
                            </td>
                            <td>
                                <div class="fw-medium">{{ $product->name }}</div>
                                <div class="text-muted small">{{ $product->slug }}</div>
                            </td>
                            <td>{{ $product->sku ?: '—' }}</td>
                            <td>{{ $product->category?->name ?: '—' }}</td>
                            <td>{{ $product->formattedPrice() }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td><span class="badge {{ $product->status->badgeClass() }}">{{ $product->status->label() }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" data-confirm="Delete this product permanently?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endsection
