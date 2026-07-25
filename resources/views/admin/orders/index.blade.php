@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <div class="admin-card mb-4">
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small">Search</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Order #, name, phone..." value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
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
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-medium">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->customer_phone }}</td>
                            <td>{{ $order->items_count }}</td>
                            <td>{{ $order->formattedTotal() }}</td>
                            <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                            <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
@endsection
