@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-link btn-sm text-muted p-0">&larr; Back to Customers</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">Customer Details</div>
                <div class="admin-card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-4 text-muted">Name</dt>
                        <dd class="col-8">{{ $customer->name }}</dd>
                        <dt class="col-4 text-muted">Phone</dt>
                        <dd class="col-8">{{ $customer->phone }}</dd>
                        <dt class="col-4 text-muted">Email</dt>
                        <dd class="col-8">{{ $customer->email ?: '—' }}</dd>
                        <dt class="col-4 text-muted">City</dt>
                        <dd class="col-8">{{ $customer->city ?: '—' }}</dd>
                        <dt class="col-4 text-muted">Address</dt>
                        <dd class="col-8">{{ $customer->address ?: '—' }}</dd>
                        <dt class="col-4 text-muted">Since</dt>
                        <dd class="col-8">{{ $customer->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">Order History ({{ $customer->orders->count() }})</div>
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customer->orders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->formattedTotal() }}</td>
                                    <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
                                    <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
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
    </div>
@endsection
