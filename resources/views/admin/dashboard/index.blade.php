@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Products</div>
                <div class="admin-stat-value">{{ number_format($stats['total_products']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Total Orders</div>
                <div class="admin-stat-value">{{ number_format($stats['total_orders']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Revenue</div>
                <div class="admin-stat-value">PKR {{ number_format($stats['revenue'], 0) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Pending Orders</div>
                <div class="admin-stat-value">{{ number_format($stats['pending_orders']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Completed Orders</div>
                <div class="admin-stat-value">{{ number_format($stats['completed_orders']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="admin-stat-card">
                <div class="admin-stat-label">Low Stock Items</div>
                <div class="admin-stat-value text-danger">{{ number_format($stats['low_stock']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="admin-card">
                <div class="admin-card-header">Latest Orders</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-medium">{{ $order->order_number }}</a>
                                        <div class="text-muted small">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->formattedTotal() }}</td>
                                    <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="admin-card">
                <div class="admin-card-header">Low Stock Products</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $product->name }}</div>
                                        @if ($product->category)
                                            <div class="text-muted small">{{ $product->category->name }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-warning text-dark">{{ $product->quantity }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">All products are well stocked.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
